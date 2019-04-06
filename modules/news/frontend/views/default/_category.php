<?php 
use app\components\Common;
?>

<div class="panel panel-default cat-panel hide" id="cat-panel-<?= $category['id']?>" data-id="<?= $category['id']?>">
    <div class="panel-heading">
        <?= yii\helpers\Html::a($category['name'], ['/category/'.Common::slugify($category['name']).'-'.$category['id']], ['data-pjax' => 0])?>
    </div>
    <div class="panel-body">
        <p class="text-center loading"><?= yii\helpers\Html::img(['/images/loading.gif'])?></p>
    </div>
    <div class="panel-footer">
        
    </div>
</div>