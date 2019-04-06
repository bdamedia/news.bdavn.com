<?php 
use luya\lazyload\LazyLoad;
use app\modules\news\models\Ads;

$this->registerCssFile('https://use.fontawesome.com/releases/v5.8.1/css/all.css');
$articles = $provider->models;
$ads = Ads::getAds([Ads::POS_SIDEBAR, Ads::POS_INNER_NEWS]);
?>

<?php if($ads && isset($ads[Ads::POS_SIDEBAR])):?>
<div class="row">
    <div class="col-lg-8">
<?php endif?>
        <div class="article-detail mb-10">
            <h1 class="title"><?= $article->title?></h1>
            <p class="datetime"><small>By <strong><?= $article->getAuthor(); ?></strong> 
                <?= $article->getFormatedDate()?>
            </small></p>
            <?= \ymaker\social\share\widgets\SocialShare::widget([
                'configurator'  => 'socialShare',
                'url'           => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]",
                'title'         => $article->title,
                'description'   => $article->title,
                'imageUrl'      => $article->getImage()->source,
            ]); ?>
            <p class="text-center">
                <?= LazyLoad::widget(['src' => $article->getImage()->source, 'extraClass' => 'img-responsive'])?>
            </p>
            <div class="content"><?= $article->content?></div>
        </div>
<?php if($ads && isset($ads[Ads::POS_SIDEBAR])):?>
    </div>
    <div class="col-lg-4">
        <?= \app\modules\news\widgets\AdsWidget::widget([
            'ads' => $ads,
            'position' => Ads::POS_SIDEBAR,
            'className' => 'ads sidebar-ads text-center'
        ])?>
    </div>
</div>
<?php endif?>

<div class="row" id="articles-list">
<?php foreach($articles as $item):?>
    <div class="col-lg-4 col-lg-4 col-sm-6 col-xs-12 article-item">
        <div class="col-lg-5 col-md-5 col-sm-4 col-xs-4">
            <a class="thumb" href="<?= $item->getLink()?>">
                <?= LazyLoad::widget(['src' => $item->getImage()->source, 'extraClass' => 'img-responsive'])?>
            </a>
        </div>
        <div class="col-lg-7 col-md-7 col-sm-8 col-xs-8">
            <a class="title" href="<?= $item->getLink()?>"><?= $item->titleForDisplay(); ?></a>
            <p class="datetime"><small><?= $item->getFormatedDate()?></small></p>
        </div>
    </div>
<?php endforeach?>
</div>


<?php $this->registerJs('
var totalPage = '. (int)$provider->pagination->pageCount .';
var currentPage = '. (int)$provider->pagination->params['page'] .';
var maxPage = 100;

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
        return;
    }

    loading = true;

    $.ajax({
        url: "'.$url.'",
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

<?php 
if($ads && isset($ads[Ads::POS_INNER_NEWS])):
    $js = 'var insertIndex = Math.round($(".article-detail .content").children().length / 2);';
    foreach($ads[Ads::POS_INNER_NEWS] as $content){
        preg_match('/<!--(.*?)-->/s', $content, $matched);
        $slot = '';
        $id = '';
        
        if($matched){
            $slot = trim($matched[1]);
            preg_match('/id=[\'|"](.*?)[\'|"]/s', $content, $matched);
            if($matched){
                $id = trim($matched[1]);
            }else{
                $id = 'div-gpt-ad-'.time().rand(100, (int)1000);
            }
        }

        if($slot && $id){
            $js .= '$(".article-detail .content").insertAt(insertIndex, "<div class=\"ads inner-ads text-center\"><div id=\"'.$id.'\"></div></div>");';
            $js .= "
                googletag.cmd.push(function() {
                    googletag.defineSlot('{$slot}', [[300, 600], [728, 90]], '{$id}').addService(googletag.pubads());

                    googletag.pubads().enableSingleRequest();
                    googletag.pubads().collapseEmptyDivs();
                    googletag.enableServices();
                });
            ";
        }else{
            $js .= '$(".article-detail .content").insertAt(insertIndex, "<div class=\"ads inner-ads text-center\">'.preg_replace( "/\r|\n/", "", addslashes($content)).'</div>");';
        }
    }

    $this->registerJs($js, yii\web\View::POS_END);
    
    // $this->registerJs('
    //     /* @TODO xu ly inline ads */
    //     var insertIndex = Math.round($(".article-detail .content").children().length / 2);

    //     $(".article-detail .content").insertAt(insertIndex, "<div style=\"color:red\">test<div id=\"div-gpt-ad-1551320847712-01\"></div></div>");

    //     googletag.cmd.push(function() {
    //         googletag.defineSlot("/21689237362/5vietnam-header", [[728, 90], [320, 50]], "div-gpt-ad-1551320847712-01").addService(googletag.pubads());

    //         googletag.pubads().enableSingleRequest();
    //         googletag.pubads().collapseEmptyDivs();
    //         googletag.enableServices();
    //     });

    //     googletag.cmd.push(function() { googletag.display("div-gpt-ad-1551320847712-01"); });
    //     /* end test */
    // ', yii\web\View::POS_END)?>
<?php endif?>