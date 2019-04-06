<?php

namespace app\modules\news\models;

use Yii;
use luya\admin\ngrest\base\NgRestModel;

/**
 * Ads.
 * 
 * File has been created with `crud/create` command. 
 *
 * @property integer $id
 * @property string $title
 * @property text $content
 * @property tinyint $position
 * @property datetime $create_date
 * @property datetime $update_date
 */
class Ads extends NgRestModel
{
    const POS_TOP = 1;
    const POS_SIDEBAR = 2;
    const POS_BOTTOM = 3;
    //const POS_RIGHT = 4;
    const POS_INNER_NEWS = 5;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'news_ads';
    }

    /**
     * @inheritdoc
     */
    public static function ngRestApiEndpoint()
    {
        return 'api-news-ads';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'title' => Yii::t('app', 'Title'),
            'content' => Yii::t('app', 'Content'),
            'position' => Yii::t('app', 'Position'),
            'create_date' => Yii::t('app', 'Create Date'),
            'update_date' => Yii::t('app', 'Update Date'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['content'], 'required'],
            [['content'], 'string'],
            [['position'], 'integer'],
            [['create_date', 'update_date'], 'safe'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function ngRestAttributeTypes()
    {
        return [
            'title' => 'text',
            'content' => [
                'html',
                'nl2br' => false
            ],
            'position' => ['selectArray', 'data' => $this->positionArray()],
        ];
    }

    /**
     * @inheritdoc
     */
    public function ngRestScopes()
    {
        return [
            ['list', ['title', 'position']],
            [['create', 'update'], ['title', 'content', 'position']],
            ['delete', true],
        ];
    }

    public function positionArray(){
        return [
            self::POS_TOP => Yii::t('app', 'Top'),
            self::POS_SIDEBAR => Yii::t('app', 'Side bar'),
            self::POS_BOTTOM => Yii::t('app', 'Bottom'),
            //self::POS_RIGHT => Yii::t('app', 'Right'),
            self::POS_INNER_NEWS => Yii::t('app', 'Inner News'),
        ];
    }

    public static function getAds(Array $positions){
        $ads = static::find();
        if($positions)
            $ads->where(['position' => $positions]);

        $rs = [];
        foreach($ads->all() as $item){
            if(!isset($rs[$item->position]))
                $rs[$item->position] = [];

            $rs[$item->position][] = $item->content;
        }

        return $rs;
    }
}