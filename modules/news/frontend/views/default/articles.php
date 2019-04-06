<?php
use yii\widgets\LinkPager;
use yii\widgets\Pjax;
use luya\admin\filters\MediumCrop;
use luya\lazyload\LazyLoad;

$articles = $provider->models;
if($articles):
    $firstArticle = $articles[0];
    $articles = array_slice($articles, 1);

    $i = 0;
?>
<div class="row mb-10" id="article-slide">
    <div class="col-lg-6 col-md-6 article-first-item highlight">
        <a class="thumb" href="<?= $firstArticle->getLink()?>">
            <?= LazyLoad::widget(['src' => $firstArticle->getImage()->source, 'extraClass' => 'img-responsive'])?>
        </a>
        <a class="title" href="<?= $firstArticle->getLink()?>"><h2><?= $firstArticle->title; ?></h2></a>
        <p class="datetime"><small><?= strftime('%d/%m/%Y %I:%M %p', strtotime($firstArticle->create_date)); ?></small></p>
        <p class="summry"><?= $firstArticle->getSummary()?></p>
    </div>
    <div class="col-lg-6 col-md-6">
    <?php while($articles): ?>
        <?php $item = $articles[0]?>
        <?php if ($item->image_name): ?>
        <div class="row mb-10 article-item">
            <div class="col-lg-4 col-md-4  col-sm-4 col-xs-4">
                <a class="thumb" href="<?= $item->getLink()?>">
                    <?= LazyLoad::widget(['src' => $item->getImage()->source, 'extraClass' => 'img-responsive'])?>
                </a>
            </div>
            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                <a class="title" href="<?= $item->getLink()?>"><?= $item->titleForDisplay(); ?></a>
                <p class="datetime"><small><?= $item->getFormatedDate()?></small></p>
            </div>
        </div>
        <?php endif?>
        <?php
        $i++;
        $articles = array_slice($articles, 1);
        if($i >= 4)
            break;
        ?>
    <?php endwhile; ?>
    </div>
</div>

<div class="row">
    <div class="row" id="articles-list">
    <?php foreach($articles as $item):?>
        <div class="col-lg-4 col-lg-4 col-sm-6 col-xs-12 article-item">
            <div class="col-lg-5 col-md-5 col-sm-4 col-xs-4">
                <a class="thumb" href="<?= $item->getLink()?>">
                    <?= LazyLoad::widget(['src' => $item->getImage()->source, 'extraClass' => 'img-responsive'])?>
                </a>
            </div>
            <div class="col-lg-7 col-md-7 col-sm-8 col-xs-8">
                <a class="title" href="<?= $item->getLink()?>"><?= $item->titleForDisplay()?></a>
                <p class="datetime"><small><?= $item->getFormatedDate()?></small></p>
            </div>
        </div>
    <?php endforeach?>
    </div>
</div>
<?php endif?>

<?php $this->registerJs('
var totalPage = '. (int)$provider->pagination->pageCount .';
var currentPage = '. (int)$provider->pagination->params['page'] .';
var maxPage = 10;

if(!currentPage)
    currentPage = 1;

if(currentPage < totalPage){
    var loading = false;
    
    $(window).on("scroll", checkOnWindowBottom);
}

function checkOnWindowBottom(){
    var scrollHeight = $(document).height();
    var scrollPosition = $(window).height() + $(window).scrollTop();
    if ((scrollHeight - scrollPosition) / scrollHeight === 0) {
        if(!loading)
            loadMoreArticles();
    }
}

function loadMoreArticles(){
    if(currentPage >= totalPage){
        $(window).off("scroll", checkOnWindowBottom);
        return;
    }

    if(currentPage % maxPage == 0){
        $(window).off("scroll", checkOnWindowBottom);
        $("#articles-list").parent().append("<p class=\"text-center\"><a href=\"'. yii\helpers\Url::base().$url."?page=" .'" + (currentPage+1) +"\">more</a></p>");
        return;
    }

    loading = true;

    $.ajax({
        url: document.base_url + "'.$url.'",
        type: "get",
        data: {page: currentPage + 1, ajax: 1},
        success: function(data){
            $("#articles-list").append(data);
            $.lazyLoad();
            loading = false;
            currentPage++; 
        },
        error: function(xhr){
            console.log(xhr.responseText);
            loading = false;
        }
    });
}', yii\web\View::POS_END)?>

