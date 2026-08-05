<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyManualTierEnumInMembersTable extends Migration
{
    public function up()
    {
        $this->db->query("ALTER TABLE `members` MODIFY COLUMN `manual_tier` ENUM('none', 'living_library', 'silver', 'gold', 'platinum') NOT NULL DEFAULT 'none'");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE `members` MODIFY COLUMN `manual_tier` ENUM('none', 'silver', 'gold', 'platinum') NOT NULL DEFAULT 'none'");
    }
}
