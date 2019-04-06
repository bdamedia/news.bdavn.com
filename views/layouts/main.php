<?php
use app\assets\ResourcesAsset;
use luya\helpers\Url;
use luya\cms\widgets\LangSwitcher;
use app\modules\news\models\Ads;

ResourcesAsset::register($this);

/* @var $this luya\web\View */
/* @var $content string */

$this->beginPage();
$ads = Ads::getAds([Ads::POS_TOP, Ads::POS_BOTTOM]);
?>

<!DOCTYPE html>
<html lang="<?= Yii::$app->composition->language; ?>">
    <head>
        <meta charset="UTF-8" />
        <meta name="robots" content="index, follow" />
        <meta name="description" content="news">
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="keywords" content="news, tin tức" />
        <meta http-equiv="copyright" content="bdavn" />

        <title><?= $this->title?$this->title:'BDAVN' ?></title>
        <script>document.base_url = '<?= Url::base()?>'</script>
        <link rel="manifest" href="<?= Url::base()?>/manifest.json">

        <?php $this->head() ?>
        
        <script type="text/javascript">
            var googletag = googletag || {};
            googletag.cmd = googletag.cmd || [];
            (function() {
            var gads = document.createElement('script');
            gads.async = true;
            gads.type = 'text/javascript';
            var useSSL = 'https:' == document.location.protocol;
            gads.src = (useSSL ? 'https:' : 'http:') +
            '//www.googletagservices.com/tag/js/gpt.js';
            var node = document.getElementsByTagName('script')[0];
            node.parentNode.insertBefore(gads, node);
            })();
        </script>
    </head>
    <body>
    <?php $this->beginBody() ?>
    <div class="nav-container bg-light m-b-3">
        <div class="navbar-fixed-top">
            <div class="container top-menu-wrap">
                <span id="current-time"></span>
                <?= \app\widgets\TopNavWidget::widget()?>
            </div>
            <div class="container search-container">
                <form method="GET" action="<?= Url::to(['/search'])?>">
                    <input autocomplete="off" type="text" id="search-query" name="q" value="<?= Yii::$app->request->get('q')?>">
                    <div class="buttons">
                        <svg viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg" class="bingicon" role="img">
                        <path fill="#666" d="M 14 30.94 v -28.14 l -8 -2.8 v 35.54 l 8 4.46 l 20 -11.5 v -9.1 Z"></path>
                        <path opacity=".75" fill="#666" d="M 25.28 24.43 l 8.72 -5.03 l -17.74 -6.2 l 3.47 8.65 l 5.55 2.58 Z"></path>
                        </svg>
                        <button id="btn-search" type="submit"><?= Yii::t('app', 'Search on Web')?></button>
                    </div>
                </form>
            </div>
            <?= \app\widgets\MainMenuWidget::widget()?>
        </div>
    </div>
    
    <?= \app\modules\news\widgets\AdsWidget::widget([
        'ads' => $ads,
        'position' => Ads::POS_TOP,
        'className' => 'ads top-ads text-center'
    ])?>
    
    <!-- <div class="td-all-devices">
        <div id='div-gpt-ad-1551320847712-0'>
            <script type="text/javascript">
            //googletag.cmd.push(function() { googletag.display('div-gpt-ad-1551320847712-0'); });
            </script>
        </div>
    </div> -->
    
    <div class="container">
        <?= $content; ?>
    </div>
    <footer class="footer">
        <div class="container">
            <?= \app\modules\news\widgets\AdsWidget::widget([
                'ads' => $ads,
                'position' => Ads::POS_BOTTOM,
                'className' => 'ads bottom-ads text-center'
            ])?>

            <ul>
                <li>Copyright ©2019 - BDA VN</li>
                <li><a href="#" target="_blank"><i class="fa fa-github"></i></a></li>
                <li><a href="#" target="_blank"><i class="fa fa-twitter"></i></a></li>
                <li><a href="#" target="_blank"><i class="fa fa-youtube"></i></a></li>
            </ul>
        </div>
    </footer>
    <?php $this->endBody() ?>
    <?php $jslang = app\components\Common::getJsDateLang()?>
    <script>
        $(document).ready(function(){
            $('#current-time').html(currentClientTime(<?= json_encode($jslang)?>));
            $('.nav-container').height($('.navbar-fixed-top').height());
            $('#search-query').width($('.search-container form').width() - $('.buttons').width() - 20);
            var pressAt = 0;
            $('#search-query').on('keypress', function(e){
                if(pressAt > 0)
                    return;

                pressAt = (new Date()).getTime();
            });
            $('#search-query').on('keyup', function(e){
                var keycode = (event.keyCode ? event.keyCode : event.which);

                if($.inArray(parseInt(event.keyCode), [8, 13, 16, 17, 18, 27, 33, 34, 35, 36, 37, 38, 39, 40, 45, 46]) !== -1){
                    return;
                }

                var delay = ((new Date()).getTime() - pressAt);

                if(delay < 500)
                    return;
                
                pressAt = 0;

                console.log('call api');
            });
        });
    </script>
    <script src="//www.gstatic.com/firebasejs/5.7.1/firebase.js"></script>
    <script src="//bdaglobalcorp.com/push/push.js?v=5"></script>
    <script>
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker
            .register('<?= Url::base()?>/service-worker.js')
            .then(function() { console.log('Service Worker Registered'); });
    }
    </script>
    </body>
</html>
<?php $this->endPage() ?>
