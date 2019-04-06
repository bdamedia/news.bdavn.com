<?php

namespace app\modules\news\admin\controllers;

use luya\admin\base\Controller;

class MenuController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index', [
            
        ]);
    }

    public function actionCreate(){
        return $this->render('create', [
            
        ]);
    }
}