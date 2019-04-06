<?php

namespace app\modules\news\models;

use Yii;
use luya\admin\ngrest\base\NgRestModel;
use luya\admin\traits\SoftDeleteTrait;
use yii\behaviors\SluggableBehavior;

/**
 * Article.
 * 
 * File has been created with `crud/create` command. 
 *
 * @property integer $id
 * @property string $title
 * @property text $content
 * @property string $image_name
 * @property tinyint $status
 * @property string $source
 * @property integer $create_user_id
 * @property integer $update_user_id
 * @property datetime $create_date
 * @property datetime $update_date
 */
class Article extends NgRestModel
{
    use SoftDeleteTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'news_article';
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
        $this->update_user_id = Yii::$app->adminuser->getId();
        $this->update_date = date('Y-m-d H:i:s');
        $this->addImage(false);
    }
    
    public function eventBeforeInsert()
    {
        $this->create_user_id = Yii::$app->adminuser->getId();
        $this->update_user_id = Yii::$app->adminuser->getId();
        $this->update_date = date('Y-m-d H:i:s');
        $this->create_date = date('Y-m-d H:i:s');
        $this->addImage();
    }

    public function addImage($insert){
        $tmpFile = Yii::getAlias('@webroot').'/tmp/'.$this->image_name;
        $filename = explode('/', $this->image_name);

        if(!$this->image_name || count($filename) > 1 || !file_exists($tmpFile))
            return;

        $storagePath = Yii::$app->storage->getServerPath();
        $folder = date('Y').'-'.date('m');
        $uploadsDir = \app\components\Utils::getUploadDir($storagePath, $folder);
        
        if(copy($tmpFile, $uploadsDir.'/'.$this->image_name)){  
            $this->image_name = $folder.'/'.$this->image_name;
            if(!$insert){
                $old = self::findOne((int)$this->id);
                if($old && $old->image_name && $old->image_name != $this->image_name)
                    @unlink(Yii::$app->storage->folderName.'/'.$old->image_name);
            }
        }
        
        @unlink($tmpFile);
    }

    public function fieldStateDescriber()
    {
        return [
            'status' => [2, [0,1]], // on delete sets `status = 1`; on find add where `where(['status' => 0]);`.
        ];
    }

    public function ngRestFilters()
    {
        $cats = Categories::getCategories();
        $filters = [];
        foreach($cats as $cat){
            $filters[$cat['label']] =  self::find()->alias('a')
                            ->andWhere(['c.category_id' => $cat['id']])
                            ->join('INNER JOIN', 'news_article_category c', 'a.id = c.article_id');
        }
        return $filters;
    }

    public static function ngRestFind()
    {
        return parent::ngRestFind()->where(['status' => [0,1]]);
    }

    public static function find()
    {
        return parent::find()->where(['status' => [0,1]]);
    }

    /* Save categories */
    public function afterSave($insert, $changedAttributes)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'PUT') {
           return;
        }
        
        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body, true);
        
        if(isset($data['categories'])){
            $categories = $data['categories'];
            if($categories){
                $catIds = array_map(function($cat){return $cat['value'];}, $categories);
                $categories = ArticleCategory::find()->where(['article_id' => (int)$this->id])->select(['category_id'])->all();

                $addes = [];
                $removes = [];
                if(!$insert){
                    foreach($categories as $cat){
                        if(in_array($cat->category_id, $catIds)){
                            $addes[] = $cat->category_id;
                        }else{
                            $removes[] = $cat->category_id;
                        }
                    }

                    if($removes){
                        ArticleCategory::deleteAll(['article_id' => (int)$this->id, 'category_id' => $removes]);
                    }
                }

                foreach($catIds as $catId){
                    if($insert || !in_array($catId, $addes)){
                        $model = new ArticleCategory;
                        $model->attributes = [
                            'category_id' => $catId,
                            'article_id' => $this->id
                        ];
                        $model->save();
                    }
                }
            }elseif(!$insert){
                ArticleCategory::deleteAll(['article_id' => (int)$this->id]);
            }
        }

        parent::afterSave($insert, $changedAttributes);        
    }

    /**
     * @inheritdoc
     */
    public static function ngRestApiEndpoint()
    {
        return 'api-news-article';
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
            'image_name' => Yii::t('app', 'Image'),
            'status' => Yii::t('app', 'Status'),
            'create_user_id' => Yii::t('app', 'Create User'),
            'update_user_id' => Yii::t('app', 'Update User'),
            'create_date' => Yii::t('app', 'Create Date'),
            'update_date' => Yii::t('app', 'Update Date'),
            'categories' => Yii::t('app', 'Categories'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['content'], 'string'],
            [['status', 'create_user_id', 'update_user_id'], 'integer'],
            [['create_date', 'update_date', 'categories', 'source'], 'safe'],
            [['title', 'image_name'], 'string', 'max' => 255],
        ];
    }

    public function getListCategories(){
        return $this->id?$this->getCatsName($this->id):'';
    }

    public function getCategories(){
        return $this->categories;
    }

    public function setCategories($value){
        $this->categories = $value;
    }

    public function getSlug(){
        return \app\components\Common::slugify($this->title);
    }

    /* Add extra fields */
    public function extraFields()
    {
        return [
            'slug',
            'listCategories', 
            'categories' => function ($model) {
                if(!$model->id) return [];
                return $this->getCatsName($model->id, true);
            },
            'status' => function ($model) {
                return 'satus';
            }
        ];
    }

    public function getCatsName($articleId, $returnArr = false){
        $categories = Categories::find()
                    ->alias('c')
                    ->join('inner join', 'news_article_category ac', 'ac.category_id = c.id')
                    ->where(['ac.article_id' => (int)$articleId])
                    ->orderBy(['parent_id' => 'asc', 'name' => 'asc'])
                    ->all();

        if($returnArr){
            $cats = [];
            foreach($categories as $category){
                $cats[] = [
                    'value' => $category->id,
                    'label' => $category->name,
                ];
            }

            return $cats;
        }

        $catsName = '';
        foreach($categories as $category){
            $catsName .= $catsName?', ':'';
            $catsName .= $category->name;
        }

        return $catsName;
    }

    /**
     * Register Extra fields for angular
     * @inheritdoc
     */
    public function ngRestExtraAttributeTypes()
    {
        return [
            'listCategories' => 'text',
            'categories' => [
                'sortRelationModel',
                'modelClass' => Categories::className(),
                'valueField' => 'id',
                'labelField' => 'name'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function ngRestAttributeTypes()
    {
        return [
            'categories' => [
                'sortRelationModel',
                'modelClass' => Categories::className(),
                'valueField' => 'id',
                'labelField' => 'name'
            ],
            'title' => 'text',
            'content' => [
                'class' => \app\modules\news\admin\plugins\WysiwygPlugin::className()
            ],
            'image_name' => [
                'class' => \app\modules\news\admin\plugins\UploadPlugin::className()
            ],
            'status' => 'toggleStatus',
            'create_user_id' => 'number',
            'update_user_id' => 'number',
            'create_date' => 'text',
            'update_date' => 'text'
        ];
    }

    /**
     * @inheritdoc
     */
    public function ngRestScopes()
    {
        return [
            ['list', ['listCategories', 'title', 'status', 'create_user_id', 'create_date']],
            [['create', 'update'], ['categories', 'title', 'content', 'image_name', 'status']],
            ['delete', true],
        ];
    }
}