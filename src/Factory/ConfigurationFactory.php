<?php declare(strict_types=1);

/*
 * This file is part of the bk2k/configuration-installer.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace BK2K\ConfigurationInstaller\Factory;

use BK2K\ConfigurationInstaller\Configuration\File;
use BK2K\ConfigurationInstaller\Configuration\GitignoreEntry;
use BK2K\ConfigurationInstaller\Configuration\InstallerConfiguration;

class ConfigurationFactory
{
    public static function fromJson(string $string): InstallerConfiguration
    {
        $data = json_decode($string, true, 512, JSON_THROW_ON_ERROR);
        return self::fromArray($data);
    }

    public static function fromArray(array $data): InstallerConfiguration
    {
        $installerConfiguration = new InstallerConfiguration();
        foreach ($data['files'] ?? [] as $source => $target) {
            if (!is_string($target)) {
                continue;
            }
            $source = is_int($source) ? $target : $source;
            $installerConfiguration->addFile(new File($source, $target));
        }
        foreach ($data['gitignore'] ?? [] as $value) {
            $installerConfiguration->addGitignoreEntry(new GitignoreEntry($value));
        }
        return $installerConfiguration;
    }
}
