<?php
use app\modules\news\models\Menus;

$children = Menus::filterItemsByParent($items, $item->id);
if(count($children)){
    echo '<li class="dropdown-submenu nav-item">
            <a href="'.$item->url.'" class="dropdown-toggle disabled" data-toggle="dropdown">'.$item->name.'</a>
            <ul class="dropdown-menu">';

    foreach($children as $item){
        echo $this->render('_item', ['items' => $items, 'item' => $item]);
    }
    
    echo '  </ul>
        </li>';

    return;
}

echo '<li><a href="'.$item->url.'">'.$item->name.'</a></li>';