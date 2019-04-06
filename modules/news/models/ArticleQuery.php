<?php

namespace app\modules\news\models;

use Yii;

/**
 * This is the model class for table "news_article".
 *
 * @property int $id
 * @property string $title
 * @property string $content
 * @property string $image_name
 * @property int $status
 * @property string $source
 * @property int $create_user_id
 * @property int $update_user_id
 * @property string $create_date
 * @property string $update_date
 */
class ArticleQuery extends \yii\db\ActiveRecord
{
    public $slug;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'news_article';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['content'], 'string'],
            [['status', 'create_user_id', 'update_user_id'], 'integer'],
            [['create_date', 'update_date', 'source'], 'safe'],
            [['title', 'image_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'title' => Yii::t('app', 'Title'),
            'content' => Yii::t('app', 'Content'),
            'image_name' => Yii::t('app', 'Image'),
            'status' => Yii::t('app', 'Status'),
            'create_user_id' => Yii::t('app', 'Create User ID'),
            'update_user_id' => Yii::t('app', 'Update User ID'),
            'create_date' => Yii::t('app', 'Create Date'),
            'update_date' => Yii::t('app', 'Update Date'),
        ];
    }

    public static function getLastArticles(int $limit = 20){
        return self::find()->where(['status' => 1])->orderBy(['create_date' => 'desc'])->limit($limit)->all();
    }

    /**
     * Get image object.
     * 
     * @return \luya\admin\image\Item|boolean
     */
    public function getImage()
    {
        $image = new \stdClass();
        $image->source = \yii\helpers\Url::base().'/images/placeholder-image.png';
        $storagePath = Yii::$app->storage->folderName;

        if($this->image_name)
            $image->source = \yii\helpers\Url::base(true).'/'.Yii::$app->storage->folderName.'/'.$this->image_name; 

    	return $image;
    }

    public function getLink(){
        return \yii\helpers\Url::home().$this->getSlug().'-'.$this->id;
    }

    public function getSummary($maxLen = 250){
        $content = strip_tags($this->content);
        if(strlen($content) <= $maxLen){
            return $content;
        }

        $content = trim(substr(strip_tags($this->content), 0, $maxLen));
        $content = substr($content, 0, strrpos($content, ' '));

        return trim($content, '.').'...';;
    }

    public function getAuthor(){
        $user = \luya\admin\models\User::findOne($this->create_user_id);

        return $user?"{$user->firstname} {$user->lastname}":"";
    }

    public function getFormatedDate(){
        $timestamp = strtotime($this->create_date);

        return vsprintf(strftime('%d %%s, %Y %I:%M %%s', $timestamp), [Yii::t('app', strftime('%B', $timestamp)), Yii::t('app', strftime('%p', $timestamp))]);
    }

    public function getSlug(){
        return \app\components\Common::slugify($this->title);
    }

    public function titleForDisplay(){
        if(strlen($this->title) <= 75)
            return $this->title;

        return trim(substr($this->title, 0, 80), '.').' ...';
    }
}
