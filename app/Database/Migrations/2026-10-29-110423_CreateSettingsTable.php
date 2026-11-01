<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSettingsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => ['type' => 'BIGSERIAL', 'auto_increment' => true],
            'setting_key' => ['type' => 'VARCHAR', 'constraint' => 50, 'unique' => true],
            'setting_value' => ['type' => 'TEXT', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('settings');

        // Seed the default values so the page doesn't break on first load
        $db = \Config\Database::connect();
        $defaults = [
            ['setting_key' => 'shop_name', 'setting_value' => 'SS Laundry'],
            ['setting_key' => 'shop_address', 'setting_value' => '123 Main St, Manila'],
            ['setting_key' => 'shop_phone', 'setting_value' => '0917-123-4567'],
            ['setting_key' => 'rate_wash_fold', 'setting_value' => '50'],
            ['setting_key' => 'rate_wash_dry', 'setting_value' => '50'],
            ['setting_key' => 'sms_intake', 'setting_value' => 'Hi {name}! Your laundry {code} was received ({weight}kg). Track live: {tracking_link}'],
            ['setting_key' => 'sms_ready', 'setting_value' => 'Hi {name}! Your laundry {code} is Ready for Pickup at SS Laundry. Please bring your claim code.'],
        ];
        $db->table('settings')->insertBatch($defaults);
    }

    public function down()
    {
        $this->forge->dropTable('settings');
    }
}