<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingModel extends Model
{
    protected $table         = 'settings';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['setting_key', 'setting_value'];
    protected $useTimestamps = false;

    /** ['rate_wash_fold' => '55', 'shop_name' => 'SAS Laundry', ...] */
    public function getAllKeyed(): array
    {
        $out = [];
        foreach ($this->findAll() as $row) {
            $out[$row['setting_key']] = $row['setting_value'];
        }
        return $out;
    }

    /** Upsert a single setting. (Renamed from set() because Model::set() is reserved.) */
    public function setValue(string $key, ?string $value): void
    {
        $existing = $this->where('setting_key', $key)->first();
        if ($existing) {
            $this->update($existing['id'], ['setting_value' => $value]);
        } else {
            $this->insert(['setting_key' => $key, 'setting_value' => $value]);
        }
    }
}