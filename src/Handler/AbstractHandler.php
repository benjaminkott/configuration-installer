<?php declare(strict_types=1);

/*
 * This file is part of the bk2k/configuration-installer.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace BK2K\ConfigurationInstaller\Handler;

use BK2K\ConfigurationInstaller\Configuration\InstallerConfiguration;
use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Composer\Util\Filesystem;

abstract class AbstractHandler
{
    protected $composer;
    protected $io;
    protected $filesystem;

    public function __construct(Composer $composer, IOInterface $io, Filesystem $filesystem)
    {
        $this->composer = $composer;
        $this->io = $io;
        $this->filesystem = $filesystem;
        $this->initialize();
    }

    public function initialize()
    {
    }

    abstract public function install(InstallerConfiguration $installerConfiguration, PackageInterface $package);
    abstract public function remove(InstallerConfiguration $installerConfiguration, PackageInterface $package);
}
