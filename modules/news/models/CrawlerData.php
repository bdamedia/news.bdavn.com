<?php

namespace app\modules\news\models;

use Yii;

/**
 * This is the model class for table "news_crawler_data".
 *
 * @property int $id
 * @property string $hashkey
 * @property string $categories
 * @property string $api_url
 * @property string $api_data
 */
class CrawlerData extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'news_crawler_data';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['hashkey', 'categories', 'api_url', 'api_data'], 'required'],
            [['api_data'], 'string'],
            [['hashkey'], 'string', 'max' => 32],
            [['categories', 'api_url'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'hashkey' => Yii::t('app', 'Hashkey'),
            'categories' => Yii::t('app', 'Categories'),
            'api_url' => Yii::t('app', 'Api Url'),
            'api_data' => Yii::t('app', 'Api Data'),
        ];
    }
}
