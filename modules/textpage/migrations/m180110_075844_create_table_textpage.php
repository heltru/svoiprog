<?php

use yii\db\Migration;

class m180110_075844_create_table_textpage extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%textpage}}', [
            'id' => $this->integer(11)->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'name' => $this->string(255)->notNull()->defaultValue(''),
            'status' => $this->integer(10)->unsigned()->notNull()->defaultValue('0'),
            'type_page' => $this->string(70)->notNull()->defaultValue(''),
            'text' => $this->text()->notNull(),
            'key' => $this->integer(11)->notNull(),
            'sitemap' => $this->smallInteger(1)->notNull()->defaultValue('1'),
            'module' => $this->string(70)->notNull()->defaultValue(''),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%textpage}}');
    }
}
