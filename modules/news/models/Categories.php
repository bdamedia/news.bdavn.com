<?php

namespace app\modules\news\models;

use Yii;
use luya\admin\ngrest\base\NgRestModel;

/**
 * Categories.
 * 
 * File has been created with `crud/create` command. 
 *
 * @property integer $id
 * @property string $name
 * @property integer $parent_id
 * @property datetime $create_date
 * @property datetime $update_date
 */
class Categories extends NgRestModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'news_categories';
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
        if(!$this->slug)
            $this->slug = $this->generateSlug(0, $this->name);

        $this->update_date = date('Y-m-d H:i:s');
    }
    
    public function eventBeforeInsert()
    {
        $this->slug = $this->generateSlug(0, $this->name);
        $this->update_date = date('Y-m-d H:i:s');
        $this->create_date = date('Y-m-d H:i:s');   
    }

    public function generateSlug($id, $name){
        if(!$name)
            return '';

        $name = $slug = \app\components\Common::slugify($name);
        $i = 0;
        while(self::find()->where(['slug' => $slug])->one()){
            $i++;
            $slug = $name.$i;
        }

        return $slug;
    }

    /**
     * @inheritdoc
     */
    public static function ngRestApiEndpoint()
    {
        return 'api-news-categories';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'parent_id' => Yii::t('app', 'Parent ID'),
            'create_date' => Yii::t('app', 'Create Date'),
            'update_date' => Yii::t('app', 'Update Date'),
            'slug' =>  Yii::t('app', 'Slug'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['parent_id'], 'integer'],
            [['create_date', 'update_date'], 'safe'],
            [['name', 'slug'], 'string', 'max' => 255],
        ];
    }
    

    /**
     * @inheritdoc
     */
    public function ngRestAttributeTypes()
    {
        return [
            'name' => 'text',
            'parent_id' => [
                'selectModel', 
                'modelClass' => Categories::className(), 
                 'valueField' => 'id', 
                 'labelField' => 'name',
             ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function ngRestScopes()
    {
        return [
            ['list', ['name', 'parent_id']],
            [['create', 'update'], ['name', 'parent_id']],
            ['delete', true],
        ];
    }

    public function filterCategoriesByParent($categories, $parentId){
        return array_filter($categories, function ($category) use ($parentId) {
            return $category->parent_id == $parentId;
        });
    }

    public static function getRecusiveCategories($depth, $sort = [])
    {
        if(!$sort){
            $sort = ['parent_id' => SORT_ASC, 'name' => SORT_ASC];
        }

        $categories = self::find()->orderBy($sort)->all();

        return self::getChilds($categories, 0, 1, $depth, $sort);
    }

    public static function getChilds($categories, $parentId, $level, $depth, $sort)
    {
        $cats = self::filterCategoriesByParent($categories, $parentId);
        
        $childs = [];
        foreach($cats as $cat){
            $childs[] = [
                'id' => $cat->id,
                'name' => $cat->name,
                'childs' => (!$depth || $level < $depth)?self::getChilds($categories, $cat->id, $level+1, $depth, $sort):[]
            ];
        }

        $level++;

        return $childs;
    }

    public static function getCategories($depth = 0, $sort = [])
    {
        $cats = Categories::getRecusiveCategories($depth, $sort);
        $catsArr = [];
        
        foreach($cats as $cat){
            $prefix = '';
            $level = 1;
            $catsArr[] = [
                'label' => $prefix.$cat['name'],
                'name' => $cat['name'],
                'id' => $cat['id'],
                'level' => $level
            ];

            if($cat['childs']){
                self::getCatChildren($cat['childs'], $catsArr, $level, $prefix);
            }
        }

        return $catsArr;
    }

    public static function getCatChildren($cats, &$catsArr, $level, $prefix){
        $prefix = '-'.$prefix;
        $level++;

        foreach($cats as $cat){
            $catsArr[] = [
                'label' => $prefix.$cat['name'],
                'name' => $cat['name'],
                'id' => $cat['id'],
                'level' => $level
            ];

            if($cat['childs']){
                self::getCatChildren($cat['childs'], $catsArr, $level, $prefix);
            }
        }
    }

    public function getUrl($data = null){
        $name = $this->name;
        $id = $this->id;

        if($data !== null){
            $name = $data['name'];
            $id = $data['id'];
        }

        return \yii\helpers\Url::base().'/category/'.\app\components\Common::slugify($name).'-'.$id;
    }

    public static function getCateIds(Array $catSlugs){
        $cats = self::find()->where(['slug' => $catSlugs])->all();
        return array_map(function($cat){return $cat->id;}, $cats);
    }
}