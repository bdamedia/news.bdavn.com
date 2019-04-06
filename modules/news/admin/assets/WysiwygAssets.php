<?php

namespace app\modules\news\admin\assets;

/**
 * Asset file for tinymce
 */
class WysiwygAssets extends \luya\web\Asset
{
    public $sourcePath = '@newsadmin/resources';

    public $js = [
        'js/directives.js',
        //'js/langs/pl.js',
    ];

    public $depends = [
        'luya\admin\assets\Main',
        '\app\modules\news\admin\assets\TinymceAssets'
    ];
}