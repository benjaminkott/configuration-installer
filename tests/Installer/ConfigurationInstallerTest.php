<?php declare(strict_types=1);

/*
 * This file is part of the bk2k/configuration-installer.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace BK2K\ConfigurationInstallerTest\Installer;

use BK2K\ConfigurationInstaller\Service\GitignoreService;

class ConfigurationInstallerTest extends InstallerTestCase
{
    public function testConfigurationInstallerSupportConfigurationType()
    {
        $installer = $this->createConfigurationInstaller();
        $this->assertTrue($installer->supports('project-configuration'));
    }

    public function testConfigurationInstallerSupportConfigurationTypeFromPackage()
    {
        $installer = $this->createConfigurationInstaller();
        $package = $this->createPackageMock('bk2k/configuration-test', 'project-configuration');
        $this->assertTrue($installer->supports($package->getType()));
    }

    public function testConfigurationInstallerIsNotSupportedWithOtherPackageType()
    {
        $installer = $this->createConfigurationInstaller();
        $package = $this->createPackageMock('bk2k/configuration-test', 'configuration');
        $this->assertFalse($installer->supports($package->getType()));
    }

    /**
     * @dataProvider provideFilesForTesting
     */
    public function testFilesCreated(array $packageFiles, array $expectedResult)
    {
        $installer = $this->createConfigurationInstaller();
        $package = $this->createPackageMockWithConfigurationFiles($installer, $packageFiles);
        $installer->install($this->repository, $package);

        foreach ($expectedResult['files'] as $file) {
            $this->assertFileExists($this->rootDirectory . DIRECTORY_SEPARATOR . $file);
        }
    }

    /**
     * @dataProvider provideFilesForTesting
     * @depends testFilesCreated
     */
    public function testFilesRemoved(array $packageFiles, array $expectedResult)
    {
        $installer = $this->createConfigurationInstaller();
        $package = $this->createPackageMockWithConfigurationFiles($installer, $packageFiles);
        $installer->install($this->repository, $package);

        $this->repository
            ->expects($this->exactly(1))
            ->method('hasPackage')
            ->with($package)
            ->willReturn(true)
        ;

        $installer->uninstall($this->repository, $package);

        foreach ($expectedResult['files'] as $file) {
            $this->assertFileDoesNotExist($this->rootDirectory . DIRECTORY_SEPARATOR . $file);
        }
    }

    /**
     * @dataProvider provideFilesForTesting
     */
    public function testGitignoreCreatedIfNotPresent(array $packageFiles, array $expectedResult)
    {
        $installer = $this->createConfigurationInstaller();
        $package = $this->createPackageMockWithConfigurationFiles($installer, $packageFiles);
        $this->assertFileDoesNotExist($this->rootDirectory . DIRECTORY_SEPARATOR . '.gitignore');

        $installer->install($this->repository, $package);
        $this->assertFileExists($this->rootDirectory . DIRECTORY_SEPARATOR . '.gitignore');
    }

    /**
     * @dataProvider provideFilesForTesting
     * @depends testGitignoreCreatedIfNotPresent
     */
    public function testGitignoreEntriesUpdated(array $packageFiles, array $expectedResult)
    {
        $installer = $this->createConfigurationInstaller();
        $package = $this->createPackageMockWithConfigurationFiles($installer, $packageFiles);
        $installer->install($this->repository, $package);

        $gitignore = new GitignoreService($this->rootDirectory . DIRECTORY_SEPARATOR . '.gitignore');
        $entries = $gitignore->getEntries();

        foreach ($expectedResult['gitignore'] as $entry) {
            $this->assertTrue(in_array($entry, $entries));
        }
    }

    /**
     * @dataProvider provideFilesForTesting
     * @depends testGitignoreCreatedIfNotPresent
     * @depends testGitignoreEntriesUpdated
     */
    public function testGitignoreEntriesRemoved(array $packageFiles, array $expectedResult)
    {
        $installer = $this->createConfigurationInstaller();
        $package = $this->createPackageMockWithConfigurationFiles($installer, $packageFiles);
        $installer->install($this->repository, $package);

        $this->repository
            ->expects($this->exactly(1))
            ->method('hasPackage')
            ->with($package)
            ->willReturn(true)
        ;

        $installer->uninstall($this->repository, $package);
        $gitignore = new GitignoreService($this->rootDirectory . DIRECTORY_SEPARATOR . '.gitignore');
        $entries = $gitignore->getEntries();

        foreach ($expectedResult['gitignore'] as $entry) {
            $this->assertFalse(in_array($entry, $entries));
        }
    }

    public function provideFilesForTesting()
    {
        return [
            'basic usage' => [
                [
                    'README.md' => 'readme content',
                    'manifest.json' => \json_encode([
                        'files' => [
                            'README.md' => 'README.md',
                        ],
                        'gitignore' =>[
                            '/README.md',
                        ]
                    ])
                ],
                [
                    'files' => [
                        'README.md'
                    ],
                    'gitignore' => [
                        '/README.md'
                    ]
                ]
            ],
            'files without target' => [
                [
                    'README.md' => 'readme content',
                    'LICENSE' => 'license content',
                    'manifest.json' => \json_encode([
                        'files' => [
                            'README.md',
                            'LICENSE'
                        ],
                        'gitignore' =>[
                            '/README.md',
                            '/LICENSE',
                        ]
                    ])
                ],
                [
                    'files' => [
                        'README.md',
                        'LICENSE'
                    ],
                    'gitignore' => [
                        '/README.md',
                        '/LICENSE',
                    ]
                ]
            ],
            'files in folders' => [
                [
                    'src/file1.md' => 'file1 content',
                    'src/file2.md' => 'file2 content',
                    'manifest.json' => \json_encode([
                        'files' => [
                            'src/file1.md',
                            'src/file2.md' => 'README.md'
                        ],
                        'gitignore' =>[
                            '/src/file1.md',
                            '/README.md',
                        ]
                    ])
                ],
                [
                    'files' => [
                        'src/file1.md',
                        'README.md'
                    ],
                    'gitignore' => [
                        '/src/file1.md',
                        '/README.md',
                    ]
                ]
            ],
        ];
    }
}
