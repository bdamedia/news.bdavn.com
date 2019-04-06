<?php 
use yii\helpers\Url;
?>

<h3><?= Yii::t('app', 'About {r} result{r, plural,=0{s} =1{} other{s}}', ['r' => $totalEstimatedMatches])?></h3>

<nav aria-label="Page navigation">
    <ul class="pagination">
        <?php if($page > 1):?>
        <li class="page-item">
            <a class="page-link" href="<?=Url::to(['/search', 'q' => $q, 'p' => $page - 1]) ?>" aria-label="Previous">
                <span aria-hidden="true">&laquo; Previous</span>
            </a>
        </li>
        <?php endif?>
    
        <li class="page-item">
            <a class="page-link" href="<?=Url::to(['/search', 'q' => $q, 'p' => $page + 1]) ?>" aria-label="Next">
                <span aria-hidden="true">Next &raquo;</span>
            </a>
        </li>
    </ul>
</nav>

<ul class="search-results">
    <?php foreach($results as $result):?>
        <?php
        $link = Url::to(['/search/redirect', 'l' => urlencode($result->url)]);
        ?>
        <li>
            <p class="title"><?= $result->name?></p>
            <p class="desc"><?= $result->snippet?></p>
            <p class="link"><a href="<?= $link?>"><?= $result->displayUrl?></a></p>
        </li>
    <?php endforeach?>
</ul>

<nav aria-label="Page navigation">
    <ul class="pagination">
        <?php if($page > 1):?>
        <li class="page-item">
            <a class="page-link" href="<?=Url::to(['/search', 'q' => $q, 'p' => $page - 1]) ?>" aria-label="Previous">
                <span aria-hidden="true">&laquo; Previous</span>
            </a>
        </li>
        <?php endif?>
    
        <li class="page-item">
            <a class="page-link" href="<?=Url::to(['/search', 'q' => $q, 'p' => $page + 1]) ?>" aria-label="Next">
                <span aria-hidden="true">Next &raquo;</span>
            </a>
        </li>
    </ul>
</nav>