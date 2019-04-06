<?php
use app\modules\news\models\Menus;
use app\modules\news\models\MenuItems;
?>
<!-- Navigation -->
<nav class="navbar navbar-default" role="navigation">
    <div class="container">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#"></a>
        </div>
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
            <?php 
            $i = 0;
            $totalItems = count($rootItems);
            ?>
            <?php foreach($rootItems as $item):?>
                <?php
                $active = '';
                if($_SERVER['REQUEST_URI'] == $item->url)
                    $active = 'active';
                ?>
                <?php if($totalItems > 11 && $i == 11):?>
                <li class="dropdown nav-item">
                    <a class="dropdown-toggle nav-link disabled" href="javascript:void(0)" data-toggle="dropdown">
                        <?= Yii::t('app', 'More') ?> <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu multi-level">
                <?php endif?>

                <?php $children = Menus::filterItemsByParent($items, $item->id);?>
                <?php if(count($children)):?>
                <li class="<?= ($totalItems > 11 && $i >= 11)?'dropdown-submenu':'dropdown'?> nav-item <?=$active?>">
                    <a class="dropdown-toggle nav-link disabled" href="<?= $item->url; ?>" data-toggle="dropdown">
                        <?= $item->name; ?> 
                        <?php if($totalItems <= 11 || $i < 11):?>
                        <b class="caret"></b>
                        <?php endif?>
                    </a>
                    <ul class="dropdown-menu multi-level">
                        <?php foreach($children as $item):?>
                        <?= $this->render('_item', ['items' => $items, 'item' => $item])?>
                        <?php endforeach?>
                    </ul>
                </li>  
                <?php else:?>
                <li class="nav-item <?=$active?>">
                    <a class="nav-link" href="<?= $item->url; ?>"><?= $item->name; ?></a>
                </li>
                <?php endif?>
                <?php $i++?>
            <?php endforeach; ?>
            <?php if($totalItems > 11):?>
                    </ul>
                </li>
            <?php endif?>
            </ul>        
        </div>
        <!-- /.navbar-collapse -->
    </div>
    <!-- /.container -->
</nav>

