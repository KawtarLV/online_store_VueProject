<?php

namespace App\Repositories;

use App\Framework\Database;
use App\Models\Setting;
use PDO;

class SettingsRepository implements ISettingsRepository
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?: Database::pdo();
    }

    public function get(): Setting
    {
        $stmt = $this->db->query('SELECT id, store_name, contact_email, updated_at FROM settings ORDER BY id ASC LIMIT 1');
        $row = $stmt->fetch();

        if (!$row) {
            $setting = new Setting();
            $setting->id = 1;
            return $setting;
        }

        return $this->map($row);
    }

    public function update(Setting $setting): Setting
    {
        $existingId = $this->getExistingId();
        if ($existingId !== null) {
            $stmt = $this->db->prepare(
                'UPDATE settings SET store_name = ?, contact_email = ? WHERE id = ?'
            );
            $stmt->execute([
                $setting->store_name,
                $setting->contact_email,
                $existingId,
            ]);
        } else {
            $stmt = $this->db->prepare(
                'INSERT INTO settings (store_name, contact_email) VALUES (?, ?)'
            );
            $stmt->execute([
                $setting->store_name,
                $setting->contact_email,
            ]);
        }

        return $this->get();
    }

    private function getExistingId(): ?int
    {
        $stmt = $this->db->query('SELECT id FROM settings ORDER BY id ASC LIMIT 1');
        $value = $stmt->fetchColumn();

        return $value === false ? null : (int) $value;
    }

    /**
     * @param array<string, mixed> $row
     */
    private function map(array $row): Setting
    {
        $setting = new Setting();
        $setting->id = (int) ($row['id'] ?? 1);
        $setting->store_name = (string) ($row['store_name'] ?? '');
        $setting->contact_email = (string) ($row['contact_email'] ?? '');
        $setting->updated_at = isset($row['updated_at']) ? (string) $row['updated_at'] : null;

        return $setting;
    }
}
