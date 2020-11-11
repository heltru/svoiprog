<?php

use yii\db\Migration;

class m180110_073905_create_table_img_links extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%img_links}}', [
            'id' => $this->integer(11)->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'type' => $this->string(),
            'id_type' => $this->integer(11),
            'id_image' => $this->integer(11),
            'ord' => $this->integer(11)->defaultValue('0'),
        ], $tableOptions);

        $this->createIndex('index2', '{{%img_links}}', ['id_type','type','ord','id_image'], true);
    }

    public function safeDown()
    {
        $this->dropTable('{{%img_links}}');
    }
}
