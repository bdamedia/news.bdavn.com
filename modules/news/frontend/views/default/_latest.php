<?php
use yii\widgets\LinkPager;
use yii\widgets\Pjax;
use luya\admin\filters\MediumCrop;
use luya\lazyload\LazyLoad;

/* @var $this \luya\web\View */
/* @var $provider \yii\data\ActiveDataProvider */
$articles = $provider->models;
if($articles):
    $firstArticle = $articles[0];
    unset($articles[0]);
?>
<?php Pjax::begin(['id' => 'lastest']); ?>
<div class="row">
    <div class="col-lg-6 col-md-6 article-first-item highlight">
        <a class="thumb" data-pjax=0 href="<?= $firstArticle->getLink()?>">
            <?= LazyLoad::widget(['src' => $firstArticle->getImage()->source, 'extraClass' => 'img-responsive'])?>
        </a>
        <a class="title" data-pjax=0 href="<?= $firstArticle->getLink()?>"><h2><?= $firstArticle->title; ?></h2></a>
        <p class="datetime"><small><?= $firstArticle->getFormatedDate()?></small></p>
        <p class="summry"><?= $firstArticle->getSummary()?></p>
    </div>
    <div class="col-lg-6 col-md-6">
    <?php foreach($articles as $item): ?>
        <?php /** @var news\models\Article $item */ ?>
        <?php if ($item->image_name): ?>
        <div class="row mb-10 article-item">
            <div class="col-lg-4 col-md-4  col-sm-4 col-xs-4">
                <a class="thumb" data-pjax=0 href="<?= $item->getLink()?>">
                    <?= LazyLoad::widget(['src' => $item->getImage()->source, 'extraClass' => 'img-responsive'])?>
                </a>
            </div>
            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                <a class="title" data-pjax=0 href="<?= $item->getLink()?>"><?= $item->titleForDisplay(); ?></a>
                <p class="datetime"><small><?= $item->getFormatedDate()?></small></p>
            </div>
        </div>
        <?php endif?>
    <?php endforeach; ?>
    </div>
</div>
<?= LinkPager::widget(['pagination' => $provider->pagination]); ?>
<?php Pjax::end();?>
<?php endif?>

