<?php

namespace app\modules\news\frontend\controllers;

use Yii;
use luya\web\Controller;
use yii\helpers\Html;
use yii\data\ActiveDataProvider;
use app\modules\news\models\ArticleQuery as Article ;
use app\modules\news\models\Categories;
use app\modules\news\models\ArticleCategory;

/**
 * News Module Default Controller contains actions to display and render views with predefined data.
 * 
 */
class DefaultController extends \luya\web\Controller
{
    /**
     * Get Article overview.
     *
     * @return string
     */
    public function actionIndex()
    {
        //$this->view->params['customParam'] = 'customValue';
        
        $pjaxId = Yii::$app->request->get('_pjax');
        
        $excludeCats = ['photos', 'videos'];
        $getCateIds = Categories::getCateIds($excludeCats);

        $query = Article::find()->where(['status' => 1])
            ->alias('a')
            ->join('LEFT JOIN', 'news_article_category c', 'a.id = c.article_id')
            ->distinct();

        if($getCateIds)
            $query->andWhere(['not', ['c.category_id' => $getCateIds]]);

        $provider = $this->getArticleProvider($query, $this->module->id, [], 5);
        
        if($pjaxId == "#lastest"){
            return $this->renderPartial('_latest', ['provider' => $provider, 'ajax' => 'me']);
        }

        $categories = Categories::getCategories(1, ['id' => 'asc']);
        
        return $this->render('index', [
            'model' => Article::className(),
            'provider' => $provider,
            'categories' => $categories
        ]);
    }

    public function actionLoadMore(int $catId){
        $category = Categories::findOne((int)$catId);

        $provider = $this->getArticleProvider($this->articleQuery($category), '/news/load-more', ['catId' => $catId]);

        return $this->renderPartial('_articles', [
            'provider' => $provider,
            'category' => $category,
            'panel' => true
        ]);
    }

    public function actionArticles($catId = 0, $slug = ''){
        $category = Categories::findOne($catId);
        $url = '/category';
        $ajax = Yii::$app->request->get('ajax');
        
        if($category)
            $url = $category->getUrl();

        $provider = $this->getArticleProvider($this->articleQuery($category), $url, $params = []);
        
        if($ajax)
            return $this->renderPartial('_articles', [
                'provider' => $provider,
                'category' => $category,
                'panel' => false
            ]);

        return $this->render('articles', ['provider' => $provider, 'url' => $url]);
    }

    public function actionArticleDetail($id){
        $article = Article::findOne($id);
        $categoryId = ArticleCategory::find()->where(['article_id' => $id])->select(['category_id'])->scalar();
        $category = Categories::findOne($categoryId);
        
        if(!$article)
            return $this->redirect('/');

        $url = '/category';
        
        if($category)
            $url = $category->getUrl();
        
        $provider = $this->getArticleProvider($this->articleQuery($category, [$id]), $url, $params = []);

        return $this->render('detail', ['article' => $article, 'provider' => $provider, 'url' => $url]);
    }

    private function articleQuery($category, $excludeIds = []){
        $query = Article::find()->where(['a.status' => 1])->alias('a');
        if($excludeIds){
            $query->andWhere(['not', ['a.id' => $excludeIds]]);
        }

        if($category){
            $query->andWhere(['c.category_id' => $category->id])
                ->join('INNER JOIN', 'news_article_category c', 'a.id = c.article_id');
        }

        return $query;
    }

    private function getArticleProvider($query, $router, $params = [], $pageSize = null){
        $params['page'] = Yii::$app->request->get('page');
        if(!$pageSize)
            $pageSize = $this->module->articleDefaultPageSize;

        return new \yii\data\ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => $this->module->articleDefaultOrder,
            ],
            'pagination' => [
                'route' => $router,
                'params' => $params,
                'defaultPageSize' => $pageSize,
            ],
        ]);
    }

    /*
    public function actionTest(){
        Yii::$app->controller->module->context = 'news';
        $response = $this->runAction('index');
        Yii::$app->controller->module->context = '';
        return $this->render('test', ['response' => $response]);
    }
    */
}
