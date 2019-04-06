<?php

namespace  app\modules\news\admin\plugins;

use luya\admin\helpers\Angular;
use luya\admin\models\Config;
use luya\admin\ngrest\base\Plugin;

/**
 * Wysiwyg Plugin.
 *
 * @author andrzej
 * @since 1.0.0
 */
class UploadPlugin extends Plugin
{
    public $imageItem = false;

    public function renderList($id, $ngModel)
    {
        $this->createListTag($ngModel);
    }
    
    public function renderUpdate($id, $ngModel)
    {
        return $this->renderCreate($id, $ngModel);
    }
    
    public function renderCreate($id, $ngModel)
    {
        return $this->createFormTag('zaa-upload-image', $id, $ngModel);
        //return Angular::directive('upload-image', ['model' => $ngModel, 'label' => $this->alias, 'data' => $this->getServiceName('data')]);
    }
    
    public function serviceData($event)
    {
        return [
            'data' => [
                // some data we always want to expose to the directive,
            ],
        ];
    }
    /**
     * @inheritDoc
     */
    public function onAfterFind($event)
    {
        if ($this->imageItem) {
            $this->writeAttribute($event, Yii::$app->storage->getImage($event->sender->getAttribute($this->name)));
        }
    }
}