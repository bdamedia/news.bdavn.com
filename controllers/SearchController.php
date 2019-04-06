<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use Curl\Curl;


class SearchController extends Controller
{
    public function actionIndex()
    {
        $q = Yii::$app->request->get('q');
        $page =  (int)Yii::$app->request->get('p');
        
        if($page <= 0)
            $page = 1;
        
        $bingParams = Yii::$app->params['bingSearch'];
        $hashKey = md5("{$q}&{$page}");
        $offset = ($page - 1)*$bingParams['limit'];
        
        $blackList = array('lồn','địt','gái','gai goi','may bay','gaigoi','gai','cave');
		$check = $this->strposa($q, $blackList);
        
        $results = [];
        $totalEstimatedMatches = 0;

		if($check === FALSE){
            $data = $this->getFromCache($hashKey);

            if(!$data)
                $data =$this->bingSearch($q, $hashKey, $bingParams, $offset);
               
            if($data){
                $response = @json_decode($data);
                if($response && isset($response->webPages)){
                    $totalEstimatedMatches = $response->webPages->totalEstimatedMatches;
                    $results = $response->webPages->value;
                }
            }
        }

        $totalPage = ceil($totalEstimatedMatches/$bingParams['limit']);

        return $this->render('index', [
            'results' => $results, 
            'totalEstimatedMatches' => $totalEstimatedMatches, 
            'totalPage' => $totalPage,
            'q' => $q,
            'page' => $page
        ]);
    }

    function strposa($haystack, $needle, $offset=0) {
        if(!is_array($needle)) $needle = array($needle);

        foreach($needle as $query) {
            if(strpos($haystack, $query, $offset) === 0) return true; // stop on first true result
        }
        return false;
    }

    function getFromCache($key){
        $data = Yii::$app->cache->get($key);
        return $data;
    }

    function bingSearch($q, $hashKey, $bingParams, $offset = 0){
        $bingKey = $bingParams['key'];
        $url = 'https://api.cognitive.microsoft.com/bing/v5.0/search?Market=es-us&q='.urlencode($q).'&count='.$bingParams['limit'].'&offset='.$offset;
        //https://api.cognitive.microsoft.com/bing/v7.0/news/search => 'Access Denied'
        //https://azure.microsoft.com/en-us/services/cognitive-services/bing-news-search-api/
        //https://docs.microsoft.com/en-us/azure/cognitive-services/bing-news-search/php
        
        $curl = new Curl();
        $curl->setHeader('Ocp-Apim-Subscription-Key', $bingKey)
            ->get($url);
        
        if (!$curl->error) {
            Yii::$app->cache->set($hashKey, $curl->response, 30*60);
            
            return $curl->response;
        }

        return '';
    }

    public function actionRedirect(){
        $link = urldecode(Yii::$app->request->get('l'));
        return $this->redirect($link);
    }
}