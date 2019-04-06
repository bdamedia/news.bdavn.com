<?php

use yii\db\Migration;

/**
 * Class m190330_080816_news_menus
 */
class m190330_080816_news_menus extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('news_menus', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'status' =>  $this->tinyInteger(1)->defaultValue(1),
            'create_date' => $this->datetime(),
            'update_date' => $this->datetime()
        ]);

        $this->createTable('news_menu_positions', [
            'id' => $this->primaryKey(),
            'menu_id' => $this->integer()->notNull(),
            'position' =>  $this->tinyInteger(1)->defaultValue(0),
        ]);

        $this->createTable('news_menu_items', [
            'id' => $this->primaryKey(),
            'menu_id' =>   $this->integer()->notNull(),
            'name' => $this->string(255),
            'url' => $this->string(255),
            'object_id' =>  $this->integer()->defaultValue(0),
            'object_type' =>  $this->string(20),
            'parent_id' => $this->integer()->defaultValue(0),
            'sort_order' => $this->integer()->defaultValue(1),
            'create_date' => $this->datetime(),
            'update_date' => $this->datetime()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('news_menus');
        $this->dropTable('news_menu_items');

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190325_090015_news_ads cannot be reverted.\n";

        return false;
    }
    */
}
