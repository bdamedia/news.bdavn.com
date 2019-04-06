<?php

namespace  app\modules\news\admin\apis;

/**
 * LuyawysiwygConfigController Controller.
 *
 * Config for module
 */
class LuyawysiwygConfigController extends \luya\admin\base\RestController
{
    /**
     * Get text editor config from module config
     * @return array
     */
    public function actionGet() {
        $mod = \Yii::$app->getModule('newsadmin');
        return $mod->textEditorOptions;
    }
}