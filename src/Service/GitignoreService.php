<?php declare(strict_types=1);

/*
 * This file is part of the bk2k/configuration-installer.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace BK2K\ConfigurationInstaller\Service;

/**
 * Class GitignoreService
 */
class GitignoreService
{
    /**
     * @var array
     */
    protected $lines = [];

    /**
     * @var string
     */
    protected $gitignore = '.gitignore';

    /**
     * @var bool
     */
    protected $hasChanges = false;

    public function __construct()
    {
        $this->ensureGitignoreExists();
        $this->lines = $this->removeDuplicates(file($this->gitignore, FILE_IGNORE_NEW_LINES));
    }

    public function addEntry(string $entry): void
    {
        $entry = $this->prependSlashIfNotExist($entry);
        if (!in_array($entry, $this->lines)) {
            $this->lines[] = $entry;
        }
        $this->hasChanges = true;
    }

    public function removeEntry(string $entry): void
    {
        $entry = $this->prependSlashIfNotExist($entry);
        $key = array_search($entry, $this->lines);
        if (false !== $key) {
            unset($this->lines[$key]);
            $this->hasChanges = true;
            // renumber array
            $this->lines = array_values($this->lines);
        }
    }

    public function getEntries(): array
    {
        return $this->lines;
    }

    public function write(): void
    {
        if ($this->hasChanges) {
            file_put_contents($this->gitignore, implode("\n", $this->lines) . "\n");
        }
    }

    private function prependSlashIfNotExist(string $file): string
    {
        return sprintf('/%s', ltrim($file, '/'));
    }

    private function removeDuplicates(array $lines): array
    {
        // remove empty lines
        $duplicates = array_filter($lines);
        // remove comments
        $duplicates = array_filter($duplicates, function ($line) {
            return strpos($line, '#') !== 0;
        });
        // check if duplicates exist
        if (count($duplicates) !== count(array_unique($duplicates))) {
            $duplicates = array_filter(array_count_values($duplicates), function ($count) {
                return $count > 1;
            });
            // search from bottom to top
            $lines = array_reverse($lines);
            foreach ($duplicates as $duplicate => $count) {
                // remove all duplicates, except the first one
                for ($i = 1; $i < $count; $i++) {
                    $key = array_search($duplicate, $lines);
                    unset($lines[$key]);
                }
            }
            // restore original order
            $lines = array_values(array_reverse($lines));
        }
        return $lines;
    }

    private function ensureGitignoreExists()
    {
        if (!file_exists($this->gitignore)) {
            file_put_contents($this->gitignore, "\n");
        }
    }
}
