<?php declare(strict_types=1);

/*
 * This file is part of the bk2k/configuration-installer.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace BK2K\ConfigurationInstaller\Handler;

use BK2K\ConfigurationInstaller\Configuration\InstallerConfiguration;
use Composer\Package\PackageInterface;

class FileHandler extends AbstractHandler
{
    public function install(InstallerConfiguration $installerConfiguration, PackageInterface $package)
    {
        $this->io->write('    Installing file links for <info>' . $package->getName() . '</info>');
        $packagePath = $this->getPackagePath($package);
        foreach ($installerConfiguration->getFiles() as $file) {
            $source = $this->filesystem->normalizePath($packagePath . '/' . $file->getSource());
            $target = $this->filesystem->normalizePath(getcwd() . '/' . $file->getTarget());
            if (!file_exists($source)) {
                throw new \InvalidArgumentException('The source "' . $source . '" is not available.');
            }
            if (file_exists($target)) {
                $this->filesystem->remove($target);
            }
            $this->filesystem->ensureDirectoryExists(dirname($target));
            $this->filesystem->copy($source, $target);
            $this->io->write('    <info>' . $source . '</info> -> <info>' . $target . '</info>');
        }
    }

    public function remove(InstallerConfiguration $installerConfiguration, PackageInterface $package)
    {
        $this->io->write('    Remove file links for <info>' . $package->getName() . '</info>');
        foreach ($installerConfiguration->getFiles() as $file) {
            $target = $this->filesystem->normalizePath(realpath($file->getTarget()));
            $this->io->write('    Remove <info>' . $target . '</info>');
            $this->filesystem->unlink($target);
        }
    }

    private function getPackagePath(PackageInterface $package): string
    {
        $vendorDir = rtrim($this->composer->getConfig()->get('vendor-dir'), '/');
        $basePath = ($vendorDir ? $vendorDir . '/' : '') . $package->getPrettyName();
        $targetDir = $package->getTargetDir();

        return $basePath . ($targetDir ? '/' . $targetDir : '');
    }
}
