<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use Yii;
use yii\console\Controller;
use app\modules\news\models\Categories;
use app\modules\news\models\Article;
use app\modules\news\models\ArticleQuery;
use app\modules\news\models\ArticleCategory;
use app\modules\news\models\CrawlerData;
use QL\QueryList;
use app\components\Utils;
use luya\admin\models\User;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class CrawlerController extends Controller
{
	public static $totalUsers = 0;
    public static $uploadDir = '';
    public static $videoCatId = 0;

	public static $linksMap = [
		'vnExpress' => [
			'thoi-su' => array('xa-hoi', 'thoi-su'),
			'thoi-su/giao-thong' => array('xa-hoi', 'giao-thong'),
			'the-gioi' => array('the-gioi'),
			'doi-song/nha' => array('doi-song', 'nha-dat'),
			'doi-song' => array('doi-song'),
			'giai-tri/phim' => array('giai-tri', 'dien-anh-truyen-hinh'),
			'giai-tri/nhac' => array('giai-tri', 'am-nhac'),
			'giai-tri/thoi-trang' => array('giai-tri', 'thoi-trang'),
			'giai-tri' => array('giai-tri'),
			'the-thao/bong-da' => array('the-thao', 'bong-da'),
			'the-thao/tennis' => array('the-thao', 'quan-vot'),
			'the-thao/cac-mon-khac' => array('the-thao', 'cac-mon-khac'),
			'the-thao/hau-truong' => array('the-thao', 'hau-truong'),
			'the-thao' => array('the-thao'),
			'oto-xe-may' => array('xe-co'),
			'khoa-hoc' => array('khoa-hoc'),
			'du-lich' => array('van-hoa', 'du-lich'),
			'suc-khoe' => array('doi-song', 'suc-khoe-y-te'),
			'giao-duc' => array('giao-duc'),
			'phap-luat' => array('phap-luat'),
			'kinh-doanh' => array('kinh-te'),
			'so-hoa' => array('cong-nghe'),
		],
		'danviet' => [
			'1007' => array('the-gioi'),
			'1003' => array('xa-hoi'),
			'1006' => array('van-hoa'),
			'1004' => array('kinh-te'),
			'1035' => array('the-thao'),
			'1060' => array('doi-song', 'suc-khoe-y-te'),
			'1005' => array('xa-hoi', 'giao-duc'),
			'1068' => array('phap-luat', 'hinh-su-dan-su'),
			'1067' => array('phap-luat', 'an-ninh-trat-tu'),
			'1028' => array('doi-song', 'dinh-duong-lam-dep'),
			'1032' => array('cong-nghe', 'khoa-hoc'),
			'1034' => array('giai-tri', 'xe-co'),
			'1033' => array('cong-nghe', 'thiet-bi-phan-cung'),
			'1078' => array('cong-nghe', 'cntt-vien-thong'),
			'1079' => array('cong-nghe', 'cntt-vien-thong'),
			'1022' => array('doi-song', 'suc-khoe-y-te'),
			'1024' => array('van-hoa', 'am-thuc'),
			'1098' => array('van-hoa', 'du-lich'),
			'1101' => array('van-hoa', 'du-lich'),
        ],
        'msn/en' => [
            'news/money' => array('money'),
            'news/us' => array('us'),
            'news/world' => array('world'),
            'news/good-news' => array('good-news'),
            'news/politics' => array('politics'),
            'news/factcheck' => array('fact-check'),
            'news/opinion' => array('opinion'),
            'news/crime' => array('crime'),
            'news/technology' => array('technology'),
            'news/entertainment' => array('entertainment'),
            'autos/news' => array('autos'),
            'travel' => array('travel'),
            'health' => array('health'),
            'foodanddrink' => array('food-and-drink'),
            'news/photos' => array('photos'),
            'news/video' => array('videos'),
            'entertainment/gaming' => array('entertainment', 'gaming'),
            'entertainment/humor' =>  array('entertainment', 'humor'),
            'music' => array('entertainment', 'music'),
            'music/reviews' => array('entertainment', 'music'),
            'movies' =>  array('entertainment', 'movies'),
            'movies/reviews' =>  array('entertainment', 'movies'),
            'entertainment/celebrity' =>   array('entertainment', 'movies'),
            'tv' =>  array('entertainment', 'tv'),
            'tv/recaps' =>  array('entertainment', 'tv'),
            'entertainment/news' => array('entertainment'),
            'travel/tripideas' => array('travel', 'trip-ideas'),
            'travel/tips' => array('travel', 'tips'),
            'travel/points-rewards' => array('travel', 'rewards'),
            'travel/accessible' => array('travel', 'accessible-travel'),
            'travel/adventuretravel' => array('travel', 'adventure-travel'),
            'travel/news' => array('travel'),
            'health/health-news' => array('health'),
            'health/weightloss' => array('health', 'weightloss'),
            'health/fitness' => array('health', 'fitness'),
            'health/nutrition' => array('health', 'nutrition'),
            'health/mentalhealth' => array('health', 'mental-health-self-care'),
            'health/wellness' => array('health', 'wellness'),
            'health/medical' => array('health', 'medical'),
            'health/voices' => array('health', 'voices'),
            'foodanddrink/cooking/tipsandtricks' => array('food-and-drink', 'tips-and-tricks'),
            'foodanddrink/restaurantsandnews' => array('food-and-drink', 'restaurants'),
            'foodanddrink/cooking/quickandeasy' => array('food-and-drink', 'quick-and-easy'),
            'foodanddrink/cooking/recipes' => array('food-and-drink', 'recipes'),
            'foodanddrink/restaurantsandnews/casual' => array('food-and-drink', 'restaurants', 'casual'),
            'foodanddrink/beverages' => array('food-and-drink', 'drinks'),
        ]
	];

	public function init(){
        self::$videoCatId = Categories::find()->where(['slug' => 'videos'])->scalar();
		self::$totalUsers = User::find()->count();
		Yii::setAlias('@webroot', Yii::$app->basePath.'/public_html');
		self::$uploadDir = Yii::$app->storage->getServerPath();

        parent::init();
	}
    
    public function actionMsnEn($start = null, $end = null){
        $webUrl = 'https://www.msn.com/en-us/';

        $linksMap = self::$linksMap['msn/en'];
        $totalLinks = count($linksMap);

        if($start === null)
            $start = 0;

        if($end === null)
            $end = $totalLinks;

        $idx = 0;
		foreach ($linksMap as $artLink => $catSLugs) {
            if($idx < $start || $idx > $end){
                $idx++;
                continue;
            }

            $categoryIds = static::getCatIds($catSLugs);
            $catName = implode(", ", $catSLugs);

            if($categoryIds){
                self::echoLog("Lấy post cho danh mục {$catName}");
                static::getPageLinks('msnen', $webUrl.$artLink, $categoryIds);
            }else{
                self::echoLog("Không tìm thấy danh mục {$catName}");
            }

            self::echoLog("=========================================================");

            $idx++;
		}
    }

    public function actionVnExpress($startPage = 1, $limitPage = 5, $catsIndex = ''){
		$webUrl = 'https://vnexpress.net/';

		if($startPage < 1)
            $startPage = 1;
        if($limitPage < 1)
            $limitPage = 1;

        $linksMap = self::$linksMap['vnExpress'];
        $startIndex = 1;
        $endIndex = count($linksMap);

        if($catsIndex){
            $catsIndex = explode('-', $catsIndex);
            if(count($catsIndex) == 2){
                $startIndex = (int)$catsIndex[0];
                $endIndex = (int)$catsIndex[1];
            }
        }

        $idx = 1;
		foreach ($linksMap as $artLink => $catSLugs) {
            if($idx >= $startIndex && $idx <= $endIndex){
                $categoryIds = static::getCatIds($catSLugs);
                $catName = implode(", ", $catSLugs);

                if($categoryIds){
                    self::echoLog("Lấy post cho danh mục {$catName}");
                    for($i = $limitPage; $i >= $startPage; $i--){
						$link = $artLink;
                        if($i > 1)
                            $link .= ($link == 'the-thao'?'/':'-').'p'.$i;

						static::getPageLinks('vnexpress', $webUrl.$link, $categoryIds);
                    }
                }else{
                    self::echoLog("Không tìm thấy danh mục {$catName}");
                }

                self::echoLog("=========================================================");
            }
            $idx++;
		}
	}

	public function actionDanViet($startPage = 1, $limitPage = 5, $catsIndex = ''){
		if($startPage < 1)
            $startPage = 1;
        if($limitPage < 1)
            $limitPage = 1;

        $linksMap = self::$linksMap['danviet'];
        $startIndex = 1;
        $endIndex = count($linksMap);

        if($catsIndex){
            $catsIndex = explode('-', $catsIndex);
            if(count($catsIndex) == 2){
                $startIndex = (int)$catsIndex[0];
                $endIndex = (int)$catsIndex[1];
            }
        }

        $idx = 1;
		foreach ($linksMap as $catId => $catSLugs) {
            if($idx >= $startIndex && $idx <= $endIndex){             
                $categoryIds = static::getCatIds($catSLugs);
                $catName = implode(", ", $catSLugs);

                if($categoryIds){
                    self::echoLog("Lấy post cho danh mục {$catName}");
                    for($i = $limitPage; $i >= $startPage; $i--){
                        self::getPageLinks('danviet', 'http://danviet.vn/ajax/box_bai_viet_trang_chuyen_muc/index/'.$catId.'/'.$i.'/10/1/0/1/0', $categoryIds);
                    }
                }else{
                    self::echoLog("Không tìm thấy danh mục {$catName}");
                }

                self::echoLog("=========================================================");
            }
            $idx++;
		}
	}

	public static function getPageLinks($site, $categoryUrl, $categoryIds = array()) {
		$command = \Yii::getAlias('@app') . DIRECTORY_SEPARATOR . 'yii crawler/get-links '.base64_encode(json_encode(array($site, $categoryUrl, $categoryIds)));
        //echo $command;die;
        self::echoLog(" - execute command get-link");
		exec($command ,  $output, $return_var);
		self::echoLog(implode("\n", $output));
	}

	public static function actionGetLinks($encodedData){
		$data = @json_decode(base64_decode($encodedData), true);

		if(!$data){
			return;
		}

		$site = $data[0];
		$categoryUrl = $data[1];
		$categoryIds = $data[2];

        self::echoLog(" - Lấy post link từ trang {$categoryUrl}");
        
        try{
            $ql = QueryList::get($categoryUrl);
        }catch(Exception $e){
            self::echoLog(' - -> ERROR: '.$e->getMessage());
            return;
        }catch(Error $e){
            self::echoLog(' - -> ERROR: '.$e->getMessage());
            return;
        }
        
        if($site == 'msnen'){
            return self::getMsnLinks($ql, $site, $categoryIds, $categoryUrl);
        }
		
		$keys = [
			'vnexpress' => [
				'.sidebar_1 .list_news', 
				'.icon_thumb_videophoto',
				'.title_news>a'
			],
			'danviet' => [
				'.listNewscm1',
				'.imgIcon',
				'.imgFloatcm>a'
			]
		];

		$ql = $ql->find($keys[$site][0]);
        
        for($i = 0; $i < $ql->count(); $i++){
            try{
                $new = $ql->eq($i);
                $new->find($keys[$site][1])->replaceWith('');
                $a = $new->find($keys[$site][2])->eq(0);
                $pageLink = $a->attr('href');
                
                if($pageLink != ''){
					if($site == "danviet"){
						$pageLink = 'http://danviet.vn'.$pageLink;
						$imageSrc = $new->find('.imgFloatcm img')->eq(0)->src;
						$imageSrc = str_replace('medium/', '', $imageSrc);
					}else{
						$imageSrc = $new->find('.thumb_art img')->eq(0)->attr('data-original');
						$imageSrc = str_replace('_180x108', '', $imageSrc);
					}

                    $title = trim($a->attr('title'));
					$added = 0;
					
                    if($title){
						$postId = Article::find()->where(['title' => $title])->select(['id'])->scalar();
						
                        if($postId){
                            $added = true;
                            if($categoryIds){
                                $rs = self::updateArticleCats($postId, $categoryIds);
                            }
                        }
                    }
                    
                    if(!$added)
                        self::execDownLoadPost($site, $pageLink, $imageSrc, $categoryIds);
                    else
                        self::echoLog(" - - Added: {$pageLink}");
                }
            }catch(error $e){
                self::echoLog(' - -> ERROR HANDLE CATE: '.$e->getMessage());
            }
        }
    }
    
    public static function getMsnLinks($ql, $site, $categoryIds, $categoryUrl){
        $ul = $ql->find('.mediuminfopanehero > ul');
        
        $ql->find('.showcasead')->replaceWith('');
        $ql->find('.rivercardphoto')->replaceWith('');

        if($ul->count() <= 0){
            return self::getMsnLinks2($ql, $site, $categoryIds, $categoryUrl);
        }

        $ql = $ul;
        $ql = $ql->find('li');
        
        for($i = 0; $i < $ql->count(); $i++){
            $new = $ql->eq($i);
            $a = $new->find('a')->eq(0);
            $pageLink = $a->attr('href');
            
            if($pageLink != ''){
                $pageLink = 'https://www.msn.com'.$pageLink;
                $img = $new->find('img')->eq(0);
                $imageSrc = $img->attr('data-src');
                $matches = [];
                preg_match('/\/\/(.*?)\?/s', $imageSrc, $matches);
                $imageSrc = '';
                if(isset($matches[1]))
                    $imageSrc = 'https://'.$matches[1].'?w=624&m=6&q=60&u=t&o=t&l=f&f=jpg';
                
                $title = trim($a->attr('title'));
                
                self::beforeDownload($title, $site, $pageLink, $imageSrc, $categoryIds, $categoryUrl);
            }
        }
    }

    public static function trimEnter($str){
        return trim(str_replace("\r\n", "", $str));
    }

    public static function getMsnLinks2($ql, $site, $categoryIds, $categoryUrl){
        $jsContainer = $ql->find('.rv-inner .rc-container-js');
        self::saveAPIData($jsContainer, $categoryUrl, $categoryIds);
        
        $list2 = $jsContainer->find('.rc-item-js');

        for($i = 0; $i < $list2->count(); $i++){
            $new = $list2->eq($i);
            $apppromocard = $new->find('.apppromocard');
                
            if($apppromocard->count()){
                continue;
            }
            
            //echo $new->html();
            //echo "\n\n";

            $a = $new->find('a')->eq(0);
            $pageLink = $a->attr('href');
            
            if(!$pageLink){
                //echo "no page\n";
                continue;
            }

            $pageLink = 'https://www.msn.com'.$pageLink;
            $img = $new->find('img')->eq(0);
            $imageSrc = $img->attr('data-src');
            $matches = [];
            preg_match('/\/\/(.*?)\?/s', $imageSrc, $matches);
            $imageSrc = '';
            if(isset($matches[1]))
                $imageSrc = 'https://'.$matches[1].'?w=624&m=6&q=60&u=t&o=t&l=f&f=jpg';
            else
                $imageSrc = $img->attr('src');

            $title = trim($a->find('h3')->text());
            //echo "$title\n";
            self::beforeDownload($title, $site, $pageLink, $imageSrc, $categoryIds, $categoryUrl);
        }
    }

    static function beforeDownload($title, $site, $pageLink, $imageSrc, $categoryIds, $categoryUrl = ''){
        $added = 0;

        if($title){
            $postId = Article::find()->where(['title' => $title])->select(['id'])->scalar();
            
            if($postId){
                $added = true;
                if($categoryIds){
                    $rs = self::updateArticleCats($postId, $categoryIds);
                }
            }
        }
        
        if(!$added){
            if($site == 'msnen' && $categoryIds && in_array(self::$videoCatId, $categoryIds))
                return self::downLoadMsnVideo($site, $pageLink, $imageSrc, $categoryIds);
            self::execDownLoadPost($site, $pageLink, $imageSrc, $categoryIds);
        }else
            self::echoLog(" - - Added: {$pageLink}");
    }

	static function execDownLoadPost($site, $pageLink, $imageLink, $categoryIds = array()){
        //echo "{$pageLink}\n";return;
		$command = \Yii::getAlias('@app') . DIRECTORY_SEPARATOR . 'yii crawler/get '.$site.' '.base64_encode(json_encode(array($pageLink, $imageLink, $categoryIds)));
		//echo $command;
        self::echoLog(" - - execute command crawler/get");
		exec($command ,  $output, $return_var);
		//self::echoLog(" - - Downloading... #".$return_var);
		self::echoLog(implode("\n", $output));
	}

	public function actionGet($site, $dataEncode){
		$data = @json_decode(base64_decode($dataEncode), true);

		if($data){
			$func = 'download'.ucfirst($site).'Post';
			self::{$func}($data[0], $data[1], $data[2]);
		}
    }
    
    public function actionTest(){
        //gallery-container
        $pageLink = 'https://www.msn.com/en-us/news/other/see-all-the-game-of-thrones-stars-at-the-season-8-premiere/ss-BBVAMhR';
        $ql = QueryList::get($pageLink);
        $gallery = $ql->find('#maincontent .gallery-container');
        $images = $gallery->find('img');
        $gallery = '<div class="text-center">';
        for($i=0; $i<$images->count();$i++){
            $image = $images->eq($i);
            $dataSrc = @json_decode($image->attr('data-src'), true);
            $src = isset($image->src)?$image->src:'';
            if($dataSrc && isset($dataSrc['default'])){
                $src = isset($dataSrc['default']['src'])?$dataSrc['default']['src']:$dataSrc['default'];
            }
            if($src){
                $gallery .= '<p><img src="'.$src.'" style="max-width: 98%"/></p>'."\n";
            }
        }
        $gallery.= '</div>';
        echo $html;
    }
    

    public static function downloadMsnenPost($pageLink, $imageLink, $categoryIds = array()){
        self::echoLog(" - - Lấy post từ trang {$pageLink}");

        try{
            $ql = QueryList::get($pageLink);
            $precontent = $ql->find('#precontent');
            if(!$precontent->html()){
                return;
            }
        }catch(Exception $e){
            self::echoLog(' - -> ERROR: '.$e->getMessage());
            return;
        }catch(Error $e){
            self::echoLog(' - -> ERROR: '.$e->getMessage());
            return;
        }
        
        $source = $precontent->find('.partnerarticlelogo img')->alt;
        $title = $precontent->find('h1')->html();
        $title = trim(str_replace('<!-- check switch to english site -->', '', $title));
		$post_id = Article::find()->where(['title' => $title])->select(['id'])->scalar();
        $date = $precontent->find('.timeinfo-txt time')->attr('datetime');
        $authorName = $precontent->find('.authorname-txt')->text();
        $precontent = null;

        if(!$date){
            self::echoLog(' - - -> STOP: không lấy được date từ title div');
            return;
        }

        $date = date('Y-m-d H:i:s', strtotime($date));
        $month = date('m', strtotime($date));
        $year = date('Y', strtotime($date));
		
		$folderName = "{$year}-{$month}";
        $uploadsDir = Utils::getUploadDir(self::$uploadDir, $folderName);
        $totalFile = Utils::countFileInDirectory($uploadsDir);
        $inc = 0;

        while($totalFile > 3000){
			$inc++;
			$folderName = "{$year}-{$month}-{$inc}";
            $uploadsDir = Utils::getUploadDir(self::$uploadDir, $folderName);
            $totalFile = Utils::countFileInDirectory($uploadsDir);
        }

		$attachmentId = Utils::uploadFeatureImage($imageLink, $uploadsDir, $folderName);
		
		if($post_id){
			self::echoLog(' - - -> Existed "'.$title.'"');

            if($categoryIds){
				$rs = self::updateArticleCats($post_id, $categoryIds);
            }
            
			if($attachmentId){
				$rs = self::updateArticleImage($post_id, $attachmentId);
				self::echoLog(' - - -> Set thumbnail thành công rs');
			}

			return;
		}
        
        $gallery = $ql->find('#maincontent .gallery-container');
        $images = $gallery->find('img');
        $gallery = '';
        for($i=0; $i<$images->count();$i++){
            $image = $images->eq($i);
            $dataSrc = @json_decode($image->attr('data-src'), true);
            $src = isset($image->src)?$image->src:'';
            if($dataSrc && isset($dataSrc['default'])){
                $src = isset($dataSrc['default']['src'])?$dataSrc['default']['src']:$dataSrc['default'];
            }
            if($src){
                $gallery .= '<p class="text-center"><img src="'.$src.'" style="max-width: 98%"/></p>'."\n";
            }
        }
        $gallery.= '';

        $detail = $ql->find('#maincontent .articlebody');
        $detail->find('.xnetvidplayer')->replaceWith('');
        $detail->find('.inline-slideshow')->replaceWith('');
        $detail->find('.ec-module')->replaceWith('');
        $detail->find('iframe')->replaceWith('');
        $detail->find('script')->replaceWith('');
        $detail->find('.readmore')->replaceWith('');
        $detail->find('img')->each(function($img){
            $dataSrc = @json_decode($img->getAttribute('data-src'), true);
            
            if($dataSrc && isset($dataSrc['default'])){
                $img->setAttribute('src', isset($dataSrc['default']['src'])?$dataSrc['default']['src']:$dataSrc['default']);
            }

            return $img;
        });

        $contentDetail = $gallery.$detail->html();
        $contentDetail = preg_replace('/<p><strong>Read more(.*?)Read More<\/a>/s', '<div>',$contentDetail);
        $authorId = 0;
        
        if($authorName)
            $authorId = self::getAuthorId($authorName);
        
        if(!$authorId)
            $authorId = Utils::getAuthorId(self::$totalUsers);
            
        self::insertArticle(array(
			'create_date' => "{$date}",
			'update_date' => "{$date}",
			'title'    => $title,
		    'content'  => $contentDetail,
		    'status'   => '1',
			'create_user_id'   => $authorId,
			'update_user_id'   => $authorId,
            'image_name' => $attachmentId,
            'source' => $source
		), $categoryIds);

    }

    public static function downloadMsnVideo($site, $pageLink, $imageLink, $categoryIds){
        self::echoLog(" - - Lấy video từ trang {$pageLink}");

        try{
            $ql = QueryList::get($pageLink);
            $video = $ql->find('.wcvideoplayer');
            $metaData = json_decode($video->attr('data-metadata'), true);
            if(!$metaData || !isset($metaData['videoFiles']) || !is_array($metaData['videoFiles'])) return;
            $src = '';
            foreach($metaData['videoFiles'] as $file){
                if(strpos($file['url'], '.mp4') !== false){
                    $src = $file['url'];
                    break;
                }
            }
            $thumbnail = $metaData['thumbnail']['url'];

            $video = '<video controls style="width: 100%; height: auto;" poster="'.$thumbnail.'">
                <source src="'.$src.'" type="video/mp4">
                Sorry, your browser doesn\'t support embedded videos. 
                <a href="'.$src.'">Download</a>
            </video>';
        }catch(Exception $e){
            self::echoLog(' - -> ERROR: '.$e->getMessage());
            return;
        }catch(Error $e){
            self::echoLog(' - -> ERROR: '.$e->getMessage());
            return;
        }
        
        $precontent = $ql->find('.video-info');
        $source = $precontent->find('.provider span')->text();
        $title = $precontent->find('h1')->html();
        $title = trim(str_replace('<!-- check switch to english site -->', '', $title));
		$post_id = Article::find()->where(['title' => $title])->select(['id'])->scalar();
        $date = $precontent->find('.metadata time')->attr('datetime');
        $authorName = $source;
        $precontent = null;

        if(!$date){
            self::echoLog(' - - -> STOP: không lấy được date từ title div');
            return;
        }

        $date = date('Y-m-d H:i:s', strtotime($date));
        $month = date('m', strtotime($date));
        $year = date('Y', strtotime($date));
		
		$folderName = "{$year}-{$month}";
        $uploadsDir = Utils::getUploadDir(self::$uploadDir, $folderName);
        $totalFile = Utils::countFileInDirectory($uploadsDir);
        $inc = 0;

        while($totalFile > 3000){
			$inc++;
			$folderName = "{$year}-{$month}-{$inc}";
            $uploadsDir = Utils::getUploadDir(self::$uploadDir, $folderName);
            $totalFile = Utils::countFileInDirectory($uploadsDir);
        }

		$attachmentId = Utils::uploadFeatureImage($imageLink, $uploadsDir, $folderName);
		
		if($post_id){
			self::echoLog(' - - -> Existed "'.$title.'"');

            if($categoryIds){
				$rs = self::updateArticleCats($post_id, $categoryIds);
            }
            
			if($attachmentId){
				$rs = self::updateArticleImage($post_id, $attachmentId);
				self::echoLog(' - - -> Set thumbnail thành công rs #'.$rs);
			}

			return;
		}
        
        $authorId = 0;
        
        if($authorName)
            $authorId = self::getAuthorId($authorName);
        
        if(!$authorId)
            $authorId = Utils::getAuthorId(self::$totalUsers);
            
        self::insertArticle(array(
			'create_date' => "{$date}",
			'update_date' => "{$date}",
			'title'    => $title,
		    'content'  => $video,
		    'status'   => '1',
			'create_user_id'   => $authorId,
			'update_user_id'   => $authorId,
            'image_name' => $attachmentId,
            'source' => $source
		), $categoryIds);

    }

	public static function downloadVnexpressPost($pageLink, $imageLink, $categoryIds = array()){
        self::echoLog(" - - Lấy post từ trang {$pageLink}");

        try{
            $ql = QueryList::get($pageLink);
            $detailNews = $ql->find('.sidebar_1');
            if(!$detailNews->html()){
                self::downloadVnExpressPost2($ql, $imageLink, $categoryIds);
                return;
            }

            $ql->setHtml($detailNews->html()); 
            $detailNews = null;
        }catch(Exception $e){
            self::echoLog(' - -> ERROR: '.$e->getMessage());
            return;
        }catch(Error $e){
            self::echoLog(' - -> ERROR: '.$e->getMessage());
            return;
        }
		
        $title = $ql->find('.title_news_detail')->html();
        $title = trim(str_replace('<!-- check switch to english site -->', '', $title));

		$post_id = Article::find()->where(['title' => $title])->select(['id'])->scalar();
		
        $date = $ql->find('.time');
        $date->find('span')->replaceWith(' ');
        $date = $date->html();
        $parsedDate = explode(',', $date);
        
        if(count($parsedDate) < 2){
            self::echoLog(' - - -> STOP: không lấy được date từ title div');
            return;
        }

        $date = $parsedDate[1];
        $date = explode(" ", trim($date));
        $time = isset($date[1])?$date[1]:'';
        if(!$time){
            $time = explode(' ', trim($parsedDate[2]));
            $time = $time[0];
        }
        
        $date = $date[0];
        $parsedDate = explode("/", $date);
        $parsedDate[0] = str_pad($parsedDate[0], 2, "0", STR_PAD_LEFT);
        $parsedDate[1] = str_pad($parsedDate[1], 2, "0", STR_PAD_LEFT);

		$date = "{$parsedDate[2]}-{$parsedDate[1]}-{$parsedDate[0]}";
		
		$folderName = "{$parsedDate[2]}-{$parsedDate[1]}";
        $uploadsDir = Utils::getUploadDir(self::$uploadDir, $folderName);
        $totalFile = Utils::countFileInDirectory($uploadsDir);
        $inc = 0;
        while($totalFile > 3000){
			$inc++;
			$folderName = "{$parsedDate[2]}-{$parsedDate[1]}-{$inc}";
            $uploadsDir = Utils::getUploadDir(self::$uploadDir, $folderName);
            $totalFile = Utils::countFileInDirectory($uploadsDir);
        }

		$attachmentId = Utils::uploadFeatureImage($imageLink, $uploadsDir, $folderName);
		
		if($post_id){
			self::echoLog(' - - -> Existed "'.$title.'"');

            if($categoryIds){
				$rs = self::updateArticleCats($post_id, $categoryIds);
            }
            
			if($attachmentId){
				$rs = self::updateArticleImage($post_id, $attachmentId);
				self::echoLog(' - - -> Set thumbnail thành công rs #'.$rs);
			}

			return;
		}
        
        $detail = $ql->find('.content_detail');
        $detail->find('iframe')->replaceWith('');
        $detail->find('input')->replaceWith('');
        $detail->find('script')->replaceWith('');
        $detail->find('noscript')->replaceWith('');
        $sImg = $detail->find('.block_thumb_slide_show');
        if($sImg && $sImg->html()){
            $sImg->attr('style', '');
        }
        
        $detail->find('img')->each(function($img){
            if($img->getAttribute('data-original')){
                $img->setAttribute('src', $img->getAttribute('data-original'));
            }

            return $img;
        });

		$contentDetail = $detail->html();
		
		$authorId = Utils::getAuthorId(self::$totalUsers);
        self::insertArticle(array(
			'create_date' => "{$date} {$time}",
			'update_date' => "{$date} {$time}",
			'title'    => $title,
		    'content'  => $contentDetail,
		    'status'   => '1',
			'create_user_id'   => $authorId,
			'update_user_id'   => $authorId,
            'image_name' => $attachmentId,
            'source' => 'vnexpress'
		), $categoryIds);
	}
	
	static function downloadVnExpressPost2($ql, $imageLink, $categoryIds = array()){
        self::echoLog(' - - -> Page 2');
        try{
            $detailNews = $ql->find('.col_noidung');
            if(!$detailNews->html())
                return;
            $ql->setHtml($detailNews->html());
            $detailNews = null;
        }catch(Exception $e){
            self::echoLog(' - -> ERROR: '.$e->getMessage());
            return;
        }catch(Error $e){
            self::echoLog(' - -> ERROR: '.$e->getMessage());
            return;
        }
		
        $title = $ql->find('.title_news h1')->html();
        $title = trim(str_replace('<!-- check switch to english site -->', '', $title));

        $post_id = Article::find()->where(['title' => $title])->select(['id'])->scalar();
		
        $date = $ql->find('.block_timer');
        $date->find('span')->replaceWith(' ');
        $date = $date->html();
        $parsedDate = explode(',', $date);
        
        if(count($parsedDate) < 2){
            self::echoLog(' - - -> STOP: không lấy được date từ title div');
            return;
        }

        $date = $parsedDate[1];
        $date = explode(" ", trim($date));
        $time = $date[1];
        if(!$time){
            $time = explode(' ', trim($parsedDate[2]));
            $time = $time[0];
        }
        
        $date = $date[0];
        $parsedDate = explode("/", $date);
        $parsedDate[0] = str_pad($parsedDate[0], 2, "0", STR_PAD_LEFT);
        $parsedDate[1] = str_pad($parsedDate[1], 2, "0", STR_PAD_LEFT);

		$date = "{$parsedDate[2]}-{$parsedDate[1]}-{$parsedDate[0]}";
        
        $folderName = "{$parsedDate[2]}-{$parsedDate[1]}";
        $uploadsDir = Utils::getUploadDir(self::$uploadDir, $folderName);
        $totalFile = Utils::countFileInDirectory($uploadsDir);
        $inc = 0;
        while($totalFile > 3000){
			$inc++;
			$folderName = "{$parsedDate[2]}-{$parsedDate[1]}-{$inc}";
            $uploadsDir = Utils::getUploadDir(self::$uploadDir, $folderName);
            $totalFile = Utils::countFileInDirectory($uploadsDir);
        }

		$attachmentId = Utils::uploadFeatureImage($imageLink, $uploadsDir, $folderName);
		
		if($post_id){
			self::echoLog(' - - -> Existed "'.$title.'"');

            if($categoryIds){
				$rs = self::updateArticleCats($post_id, $categoryIds);
            }
            
			if($attachmentId){
				$rs = self::updateArticleImage($post_id, $attachmentId);
				self::echoLog(' - - -> Set thumbnail thành công rs #'.$rs);
			}

			return;
		}
        
        $detail = $ql->find('.fck_detail');
        $detail->find('iframe')->replaceWith('');
        $detail->find('input')->replaceWith('');
        $detail->find('script')->replaceWith('');
        $detail->find('noscript')->replaceWith('');
        $detail->find('.author_mail .email')->replaceWith('');

        $sImg = $detail->find('.block_thumb_slide_show');
        if($sImg && $sImg->html()){
            $sImg->attr('style', '');
		}
		
		$authorId = Utils::getAuthorId(self::$totalUsers);
        self::insertArticle(array(
			'create_date' => "{$date} {$time}",
			'update_date' => "{$date} {$time}",
			'title'    => $title,
		    'content'  => $detail->html(),
		    'status'   => '1',
			'create_user_id'   => $authorId,
			'update_user_id'   => $authorId,
            'image_name' => $attachmentId,
            'source' => 'vnexpress'
		), $categoryIds);
	}

	public static function downloadDanvietPost($pageLink, $imageLink, $categoryIds = array()){
		self::echoLog(" - - Lấy post từ trang {$pageLink}");

        try{
            $ql = QueryList::get($pageLink);
            $detailNews = $ql->find('.listNewscm');
        }catch(Exception $e){
            self::echoLog(' - -> ERROR: '.$e->getMessage());
            return;
        }catch(Error $e){
            self::echoLog(' - -> ERROR: '.$e->getMessage());
            return;
        }
		
        $title = $detailNews->find('.Bigtieudebaiviet')->html();

        $post_id = Article::find()->where(['title' => $title])->select(['id'])->scalar();
		
        $date = $detailNews->find('.datetimeup');
        $date->find('b')->replaceWith('');
        $date->find('img')->replaceWith('');
        $date = $date->html();
        $date = explode('ngày', $date);
        
        if(count($date) < 2){
            self::echoLog(' - - -> STOP: không lấy được date từ title div');
            return;
        }

        $date = $date[1];
        $date = explode(" ", trim($date));
        $time = $date[1];
        $date = $date[0];
        $parsedDate = explode("/", $date);
		$date = "{$parsedDate[2]}-{$parsedDate[1]}-{$parsedDate[0]}";
		
		$folderName = "{$parsedDate[2]}-{$parsedDate[1]}";
        $uploadsDir = Utils::getUploadDir(self::$uploadDir, $folderName);
        $totalFile = Utils::countFileInDirectory($uploadsDir);
        $inc = 0;
        while($totalFile > 3000){
			$inc++;
			$folderName = "{$parsedDate[2]}-{$parsedDate[1]}-{$inc}";
            $uploadsDir = Utils::getUploadDir(self::$uploadDir, $folderName);
            $totalFile = Utils::countFileInDirectory($uploadsDir);
        }

		$attachmentId = Utils::uploadFeatureImage($imageLink, $uploadsDir, $folderName);
		
		if($post_id){
			self::echoLog(' - - -> Existed "'.$title.'"');

            if($categoryIds){
				$rs = self::updateArticleCats($post_id, $categoryIds);
            }
            
			if($attachmentId){
				$rs = self::updateArticleImage($post_id, $attachmentId);
				self::echoLog(' - - -> Set thumbnail thành công rs #'.$rs);
			}

			return;
		}

		$detail = $detailNews->find('.contentbaiviet');
        $detail->find('iframe')->replaceWith('');
        $detail->find('.shareImage')->replaceWith('');
        $detail->find('.media-player')->replaceWith('');
        $detail->find('input')->replaceWith('');
        $detail->find('script')->replaceWith('');

		$authorId = Utils::getAuthorId(self::$totalUsers);
        self::insertArticle(array(
			'create_date' => "{$date} {$time}",
			'update_date' => "{$date} {$time}",
			'title'    => $title,
		    'content'  => $detail->html(),
		    'status'   => '1',
			'create_user_id'   => $authorId,
			'update_user_id'   => $authorId,
            'image_name' => $attachmentId,
            'source' => 'danviet'
		), $categoryIds);
	}

	static function insertArticle($data, $categoryIds){
        if($data['title']){
            $postId = Article::find()->where(['title' => $data['title']])->select(['id'])->scalar();
            
            if($postId){
                self::echoLog(' - - -> Added from other command');
                return;
            }
        }

        $model = new ArticleQuery;
		$model->attributes = $data;
		if($model->save()){
			self::updateArticleCats($model->id, $categoryIds);
			self::echoLog(' - - -> Success: Tạo POST thành công post #'.$model->id);
			return;
		}

		self::echoLog(' - - -> Fail: Lỗi tạo POST');
		print_r($model->getErrors(), true);
	}

	static function updateArticleCats($postId, $catIds){
		if(!$catIds || !$postId)
			return;

		$categories = ArticleCategory::find()->where(['article_id' => $postId])->select(['category_id'])->all();

		$addes = [];
		$removes = [];
		
		foreach($categories as $cat){
			if(in_array($cat->category_id, $catIds)){
				$addes[] = $cat->category_id;
			}else{
				$removes[] = $cat->category_id;
			}
		}

		if($removes){
			ArticleCategory::deleteAll(['article_id' => $postId, 'category_id' => $removes]);
		}

		foreach($catIds as $catId){
			if(!in_array($catId, $addes)){
				$model = new ArticleCategory;
				$model->attributes = [
					'category_id' => $catId,
					'article_id' => $postId
				];
				$model->save();
			}
		}
	}

	static function updateArticleImage($postId, $imageName){
		if(!$imageName)
			return;

		return ArticleQuery::updateAll(['image_name' => $imageName], ['id' => $postId]);
	}

	static function echoLog($message){
		echo "{$message}\n";
	}

	static function getCatIds($slugs){
		$cats = Categories::find()->where(['slug' => $slugs])->all();
		return array_map(function($cat){return $cat->id;}, $cats);
    }
    
    public function getAuthorId($authorName){
        $authorName = explode(' ', $authorName);
        $firstName = $authorName[0];
        $lastName = '';
        if(count($authorName) > 1){
            unset($authorName[0]);
            $lastName = implode(' ', $authorName);
        }

        $author = User::find()->where([
            'firstname' => $firstName,
            'lastname' => $lastName
        ])->one();

        if(!$author){
            $email = str_replace('-','.',\app\components\Common::slugify($firstName.$lastName)).'@local.'.rand(100,(int)999);

            $author = new User;
            $author->attributes = [
                'firstname' => $firstName,
                'lastname' => $lastName,
                'email' => $email
            ];

            if($author->save(false))
                return $author->id;
            return 0;
        }

        return $author->id;
    }


    /*
     * Command:
     * yii msn-en-api 100 asc|desc n|n%
     * Eg: yii msn-en-api 20 desc 50%
     */
    public function actionMsnEnApi($limit = 100, $sort = 'asc', $numberOfLink = null){
        $totalLinks = $apis = CrawlerData::find()->count();
        $apis = CrawlerData::find()->orderBy(['id' => $sort == 'desc'?SORT_DESC:SORT_ASC]);
        
        $queryLimit = $totalLinks;
        if($numberOfLink !== null && $numberOfLink){
            if(strpos($numberOfLink, '%') !== false){
                $limitPercent = (int)$numberOfLink;
                $queryLimit = floor(($limitPercent * $totalLinks)/100);
            }else{
                $queryLimit = (int)$numberOfLink;
            }

            if($queryLimit <= 0)
                $queryLimit = $totalLinks;
        }

        $apis->limit($queryLimit);
        $apis = $apis->all();

        foreach($apis as $api){
            echo " Getting api {$api->api_url} {$api['categories']}\n";
            $command = \Yii::getAlias('@app') . DIRECTORY_SEPARATOR . 'yii crawler/call-msn-api '.base64_encode(json_encode($api->attributes)).' '.$limit;
            exec($command ,  $output, $return_var);
            self::echoLog(implode("\n", $output));
        }
    }

    public function actionCallMsnApi($apiData, $limit = 100){
        if($limit > 100) $limit = 100;
        elseif($limit <= 0) $limit = 10;
        
        $api = @json_decode(base64_decode($apiData), true);
        $data = json_decode($api['api_data'], true);
        $data['Regions'][0]['Modules'][0]['streamCount'] = $limit;
        $data['Regions'][0]['Modules'][0]['streamDocumentLink'] = '/common/abstract/id/cms-amp-AAkc9Ae';
        $data['Regions'][0]['Modules'][0]['streamMarket'] = "en-us";
        $data['Regions'][0]['Modules'][0]['streamVertical'] = "news";
        $data['Regions'][0]['Modules'][0]['streamPagingOffset'] = 0;
        
        $data = json_encode($data);

        if(!$api)
            return;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        curl_setopt($curl, CURLOPT_URL, $api['api_url']);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'x-requested-with: XMLHttpRequest',
            'origin: https://www.msn.com',
            'referer: https://www.msn.com/en-us/news/good-news/',
            'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
            'Content-Type: application/json',
        ));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        
        $result = curl_exec($curl);
        if(!$result){
            echo curl_error($curl);
            curl_close($curl);
            return;
        }

        curl_close($curl);
        
        $ql = QueryList::setHtml($result);
        $items = $ql->find('.rc-item');
        
        for($i = 0; $i < $items->count(); $i++){
            $item = $items->eq($i);
            $apppromocard = $item->find('.apppromocard');
            
            if($apppromocard->count())
                continue;

            $a = $item->find('a');
            echo $a->attr('title'). '-' . $a->attr('href')."\n";

            $a = $item->find('a')->eq(0);
            $pageLink = $a->attr('href');
            
            if(!$pageLink || strpos($pageLink, 'afflnk.microsoft.com')){
                continue;
            }

            $pageLink = 'https://www.msn.com'.$pageLink;
            $img = $item->find('img')->eq(0);
            $imageSrc = $img->attr('data-src');
            $matches = [];
            preg_match('/\/\/(.*?)\?/s', $imageSrc, $matches);
            $imageSrc = '';
            if(isset($matches[1]))
                $imageSrc = 'https://'.$matches[1].'?w=624&m=6&q=60&u=t&o=t&l=f&f=jpg';
            else
                $imageSrc = $img->attr('src');

            $title = trim($a->find('.h3')->text());
            
            self::beforeDownload($title, 'msnen', $pageLink, $imageSrc, json_decode($api['categories']));
        }
    }


    public function actionMsnEnApi_backup(){
        $webUrl = 'https://www.msn.com/en-us/news/';

        $url = 'https://www.msn.com/en-us/news/verticalriverajax';
        $msnApis = [
            'entertainment/gaming' => [
                "APIUrl" => 'https://www.msn.com/en-us/entertainment/verticalriverajax',
                "RequestUri" => "https://www.msn.com/en-us/entertainment/verticalriverajax?ou=https%3A%2F%2Fwww.msn.com%2Fen-us%2Fentertainment%2Fgaming",
                "ExperienceId" => "CCfMRd",
                "key" => "1",
            ],
            'entertainment/celebrity' => [
                "APIUrl" => 'https://www.msn.com/en-us/entertainment/verticalriverajax',
                "RequestUri" => "https://www.msn.com/en-us/entertainment/verticalriverajax?ou=https%3A%2F%2Fwww.msn.com%2Fen-us%2Fentertainment%2Fcelebrit",
                "ExperienceId" => "AA4V4Q3",
                "key" => "1",
            ],
            'entertainment' => [
                "APIUrl" => 'https://www.msn.com/en-us/entertainment/verticalriverajax',
                "RequestUri" => "https://www.msn.com/en-us/entertainment/verticalriverajax?ou=https%3A%2F%2Fwww.msn.com%2Fen-us%2Fentertainment",
                "ExperienceId" => "BB2aYCE",
                "key" => "1",
            ],
            'photos' => [
                "RequestUri" => "https://www.msn.com/en-us/news/verticalriverajax?ou=https%3A%2F%2Fwww.msn.com%2Fen-us%2Fnews%2Fphotos",
                "ExperienceId" => "AA1W24T",
                "key" => "",
            ],
            'fact-check' => [
                "RequestUri" => "https://www.msn.com/en-us/news/verticalriverajax?ou=https%3A%2F%2Fwww.msn.com%2Fen-us%2Fnews%2Ffactcheck",
                "ExperienceId" => "AAmyYQA",
                "key" => "1",
            ],
            'good-news' => [
                "RequestUri" => "https://www.msn.com/en-us/news/verticalriverajax?ou=https%3A%2F%2Fwww.msn.com%2Fen-us%2Fnews%2Fgood-news%2F",
                "ExperienceId" => "AAkc6ZK",
                "key" => "",
            ]
        ];

        foreach($msnApis as $catName => $api){
            $catSlugs = explode('/', $catName);
            $categoryIds = static::getCatIds($catSlugs);
            
            $site = 'msnen';

            if(!$categoryIds){
                self::echoLog("Không tìm thấy danh mục {$catName}");
                continue;
            }

            $data = json_encode([
                "RequestUri" => $api['RequestUri'],
                "ExperienceId" => $api['ExperienceId'],
                "Type" => "homepage",
                "Regions" => [  
                    [ 
                        "key" => 'eventhubriversection'.$api['key'],
                        "type" => "eventhubriversection",
                        "Modules" => [  
                            [
                                "key" => "eventhubrivercontent".$api['key'],
                                "type" => "eventhubrivercontent",
                                "streamCount" => 100,
                                // "streamDocumentLink" => "/common/abstract/id/cms-amp-AAkc9Ae",
                                // "streamMarket" => "en-us",
                                // "streamVertical" => "news",
                                // "streamPagingOffset" => 0,
                            ]
                        ]
                    ]
                ]
            ]);

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

            curl_setopt($curl, CURLOPT_URL, isset($api['APIUrl'])?$api['APIUrl']:$url);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'x-requested-with: XMLHttpRequest',
                'origin: https://www.msn.com',
                'referer: https://www.msn.com/en-us/news/good-news/',
                'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
                'Content-Type: application/json',
            ));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            
            $result = curl_exec($curl);
            if(!$result){
                echo curl_error($curl);
                curl_close($curl);
                continue;
            }

            curl_close($curl);
            
            $ql = QueryList::setHtml($result);
            $items = $ql->find('.rc-item');

            for($i = 0; $i < $items->count(); $i++){
                $item = $items->eq($i);
                $apppromocard = $item->find('.apppromocard');
                
                if($apppromocard->count())
                    continue;

                $a = $item->find('a');
                echo $a->attr('title'). '-' . $a->attr('href')."\n";

                $a = $item->find('a')->eq(0);
                $pageLink = $a->attr('href');
                
                if(!$pageLink || strpos($pageLink, 'afflnk.microsoft.com')){
                    continue;
                }

                $pageLink = 'https://www.msn.com'.$pageLink;
                $img = $item->find('img')->eq(0);
                $imageSrc = $img->attr('data-src');
                $matches = [];
                preg_match('/\/\/(.*?)\?/s', $imageSrc, $matches);
                $imageSrc = '';
                if(isset($matches[1]))
                    $imageSrc = 'https://'.$matches[1].'?w=624&m=6&q=60&u=t&o=t&l=f&f=jpg';
                else
                    $imageSrc = $img->attr('src');

                $title = trim($a->find('.h3')->text());

                self::beforeDownload($title, $site, $pageLink, $imageSrc, $categoryIds);
            }
        }
     }

     public static function saveApiData($jsContainer, $categoryUrl, $categoryIds){
        $experienceId = $jsContainer->attr('data-river-contentxd');
        $apiUri = $jsContainer->attr('data-river-ajaxurl');
        $moduleData = $jsContainer->attr('data-module-id');

        if(!$experienceId || !$apiUri){
            return;
        }

        $hashkey = md5($categoryUrl);
        $crawlerData = CrawlerData::find()->where(['hashkey' => $hashkey])->one();
        
        $apiUrl = 'https://www.msn.com'.$apiUri;
        $moduleData = explode('|', $moduleData);
        $requestUri = $apiUrl.'?ou='.urlencode($categoryUrl);
        
        $data = json_encode([
            "RequestUri" => $requestUri,
            "ExperienceId" => $experienceId,
            "Type" => $moduleData[0],
            "Regions" => [  
                [ 
                    "key" => $moduleData[1],
                    "type" => $moduleData[2],
                    "Modules" => [  
                        [
                            "key" => $moduleData[3],
                            "type" => $moduleData[4],
                            "streamCount" => 100
                        ]
                    ]
                ]
            ]
        ]);

        if($crawlerData && md5($crawlerData->api_data) == md5($data) && $crawlerData->api_url == $apiUrl){
            return;
        }
        
        if(!$crawlerData){
            $crawlerData = new CrawlerData;
            $crawlerData->attributes = [
                'hashkey' => $hashkey,
                'categories' => json_encode($categoryIds),
            ];
        }

        $crawlerData->attributes = [
            'api_url' => $apiUrl,
            'api_data' => $data
        ];
        
        $crawlerData->save();
     }
}
