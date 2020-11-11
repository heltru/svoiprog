<?php

use yii\db\Migration;

class m180110_075801_create_table_settings extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%settings}}', [
            'id' => $this->integer(10)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'name' => $this->string(64)->notNull()->defaultValue(''),
            'value' => $this->string(1024)->notNull()->defaultValue(''),
            'description' => $this->string(256)->notNull()->defaultValue(''),
            'update_at' => $this->dateTime()->notNull(),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%settings}}');
    }
}
