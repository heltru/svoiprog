<?php

use yii\db\Migration;

class m180110_073905_create_table_img extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%img}}', [
            'id' => $this->integer(11)->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'filename' => $this->string(255)->notNull()->comment('Имя файла'),
            'width' => $this->integer(50)->defaultValue('0')->comment('Ширина'),
            'height' => $this->integer(50)->defaultValue('0')->comment('Высота'),
            'date_cr' => $this->dateTime()->notNull()->comment('Дата создания'),
            'alt' => $this->string(255)->defaultValue('')->comment('Альт тег'),
            'title' => $this->string(255)->defaultValue('')->comment('Тайтл тег'),
            'ord' => $this->smallInteger(3)->unsigned()->defaultValue('0')->comment('Порядок'),
            'status' => $this->integer(9)->notNull()->defaultValue('0')->comment('Статус'),
            'parent_id' => $this->integer(11)->defaultValue('0')->comment('Продукт'),
            'name_image' => $this->string(255)->defaultValue(''),
            'watermark' => $this->smallInteger(1)->unsigned()->defaultValue('0'),
            'original' => $this->integer(10)->unsigned()->defaultValue('0'),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%img}}');
    }
}
