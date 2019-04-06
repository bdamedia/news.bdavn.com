<?php

namespace app\modules\news\models;

use Yii;
use luya\admin\ngrest\base\NgRestModel;

/**
 * Article Category.
 * 
 * File has been created with `crud/create` command. 
 *
 * @property integer $id
 * @property integer $article_id
 * @property integer $category_id
 */
class ArticleCategory extends NgRestModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'news_article_category';
    }

    /**
     * @inheritdoc
     */
    public static function ngRestApiEndpoint()
    {
        return 'api-news-articlecategory';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'article_id' => Yii::t('app', 'Article ID'),
            'category_id' => Yii::t('app', 'Category ID'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['article_id', 'category_id'], 'required'],
            [['article_id', 'category_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function ngRestAttributeTypes()
    {
        return [
            'article_id' => 'number',
            'category_id' => 'number',
        ];
    }

    /**
     * @inheritdoc
     */
    public function ngRestScopes()
    {
        return [
            ['list', ['article_id', 'category_id']],
            [['create', 'update'], ['article_id', 'category_id']],
            ['delete', false],
        ];
    }
}