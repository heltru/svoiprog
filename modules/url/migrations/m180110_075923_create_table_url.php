<?php

use yii\db\Migration;

class m180110_075923_create_table_url extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%url}}', [
            'id' => $this->integer(11)->unsigned()->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'href' => $this->string(1000)->notNull()->defaultValue(''),
            'real_canonical' => $this->string(255)->defaultValue(''),
            'title' => $this->string(255)->defaultValue(''),
            'h1' => $this->string(1000)->defaultValue(''),
            'description_meta' => $this->text()->notNull(),
            'redirect' => $this->integer(11)->unsigned()->notNull()->defaultValue('0'),
            'controller' => $this->string(45)->defaultValue(''),
            'action' => $this->string(45)->defaultValue(''),
            'identity' => $this->integer(11)->defaultValue('0'),
            'crs' => $this->integer(10)->unsigned()->defaultValue('0')->comment('crc32 hash для url 
SELECT * FROM url WHERE crs= CRC32("какой то url") AND url = "какой то url" LIMIT 1'),
            'domain_id' => $this->integer(11)->defaultValue('0'),
            'last_mod' => $this->dateTime()->notNull(),
            'public' => $this->smallInteger(1)->defaultValue('1'),
            'pagination' => $this->smallInteger(1)->notNull()->defaultValue('0'),
            'keywords' => $this->string(255)->notNull()->defaultValue(''),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%url}}');
    }
}
