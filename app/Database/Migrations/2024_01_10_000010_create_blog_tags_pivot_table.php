<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBlogTagsPivotTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'blog_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'tag_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
        ]);
        
        $this->forge->addKey(['blog_id', 'tag_id'], true);
        $this->forge->addForeignKey('blog_id', 'blogs', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('tag_id', 'blog_tags', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('blog_tag_pivot');
    }

    public function down()
    {
        $this->forge->dropTable('blog_tag_pivot');
    }
}