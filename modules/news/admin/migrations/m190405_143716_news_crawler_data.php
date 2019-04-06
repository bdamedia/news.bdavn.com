<?php

use yii\db\Migration;

/**
 * Class m190405_143716_news_crawler_data
 */
class m190405_143716_news_crawler_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('news_crawler_data', [
            'id' => $this->primaryKey(),
            'hashkey' => $this->string(32)->notNull(),
            'categories' => $this->string(255)->notNull(),
            'api_url' => $this->string(255)->notNull(),
            'api_data' => $this->text()->notNull()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('news_crawler_data');

        return false;
    }
}
