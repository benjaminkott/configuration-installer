<?php declare(strict_types=1);

/*
 * This file is part of the bk2k/configuration-installer.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace BK2K\ConfigurationInstaller\Installer;

use BK2K\ConfigurationInstaller\Configuration\File;
use BK2K\ConfigurationInstaller\Configuration\GitignoreEntry;
use BK2K\ConfigurationInstaller\Configuration\InstallerConfiguration;
use BK2K\ConfigurationInstaller\Factory\ConfigurationFactory;
use BK2K\ConfigurationInstaller\Handler;
use BK2K\ConfigurationInstaller\Service\GitignoreService;
use Composer\Composer;
use Composer\Installer\BinaryInstaller;
use Composer\Installer\LibraryInstaller;
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Composer\Util\Filesystem;

class ConfigurationInstaller extends LibraryInstaller
{
    private $handler;
    private $cache;

    public function __construct(
        IOInterface $io,
        Composer $composer,
        $type = 'project-configuration',
        ?Filesystem $filesystem = null,
        ?BinaryInstaller $binaryInstaller = null
    ) {
        parent::__construct($io, $composer, $type, $filesystem, $binaryInstaller);
        $this->handler = [
            'files' => Handler\FileHandler::class,
            'gitignore' => Handler\GitignoreHandler::class
        ];

        // Force early autoloading of needed classes to
        // keep them in memory during uninstallation of
        // the last configuration package.
        class_exists(File::class, true);
        class_exists(GitignoreEntry::class, true);
        class_exists(InstallerConfiguration::class, true);
        class_exists(ConfigurationFactory::class, true);
        class_exists(Handler\FileHandler::class, true);
        class_exists(Handler\GitignoreHandler::class, true);
        class_exists(GitignoreService::class, true);
    }

    public function getHandler($key)
    {
        if (!isset($this->handler[$key])) {
            throw new \InvalidArgumentException(sprintf('Unknown handler "%s".', $key));
        }
        if (isset($this->cache[$key])) {
            return $this->cache[$key];
        }
        $class = $this->handler[$key];
        return $this->cache[$key] = new $class($this->composer, $this->io, $this->filesystem);
    }

    protected function installCode(PackageInterface $package)
    {
        parent::installCode($package);
        $this->processInstallPackage($package);
    }

    protected function updateCode(PackageInterface $initial, PackageInterface $target)
    {
        $this->processRemovePackage($initial);
        parent::updateCode($initial, $target);
        $this->processInstallPackage($target);
    }

    protected function removeCode(PackageInterface $package)
    {
        $this->processRemovePackage($package);
        parent::removeCode($package);
    }

    public function processInstallPackage(PackageInterface $package)
    {
        $installerConfiguration = $this->getInstallerConfiguration($package);
        foreach (array_keys($this->handler) as $handler) {
            $this->getHandler($handler)->install($installerConfiguration, $package);
        }
    }

    public function processRemovePackage(PackageInterface $package)
    {
        $installerConfiguration = $this->getInstallerConfiguration($package);
        foreach (array_keys($this->handler) as $handler) {
            $this->getHandler($handler)->remove($installerConfiguration, $package);
        }
    }

    public function getInstallerConfiguration(PackageInterface $package): InstallerConfiguration
    {
        $directory = $this->getInstallPath($package);
        $configurationManifestPath = $this->filesystem->normalizePath($directory . DIRECTORY_SEPARATOR . 'manifest.json');
        $importedConfiguration = [];
        if (\file_exists($configurationManifestPath)) {
            $importedConfiguration = json_decode(file_get_contents($configurationManifestPath), true, 512, JSON_THROW_ON_ERROR);
        }

        return ConfigurationFactory::fromArray($importedConfiguration);
    }
}
