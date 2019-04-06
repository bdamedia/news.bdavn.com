<?php

namespace app\modules\news\admin\apis;

/**
 * Categories Controller.
 * 
 * File has been created with `crud/create` command. 
 */
class CategoriesController extends \luya\admin\ngrest\base\Api
{
    /**
     * @var string The path to the model which is the provider for the rules and fields.
     */
    public $modelClass = 'app\modules\news\models\Categories';
}