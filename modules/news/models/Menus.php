<?php

namespace app\modules\news\models;

use Yii;

/**
 * This is the model class for table "news_menus".
 *
 * @property int $id
 * @property string $name
 * @property int $status
 * @property string $create_date
 * @property string $update_date
 */
class Menus extends \yii\db\ActiveRecord
{
    const POS_TOP = 1;
    const POS_BOTTOM = 2;
    const POS_SIDEBAR = 3;
    const POS_MAIN = 4;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'news_menus';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['status'], 'integer'],
            [['create_date', 'update_date'], 'safe'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'status' => Yii::t('app', 'Status'),
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

    public static function getAllItems($menuId, $sort = []){
        if(!$sort){
            $sort = ['sort_order' => SORT_ASC];
        }

        return MenuItems::find()->where(['menu_id' => $menuId])->orderBy($sort)->all();
    }

    public function filterItemsByParent($items, $parentId){
        return array_filter($items, function ($item) use ($parentId) {
            return $item->parent_id == $parentId;
        });
    }

    public static function getRecusiveItems($menuId, $depth, $sort = [])
    {
        $items = self::getAllItems($menuId, $sort);

        return self::getChilds($items, 0, 1, $depth, $sort);
    }

    public static function getChilds($allItems, $parentId, $level, $depth, $sort)
    {
        if(!$sort)
            $sort = ['sort_order' => 'asc'];
            
        $items = self::filterItemsByParent($allItems, $parentId);
        
        $childs = [];
        foreach($items as $item){
            $childs[] = [
                'id' => $item->id,
                'name' => $item->name,
                'url' => $item->url,
                'type' => $item->object_type,
                'objectId' => $item->object_id,
                'sOrder' => $item->sort_order,
                'childs' => (!$depth || $level < $depth)?self::getChilds($allItems, $item->id, $level+1, $depth, $sort):[]
            ];
        }

        $level++;

        return $childs;
    }

    public static function getItems($menuId, $depth = 0, $sort = [])
    {
        $items = self::getRecusiveItems($menuId, $depth, $sort);
        $itemsArr = [];
        
        foreach($items as $item){
            $prefix = '';
            $level = 1;
            $itemsArr[] = [
                'id' => $item['id'],
                'label' => $prefix.$item['name'],
                'name' => $item['name'],
                'url' => $item['url'],
                'type' => $item['type'],
                'objectId' => $item['objectId'],
                'level' => $level,
            ];

            if($item['childs']){
                self::getChildren($menuId, $item['childs'], $itemsArr, $level, $prefix);
            }
        }

        return $itemsArr;
    }

    public static function getChildren($menuId, $items, &$itemsArr, $level, $prefix){
        $prefix = '-'.$prefix;
        $level++;

        foreach($items as $item){
            $itemsArr[] = [
                'id' => $item['id'],
                'label' => $prefix.$item['name'],
                'name' => $item['name'],
                'url' => $item['url'],
                'type' => $item['type'],
                'objectId' => $item['objectId'],
                'level' => $level,
            ];

            if($item['childs']){
                self::getChildren($menuId, $item['childs'], $itemsArr, $level, $prefix);
            }
        }
    }

    public static function getAll(){
        $menus = Menus::find()->where(['status' => 1])->orderBy(['name' => SORT_ASC])->all();
        $data = [];

        foreach($menus as $menu){
            $items = self::getItems($menu->id);
            foreach($items as $i => $item){
                $items[$i]['index'] = $i;
                $items[$i]['action'] = '';
                $items[$i]['expanded'] = false;
            }
            $data[] = [
                'id' => $menu->id,
                'name' => $menu->name,
                'items' => $items,
                'positions' => $menu->getPositions()
            ];
        }

        return $data;
    }

    public function getPositions(){
        $positions = Menus::getPositionsData();

        $selected = array_map(function($item){return $item->position;}, MenuPositions::find()
            ->where(['menu_id' => $this->id])
            ->select('position')->all());

        foreach($positions as $i => $position){
            if(in_array($position['id'], $selected))
                $positions[$i]['checked'] = true;
        }

        return $positions;
    }

    public static function getPositionsData(){
        return [
            ['id' => self::POS_MAIN, 'name' => Yii::t('app', 'Main menu')],
            ['id' => self::POS_TOP, 'name' => Yii::t('app', 'Top')],
            ['id' => self::POS_BOTTOM, 'name' => Yii::t('app', 'Bottom')],
            ['id' => self::POS_SIDEBAR, 'name' => Yii::t('app', 'Sidebar')],
        ];
    }
}
