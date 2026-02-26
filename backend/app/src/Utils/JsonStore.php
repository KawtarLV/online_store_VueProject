<?php

namespace App\Utils;

/**
 * Tiny JSON file store used as a dummy database.
 */
class JsonStore
{
    private string $filePath;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;

        if (!file_exists($this->filePath)) {
            $this->persist([]);
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function all(): array
    {
        $content = file_get_contents($this->filePath);
        if ($content === false || $content === '') {
            return [];
        }
        $data = json_decode($content, true);
        return is_array($data) ? $data : [];
    }

    /**
     * @param array<int, array<string, mixed>> $records
     */
    public function persist(array $records): void
    {
        $fp = fopen($this->filePath, 'c+');
        if (!$fp) {
            throw new \RuntimeException('Unable to open data store');
        }

        if (!flock($fp, LOCK_EX)) {
            fclose($fp);
            throw new \RuntimeException('Unable to lock data store');
        }

        ftruncate($fp, 0);
        fwrite($fp, json_encode(array_values($records), JSON_PRETTY_PRINT));
        fflush($fp);
        flock($fp, LOCK_UN);
        fclose($fp);
    }
}
