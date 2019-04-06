<?php
use app\modules\news\models\Menus;

$children = Menus::filterItemsByParent($items, $item->id);
if(count($children)){
    echo '<li class="menu-item">
            <a href="'.$item->url.'" class="item-link">'.$item->name.'</a>
            <ul class="top-submenu">';

    foreach($children as $child){
        echo $this->render('_item', ['items' => $items, 'item' => $item]);
    }
    
    echo '  </ul>
        </li>';

    return;
}

echo '<li class="menu-item"><a class="item-link" href="'.$item->url.'">'.$item->name.'</a></li>';