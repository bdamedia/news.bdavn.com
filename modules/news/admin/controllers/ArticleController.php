<?php

namespace app\modules\news\admin\controllers;

/**
 * Article Controller.
 * 
 * File has been created with `crud/create` command. 
 */
class ArticleController extends \luya\admin\ngrest\base\Controller
{
    /**
     * @var string The path to the model which is the provider for the rules and fields.
     */
    public $modelClass = 'app\modules\news\models\Article';
}