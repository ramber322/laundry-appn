<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOrdersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'           => ['type' => 'BIGSERIAL', 'auto_increment' => true],
            'claim_code'   => ['type' => 'VARCHAR', 'constraint' => 20, 'unique' => true], // e.g. LND-8821
            'name'         => ['type' => 'VARCHAR', 'constraint' => 100], // Name / Alias
            'phone'        => ['type' => 'VARCHAR', 'constraint' => 20],
            'service'      => ['type' => 'VARCHAR', 'constraint' => 50], // Wash & Fold, Wash & Dry
            'weight'       => ['type' => 'DECIMAL', 'constraint' => '5,2'],
            'price'        => ['type' => 'DECIMAL', 'constraint' => '10,2'],
            'paid'         => ['type' => 'BOOLEAN', 'default' => false],
            'status'       => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'active'], // active, completed, cancelled
            'stage'        => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'intake'], // intake, washing, drying, folding, ready
            'created_at'   => ['type' => 'TIMESTAMP', 'null' => true],
            'completed_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('orders');
    }

    public function down()
    {
        $this->forge->dropTable('orders');
    }
}