<?php declare(strict_types=1);

/*
 * This file is part of the bk2k/configuration-installer.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace BK2K\ConfigurationInstallerTest\Installer;

use BK2K\ConfigurationInstaller\Installer\ConfigurationInstaller;
use BK2K\ConfigurationInstallerTest\TestCase;
use Composer\Composer;
use Composer\Config;
use Composer\Downloader\DownloadManager;
use Composer\Installer\InstallationManager;
use Composer\Installer\InstallerInterface;
use Composer\IO\IOInterface;
use Composer\Package\Package;
use Composer\Package\RootPackage;
use Composer\Package\RootPackageInterface;
use Composer\Repository\InstalledRepositoryInterface;
use Composer\Util\Filesystem;

class InstallerTestCase extends TestCase
{
    /**
     * @string
     */
    protected $previousDirectory;

    /**
     * @string
     */
    protected $rootDirectory;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var Composer
     */
    protected $composer;

    /**
     * @var InstalledRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $repository;

    /**
     * @var IOInterface
     */
    protected $io;

    protected function setUp(): void
    {
        $this->previousDirectory = getcwd();
        $this->rootDirectory = TestCase::getUniqueTmpDirectory();
        chdir($this->rootDirectory);

        $this->composer = new Composer();
        $this->composer->setConfig($this->createComposerConfig());

        /** @var InstallationManager */
        $installationManager = $this->createMock(InstallationManager::class);
        $this->composer->setInstallationManager($installationManager);

        /** @var DownloadManager */
        $downloadManager = $this->getMockBuilder(DownloadManager::class)->disableOriginalConstructor()->getMock();
        $this->composer->setDownloadManager($downloadManager);

        /** @var RootPackage|\PHPUnit_Framework_MockObject_MockObject $package */
        $package = $this->createMock(RootPackageInterface::class);
        $this->composer->setPackage($package);

        $this->repository = $this->createMock(InstalledRepositoryInterface::class);
        $this->io = $this->createMock(IOInterface::class);
    }

    protected function tearDown(): void
    {
        chdir($this->previousDirectory);
        if (is_dir($this->rootDirectory)) {
            $filesystem = new Filesystem;
            $filesystem->removeDirectory($this->rootDirectory);
        }
    }

    protected function createPackageMock(string $prettyName, $type = 'library', array $extra = []): Package
    {
        /** @var Package|\PHPUnit_Framework_MockObject_MockObject $package */
        $package = $this->getMockBuilder(Package::class)
            ->setConstructorArgs([md5(uniqid()), 'dev-develop', 'dev-develop'])
            ->getMock()
        ;
        $package
            ->expects($this->any())
            ->method('getType')
            ->willReturn($type)
        ;
        $package
            ->expects($this->any())
            ->method('getPrettyName')
            ->willReturn($prettyName)
        ;
        $package
            ->expects($this->any())
            ->method('getPrettyVersion')
            ->willReturn('dev-develop')
        ;
        $package
            ->expects($this->any())
            ->method('getVersion')
            ->willReturn('dev-develop')
        ;
        $package
            ->expects($this->any())
            ->method('getInstallationSource')
            ->willReturn('source')
        ;
        $package
            ->expects($this->any())
            ->method('getExtra')
            ->willReturn($extra)
        ;

        return $package;
    }

    protected function createPackageMockWithConfigurationFiles(InstallerInterface $installer, array $files = []): Package
    {
        $package = $this->createPackageMock(
            'bk2k/configuration-test',
            'project-configuration'
        );

        $packageInstallationPath = $installer->getInstallPath($package);
        $filesystem = new Filesystem;
        $filesystem->ensureDirectoryExists($packageInstallationPath);

        if (count($files) > 0) {
            foreach ($files as $filename => $fileContent) {
                $path = $filesystem->normalizePath($packageInstallationPath . DIRECTORY_SEPARATOR . $filename);
                $filesystem->ensureDirectoryExists(dirname($path));
                file_put_contents($path, $fileContent);
            }
        }

        return $package;
    }

    protected function createConfigurationInstaller(): InstallerInterface
    {
        return new ConfigurationInstaller($this->io, $this->composer);
    }

    protected function createComposerConfig(): Config
    {
        $config = new Config();
        $config->merge([
            'config' => [
                'vendor-dir' => $this->rootDirectory . DIRECTORY_SEPARATOR . 'vendor',
                'bin-dir' => $this->rootDirectory . DIRECTORY_SEPARATOR . 'bin',
            ],
            'repositories' => ['packagist' => false],
        ]);

        return $config;
    }
}
