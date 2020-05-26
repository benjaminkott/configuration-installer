<?php declare(strict_types=1);

/*
 * This file is part of the bk2k/configuration-installer.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace BK2K\ConfigurationInstaller\Configuration;

class InstallerConfiguration
{
    /**
     * @var File[]
     */
    protected $files = [];

    /**
     * @var GitignoreEntry[]
     */
    protected $gitignoreEntries = [];

    public function addFile(File $file): self
    {
        $this->files[] = $file;
        return $this;
    }

    /**
     * @return File[]
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    public function addGitignoreEntry(GitignoreEntry $gitignoreEntry): self
    {
        $this->gitignoreEntries[] = $gitignoreEntry;
        return $this;
    }

    /**
     * @return GitignoreEntry[]
     */
    public function getGitignoreEntries(): array
    {
        return $this->gitignoreEntries;
    }
}
