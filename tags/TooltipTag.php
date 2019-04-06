<?php
namespace app\tags;

use luya\tag\BaseTag;

class TooltipTag extends BaseTag
{
    public $position = 'left';
    
    public function example()
    {
        return 'tooltip[Text](Overlay Tooltip Text)'; 
    }
    
    public function readme()
    {
        return '';
    }
    
    public function parse($value, $sub)
    {
        //\Yii::$app->view->registerJs('$(document).ready(function(){ $(\'[data-toggle="tooltip"]\').tooltip(); });');
        return '<span data-toggle="tooltip" data-placement="'.$this->position.'" title="'.$sub.'">'.$value.'</span>';
    }
}