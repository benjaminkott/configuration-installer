<?php declare(strict_types=1);

/*
 * This file is part of the bk2k/configuration-installer.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace BK2K\ConfigurationInstaller\Installer;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;

class Plugin implements PluginInterface
{
    public function activate(
        Composer $composer,
        IOInterface $io
    ) {
        $installer = new ConfigurationInstaller($io, $composer);
        $composer->getInstallationManager()->addInstaller($installer);
    }

    public function deactivate(
        Composer $composer,
        IOInterface $io
    ) {
    }

    public function uninstall(
        Composer $composer,
        IOInterface $io
    ) {
    }
}
