<?php
use yii\widgets\LinkPager;
use luya\lazyload\LazyLoad;
use app\components\Common;
?>

<?php if($panel):?>
<div class="panel panel-default cat-panel" id="cat-panel-<?= $category['id']?>" data-id="<?= $category['id']?>">
    <div class="panel-heading">
    <?= yii\helpers\Html::a($category['name'], ['/category/'.Common::slugify($category['name']).'-'.$category['id']], ['data-pjax' => 0])?>
    </div>
    <div class="panel-body">
        <div class="row">
        <?php foreach($provider->models as $item):?>
            <div class="col-lg-4 col-lg-4 col-sm-6 col-xs-12 article-item">
                <div class="col-lg-5 col-md-5 col-sm-4 col-xs-4">
                    <a class="thumb" href="<?= $item->getLink()?>" data-pjax="0">
                        <?= LazyLoad::widget(['src' => $item->getImage()->source, 'extraClass' => 'img-responsive'])?>
                    </a>
                </div>
                <div class="col-lg-7 col-md-7 col-sm-8 col-xs-8">
                    <a class="title" href="<?= $item->getLink()?>" data-pjax="0"><?= $item->titleForDisplay(); ?></a>
                    <p class="datetime"><small><?= $item->getFormatedDate() ?></small></p>
                </div>
            </div>
        <?php endforeach?>
        </div>
    </div>
    <?php if($provider->pagination->pageCount > 1):?>
    <div class="panel-footer">
        <?= LinkPager::widget(['pagination' => $provider->pagination]); ?>
    </div>
    <?php endif?>
</div>
<?php else:?>
<?php foreach($provider->models as $item):?>
    <div class="col-lg-4 col-lg-4 col-sm-6 col-xs-12 article-item">
        <div class="col-lg-5 col-md-5 col-sm-4 col-xs-4">
            <a class="thumb" href="<?= $item->getLink()?>">
                <?= LazyLoad::widget(['src' => $item->getImage()->source, 'extraClass' => 'img-responsive'])?>
            </a>
        </div>
        <div class="col-lg-7 col-md-7 col-sm-8 col-xs-8">
            <a class="title" href="<?= $item->getLink()?>"><?= $item->titleForDisplay(); ?></a>
            <p class="datetime"><small><?= $item->getFormatedDate() ?></small></p>
        </div>
    </div>
<?php endforeach?>
<?php endif?>