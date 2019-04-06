<?php

namespace  app\modules\news\admin\apis;

use Yii;
use app\modules\news\models\Menus;
use app\modules\news\models\MenuItems;
use app\modules\news\models\MenuPositions;

/**
 * MenuController Controller.
 *
 * Config for module
 */
class MenuController extends \luya\admin\base\RestController
{
    /**
     * Get text editor config from module config
     * @return array
     */
    public function actionGet() {
        set_time_limit(120);

        $pages = [];
        $navItems = \Yii::$app->menu->find()->container('default')->all();

        foreach($navItems as $item){
            $pages[] = [
                'id' => $item->id,
                'name' => $item->title,
                'url' => $item->link,
                'checked' => false
            ];
        }

        $categoryModel = new \app\modules\news\models\Categories;
        $categories = $categoryModel->getCategories();
        $positions = Menus::getPositionsData();

        foreach($categories  as $i => $category){
            $categories[$i]['checked'] = false;
            $categories[$i]['url'] = $categoryModel->getUrl($categories[$i]);
        }

        $menu = [
            'id' => 0,
            'name' => '',
            'items' => [],
            'positions' => $positions
        ];

        $menus = Menus::getAll();
        if($menus){
            $menu = $menus[0];
        }

        return ['pages' => $pages, 'categories' => $categories, 'menus' => $menus, 'menu' => $menu, 'positions' => $positions];
    }

    public function actionPost(){
        set_time_limit(120);

        $data = @json_decode(file_get_contents('php://input'), true);
        
        if(!$data){
            Yii::$app->response->statusCode = 422;
            Yii::$app->response->statusText = 'Validate Data Fail';
            return [];
        }

        $menuId = (int)$data['id'];
        $menuName = (string)$data['name'];
        $items = $data['items'];
        $positions = $data['positions'];

        $model = '';
        if($data['id']){
            $model = Menus::findOne($menuId);
        }
        if(!$model){
            $model = new Menus;
            $model->id = $menuId;
        }

        $model->name = $menuName;

        if(!$model->save()){
            $errors = [];
            foreach($model->getErrors() as $field => $error){
                $errors[] = ['field' => $field, 'message' => $error[0]];
            }

            Yii::$app->response->statusCode = 422;
            Yii::$app->response->statusText = 'Validate Data Fail';
            return $errors;
        }

        $savedItems = [];
        $sorder = 1;
        $parentId = 0;

        /*
         cat - 1 
         cat1 -1
           catt2 - 3
          cat 3 - 2
           cat - 1
        */
        
        $removedIds = [];
        $i = 0;
        $removedItems = array_filter($items, function ($item) {
            return isset($item['action']) && $item['action'] == 'remove';
        });

        $removedIds = array_map(function($item){return $item['id'];}, $removedItems);
        $removedItems = null;
        
        $items = array_filter($items, function ($item) {
            return !isset($item['action']) || $item['action'] != 'remove';
        });

        foreach($items as $item){
            if(isset($item['action']) && $item['action'] == 'remove'){
                continue;
            }

            $items[$i]['originLevel'] = $item['level'];

            $menuItem = $this->loadMenuItem($model->id, $item);
            $menuItem->sort_order = $sorder;

            if($i == 0 || $item['level'] <= 1){
                $menuItem->parent_id = 0;
                $item['level'] = 1;
            }else{
                for($j=$i-1; $j>=0; $j--){
                    $depth = $item['level'] - $items[$j]['originLevel'];
                    if($depth > 0){
                        $parentId = $items[$j]['id'];
                        $menuItem->parent_id = $items[$j]['id'];
                        $item['level'] = $items[$j]['level'] + 1;
                        break;
                    }
                }
            }

            if($menuItem->save(false)){
                $savedIds[] = $menuItem->id;
                $item['id'] = $menuItem->id;
                $savedItems[] = $item;
                $sorder++;
            }

            $i++;
        }

        //Delete Removed Id;
        if($removedIds)
            MenuItems::deleteAll('id in ('.implode(',', $removedIds).') and menu_id = '.$model->id);

        //Hanlde menus
        $savedIds = [0];
        foreach($positions as $position){
            if(!isset($position['checked']) || !$position['checked'])
                continue;

            $menuPosition = MenuPositions::find()->where(['menu_id' => $model->id, 'position' => $position['id']])->one();
            if(!$menuPosition){
                $menuPosition = new MenuPositions;
                $menuPosition->attributes = [
                    'menu_id' => $model->id, 
                    'position' => $position['id']
                ];
            }

            if($menuPosition->save(false)){
                $savedIds[] = $menuPosition->id;
            }
        }

        //Delete Removed Id;
        MenuPositions::deleteAll('id not in ('.implode(',', $savedIds).') and menu_id = '.$model->id);

        return ['id' => $model->id, 'name' => $model->name, 'items' => $savedItems, 'positions' => $positions];
    }

    public function loadMenuItem($menuId, $item){
        if($item['id'])
            $model = MenuItems::findOne((int)$item['id']);

        if(!isset($model) || !$model){
            $model = new MenuItems;
        }

        $model->attributes = [
            'menu_id' => $menuId,
            'name' => $item['name'],
            'url' => $item['url'],
            'object_id' =>  $item['objectId'],
            'object_type' => $item['type']
        ];

        return $model;
    }

    public function actionDelete(){
        $id = Yii::$app->request->get('id');

        $connection = Yii::$app->getDb();
        $command = $connection->createCommand("DELETE m, mi, p FROM news_menus m 
                LEFT JOIN news_menu_items mi ON mi.menu_id = m.id 
                LEFT JOIN news_menu_positions p ON p.menu_id = m.id 
                WHERE m.id = ".($id));

        $result = $command->execute();
        $connection->close();

        return ['id' => $id];
    }
}