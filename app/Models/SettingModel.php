<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingModel extends Model
{
    protected $table            = 'settings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['class', 'key', 'value', 'type', 'context'];
    protected $useTimestamps    = true;

    /**
     * Get single setting value by key
     */
    public function getSetting(string $key, $default = null)
    {
        $row = $this->where('class', 'Library')->where('key', $key)->first();
        return $row ? $row['value'] : $default;
    }

    /**
     * Set / update single setting value
     */
    public function setSetting(string $key, $value): bool
    {
        $existing = $this->where('class', 'Library')->where('key', $key)->first();
        if ($existing) {
            return (bool)$this->update($existing['id'], [
                'value'      => (string)$value,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        } else {
            return (bool)$this->insert([
                'class'      => 'Library',
                'key'        => $key,
                'value'      => (string)$value,
                'type'       => 'string',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    /**
     * Get all Library settings as key => value array
     */
    public function getAllLibrarySettings(): array
    {
        $rows = $this->where('class', 'Library')->findAll();
        $settings = [];
        foreach ($rows as $row) {
            $settings[$row['key']] = $row['value'];
        }
        return $settings;
    }
}
