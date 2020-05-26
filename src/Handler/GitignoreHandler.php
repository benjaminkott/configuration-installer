<?php declare(strict_types=1);

/*
 * This file is part of the bk2k/configuration-installer.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace BK2K\ConfigurationInstaller\Handler;

use BK2K\ConfigurationInstaller\Configuration\InstallerConfiguration;
use BK2K\ConfigurationInstaller\Service\GitignoreService;
use Composer\Package\PackageInterface;

class GitignoreHandler extends AbstractHandler
{
    private $gitignore;

    public function initialize()
    {
        $this->gitignore = new GitignoreService();
    }

    public function install(InstallerConfiguration $installerConfiguration, PackageInterface $package)
    {
        foreach ($installerConfiguration->getGitignoreEntries() as $entry) {
            $this->gitignore->addEntry($entry->getPath());
        }
        $this->gitignore->write();
    }

    public function remove(InstallerConfiguration $installerConfiguration, PackageInterface $package)
    {
        foreach ($installerConfiguration->getGitignoreEntries() as $entry) {
            $this->gitignore->removeEntry($entry->getPath());
        }
        $this->gitignore->write();
    }
}
