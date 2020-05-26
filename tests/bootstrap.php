<?php declare(strict_types=1);

/*
 * This file is part of the bk2k/configuration-installer.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

$loader = require __DIR__ . '/../src/bootstrap.php';
$loader->add('BK2K\ConfigurationInstallerTest\Installer', __DIR__);
