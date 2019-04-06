<?php
namespace app\widgets;

use yii\base\Widget;
use yii\helpers\Html;
use app\modules\news\models\Menus;
use app\modules\news\models\MenuItems;

class TopNavWidget extends Widget
{
    public function init()
    {
        parent::init();
    }

    public function run()
    {
        $menu = Menus::find()->alias('m')
            ->join('INNER JOIN', 'news_menu_positions p', 'p.menu_id = m.id')
            ->where(['p.position' => Menus::POS_TOP])->one();
        
        if(!$menu) return '';

        $items = Menus::getAllItems($menu->id);
        $rootItems = Menus::filterItemsByParent($items, 0);

        return $this->render('_nav', ['rootItems' => $rootItems, 'items' => $items]);
    }


}
?>