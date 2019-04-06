<?php

namespace app\modules\news\models;

use Yii;

/**
 * This is the model class for table "news_menu_positions".
 *
 * @property int $id
 * @property int $menu_id
 * @property int $position
 */
class MenuPositions extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'news_menu_positions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['menu_id'], 'required'],
            [['menu_id', 'position'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'menu_id' => Yii::t('app', 'Menu ID'),
            'position' => Yii::t('app', 'Position'),
        ];
    }
}
