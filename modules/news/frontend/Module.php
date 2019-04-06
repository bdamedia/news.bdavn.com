<?php

namespace app\modules\news\frontend;

/**
 * News Admin Module.
 *
 * File has been created with `module/create` command. 
 * 
 * @author
 * @since 1.0.0
 */
class Module extends \luya\base\Module
{
    public $articleDefaultOrder = ['create_date' => SORT_DESC];
    
    /**
     * @var integer Default number of pages.
     */
    public $articleDefaultPageSize = 20;

    /**
     * @inheritdoc
     */
    public $urlRules = [
        ['pattern' => 'category/<slug:[^/]+>-<catId:\d+>', 'route' => 'news/default/articles'],
        ['pattern' => 'category', 'route' => 'news/default/articles'],
        ['pattern' => '<slug:[^/]+>-<id:\d+>', 'route' => 'news/default/article-detail'],
        ['pattern' => 'news/load-more', 'route' => 'news/default/load-more'],
        ['pattern' => 'news/<action:\w+>', 'route' => 'news/default/<action>'],
    ];

}