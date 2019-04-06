<?php

namespace app\modules\news\admin\apis;

/**
 * Ads Controller.
 * 
 * File has been created with `crud/create` command. 
 */
class AdsController extends \luya\admin\ngrest\base\Api
{
    /**
     * @var string The path to the model which is the provider for the rules and fields.
     */
    public $modelClass = 'app\modules\news\models\Ads';
}