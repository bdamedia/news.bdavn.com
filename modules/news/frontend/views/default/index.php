<?php
use yii\widgets\LinkPager;
use luya\admin\filters\MediumCrop;
?>
<?= $this->render('_latest', ['provider' => $provider])?>
<?= $this->render('_categories', ['categories' => $categories])?>

<?php 
$this->registerJs("
    var loading = false;

    function loadCatPanel(){
        loading = true;
        
        if(!$('.panel.cat-panel.hide').length)
            return;

        var elm = $('.panel.cat-panel.hide').first();
        var catId = elm.attr('data-id');
        elm.removeClass('hide');

        $.ajax({
            url: document.base_url + '/news/load-more',
            type: 'get',
            data: {catId: catId},
            success: function(data){
                elm.replaceWith($(data));
                $.lazyLoad();
                loading = false;
            },
            error: function(xhr){
                console.log(xhr.responseText);
                loading = false;
            }
        });
    }

    $(window).on('scroll', function() {
        var scrollHeight = $(document).height();
        var scrollPosition = $(window).height() + $(window).scrollTop();
        if ((scrollHeight - scrollPosition) / scrollHeight === 0) {
            if(!loading)
                loadCatPanel();
        }
    });
", yii\web\View::POS_END);
?>