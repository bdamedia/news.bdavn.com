<?php

namespace app\modules\news\admin\assets;

class MainAssets extends \luya\web\Asset
{
    public $sourcePath = '@newsadmin/resources';

    public $js = [
        'js/angular-animate.min.js',
        'js/drag.js',
        'js/menu.js',
    ];

    public $css = [
        'css/bootstrap.min.css',
        'css/menu.css',
    ];

    // important to solve all javascript dependency issues here, e.g. jquery, bower, angular, ...
    public $depends = [
        'luya\admin\assets\Main',
    ];
}