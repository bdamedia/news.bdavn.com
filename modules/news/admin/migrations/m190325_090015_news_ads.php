<?php

use yii\db\Migration;

/**
 * Class m190325_090015_news_ads
 */
class m190325_090015_news_ads extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('news_ads', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255),
            'content' => $this->text()->notNull(),
            'position' => $this->tinyInteger(1)->defaultValue(0),
            'create_date' => $this->datetime(),
            'update_date' => $this->datetime()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('news_ads');

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
