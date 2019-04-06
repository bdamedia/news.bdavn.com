<?php
use yii\widgets\Pjax;
?>

<?php foreach($categories as $category):?>
<?php Pjax::begin(['id' => 'cat-'.$category['id'], 'enablePushState' => false, 'clientOptions' => ['scrollTo' => true]]) ?>
<?= $this->render('_category', ['category' => $category])?>
<?php Pjax::end(); ?>
<?php endforeach?>

<?php $this->registerJs("
    loadCatPanel();
    
    $(document).on('pjax:complete', function() {
        $.lazyLoad();
    })
", \yii\web\View::POS_READY)?>

