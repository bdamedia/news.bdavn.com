<?php

namespace app\modules\news\admin\apis;

use Yii;
use yii\web\UploadedFile;
use yii\helpers\Url;

/**
 * Article Controller.
 * 
 * File has been created with `crud/create` command. 
 */
class ArticleController extends \luya\admin\ngrest\base\Api
{
    /**
     * @var string The path to the model which is the provider for the rules and fields.
     */
    public $modelClass = 'app\modules\news\models\Article';

    public function actionUpload(){
        $file = UploadedFile::getInstanceByName('file');        
        $ext = $file->getExtension();
        
        if($file->error)
            return;

        $fileName = \app\components\Common::slugify(str_replace(".{$ext}", '', $file->name)).'.'.time().".{$ext}";
        if(!$file->saveAs(\Yii::getAlias('@webroot').'/tmp/'.$fileName))
            return [];

        return ['name' =>  $fileName, 'ext' => $ext, 'source' => Url::base().'/tmp/'.$fileName];
    }

    public function actionGetImage(){
        $image = new \stdClass();
        $image->source = '';
        $storagePath = Yii::$app->storage->folderName;
        $imageName = Yii::$app->request->get('name');
        
        if($imageName)
            $image->source = \yii\helpers\Url::base().'/'.Yii::$app->storage->folderName.'/'.$imageName; 

    	return $image; 
    }
}

//{"file":{"name":"CHAYANPHU-1551596551-9495-1551596893.jpg","type":"image/jpeg","tmp_name":"D:\\xampp\\tmp\\phpAA4A.tmp","error":0,"size":402475}}