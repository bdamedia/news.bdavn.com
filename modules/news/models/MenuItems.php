<?php

namespace app\modules\news\models;

use Yii;

/**
 * This is the model class for table "news_menu_items".
 *
 * @property int $id
 * @property int $menu_id
 * @property string $name
 * @property string $url
 * @property int $object_id
 * @property string $object_type
 * @property int $parent_id
 * @property int $sort_order
 * @property string $create_date
 * @property string $update_date
 */
class MenuItems extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'news_menu_items';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['menu_id'], 'required'],
            [['menu_id', 'object_id', 'parent_id', 'sort_order'], 'integer'],
            [['create_date', 'update_date'], 'safe'],
            [['name', 'url'], 'string', 'max' => 255],
            [['object_type'], 'string', 'max' => 20],
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
            'name' => Yii::t('app', 'Name'),
            'url' => Yii::t('app', 'Url'),
            'object_id' => Yii::t('app', 'Object ID'),
            'object_type' => Yii::t('app', 'Object Type'),
            'parent_id' => Yii::t('app', 'Parent ID'),
            'sort_order' => Yii::t('app', 'Sort Order'),
            'create_date' => Yii::t('app', 'Create Date'),
            'update_date' => Yii::t('app', 'Update Date'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->on(self::EVENT_BEFORE_INSERT, [$this, 'eventBeforeInsert']);
        $this->on(self::EVENT_BEFORE_UPDATE, [$this, 'eventBeforeUpdate']);
    }

    public function eventBeforeUpdate()
    {
        $this->update_date = date('Y-m-d H:i:s');
    }
    
    public function eventBeforeInsert()
    {
        $this->update_date = date('Y-m-d H:i:s');
        $this->create_date = date('Y-m-d H:i:s');   
    }
}
