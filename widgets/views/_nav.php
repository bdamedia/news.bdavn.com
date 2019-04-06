<?php
use app\modules\news\models\Menus;
use app\modules\news\models\MenuItems;
?>

<ul class="menu-top">
<?php 
$totalItems = count($rootItems);
?>
<?php foreach($rootItems as $item):?>
    <?php $children = Menus::filterItemsByParent($items, $item->id);?>
    <?php if(count($children)):?>
    <li class="<?= ($totalItems > 11 && $i >= 11)?'dropdown-submenu':'dropdown'?> nav-item">
        <a class="" href="<?= $item->url; ?>">
            <?= $item->name; ?> 
        </a>
        <ul class="top-submenu">
            <?php foreach($children as $item):?>
                <?= $this->render('_item', ['items' => $items, 'item' => $item])?>
            <?php endforeach?>
        </ul>
    </li>  
    <?php else:?>
    <li class="menu-item">
        <a class="item-link" href="<?= $item->url; ?>"><?= $item->name; ?></a>
    </li>
    <?php endif?>
<?php endforeach; ?>
</ul> 

<?php 
function showSubNav($items, $item){
    $children = Menus::filterItemsByParent($items, $item->id);
    if(count($children)){
        echo '<li class="menu-item">
                <a href="'.$item->url.'" class="item-link">'.$item->name.'</a>
                <ul class="top-submenu">';

        foreach($children as $child){
            showSubNav($items, $child);
        }
        
        echo '  </ul>
            </li>';

        return;
    }

    echo '<li class="menu-item"><a class="item-link" href="'.$item->url.'">'.$item->name.'</a></li>';
}
?>