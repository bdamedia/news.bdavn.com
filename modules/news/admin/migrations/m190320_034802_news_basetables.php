<?php

use yii\db\Migration;

/**
 * Class m190320_034802_news_basetables
 */
class m190320_034802_news_basetables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('news_article', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255)->notNull(),
            'content' => $this->text(),
            'image_name' => $this->string(255),
            'status' => $this->boolean()->defaultValue(0),
            'source' => $this->string(255),
            'create_user_id' => $this->integer(11)->defaultValue(0),
            'update_user_id' => $this->integer(11)->defaultValue(0),
            'create_date' => $this->datetime(),
            'update_date' => $this->datetime()
        ]);

        $this->createTable('news_article_category', [
            'id' => $this->primaryKey(),
            'article_id' => $this->integer(11)->notNull(),
            'category_id' => $this->integer(11)->notNull()
        ]);

        $this->createTable('news_categories', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'slug' => $this->string(255),
            'parent_id' => $this->integer(11)->defaultValue(0),
            'create_date' => $this->datetime(),
            'update_date' => $this->datetime()
        ]);

        $this->createTable('news_search_cache', [
            'id' => $this->primaryKey(),
            'query' => $this->string(255)->notNull(),
            'data' => $this->text(),
            'create_date' => $this->datetime()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('news_article');
        $this->dropTable('news_categories');
        $this->dropTable('news_search_cache');

        return false;
    }
}
