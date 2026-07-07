<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCheckoutFieldsToTransaction extends Migration
{
    public function up()
    {
        $fields = [
            'biaya_admin' => [
                'type' => 'DOUBLE',
                'null' => true,
                'default' => null,
            ],
            'kupon_code' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'diskon_kupon' => [
                'type' => 'DOUBLE',
                'null' => true,
                'default' => null,
            ],
            'cashback' => [
                'type' => 'DOUBLE',
                'null' => true,
                'default' => null,
            ],
        ];

        $this->forge->addColumn('transaction', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('transaction', 'biaya_admin');
        $this->forge->dropColumn('transaction', 'kupon_code');
        $this->forge->dropColumn('transaction', 'diskon_kupon');
        $this->forge->dropColumn('transaction', 'cashback');
    }
}

