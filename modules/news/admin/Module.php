<?php

namespace app\modules\news\admin;

/**
 * News Admin Module.
 *
 * File has been created with `module/create` command. 
 * 
 * @author
 * @since 1.0.0
 */
class Module extends \luya\admin\base\Module
{
    public $apis = [
        'api-news-article' => 'app\modules\news\admin\apis\ArticleController',
        'api-news-categories' => 'app\modules\news\admin\apis\CategoriesController',
        'api-luyawisywyg-config' => 'app\modules\news\admin\apis\LuyawysiwygConfigController',
        'api-news-ads' => 'app\modules\news\admin\apis\AdsController',
        'api-menu' => 'app\modules\news\admin\apis\MenuController'
    ];
    
    public function getMenu()
    {
        return (new \luya\admin\components\AdminMenuBuilder($this))
            ->nodeRoute('Menus', 'note_add', 'newsadmin/menu/index')
            ->node('News', 'extension')
                ->group('News')
                    ->itemApi('Article', 'newsadmin/article/index', 'label', 'api-news-article')
                    ->itemApi('Categories', 'newsadmin/categories/index', 'label', 'api-news-categories')
                    ->itemApi('Ads', 'newsadmin/ads/index', 'label', 'api-news-ads');
    }

    /**
     * @inheritdoc
     */
    public function getAdminAssets() {
        return [
            '\app\modules\news\admin\assets\MainAssets',
            '\app\modules\news\admin\assets\WysiwygAssets',
        ];
    }

    public function extendPermissionApi(){
        return [
            //['api' => 'api-menu', 'alias' => 'Menu'],
        ];
    }

    public function extendPermissionRoutes()
    {
        return [
            //['route' => 'newsadmin/menu/create', 'alias' => 'Test Create'],
        ];
    }

    public static function t($text){
        return \Yii::t('app', $text);
    }
}