<?php
namespace app\components;

use Yii;
use luya\admin\models\User;

class Utils extends yii\base\Component
{
    public static function countFileInDirectory($dir){
        if(!is_dir($dir))
            return null;

        $fi = new \FilesystemIterator($dir, \FilesystemIterator::SKIP_DOTS);
        return iterator_count($fi);
    }

    public static function getUploadDir($path, $folder){
        $uploadsDir = $path . DIRECTORY_SEPARATOR .$folder;
        
		if(!is_dir($uploadsDir)){
			$oldmask = umask(0);
            if(!mkdir($uploadsDir, 0777))
                return;
			umask($oldmask);
        }
        
		return $uploadsDir;
    }
    
	public static function uploadFeatureImage($imageUrl, $uploadsDir, $folderName){
        if(!$imageUrl)
            return '';

        $filename = basename($imageUrl);
        $filename = explode('?', $filename);
        $filename = $filename[0];

        if(strpos(basename($imageUrl), 'f=jpg') !== false){
            $filename = str_replace('.img', '.jpg', $filename);
        }

        $desFile = $uploadsDir.'/'.$filename;
        
        if(file_exists($desFile))
            return "{$folderName}/{$filename}";
        
        $data = @file_get_contents($imageUrl);
        
        if(!$data)
            return '';

        @file_put_contents($desFile, $data);
        
        if(file_exists($desFile))
            return "{$folderName}/{$filename}";
		
		return '';
    }

    public static function getUrl($url) {
        $curl = curl_init();
        $header[0] = "Accept: text/xml,application/xml,application/xhtml+xml, text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5"; 
        $header[] = "Cache-Control: max-age=0"; 
        $header[] = "Connection: keep-alive"; 
        $header[] = "Keep-Alive: 300"; 
        $header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7"; 
        $header[] = "Accept-Language: en-us,en;q=0.5"; 
    
        curl_setopt($curl, CURLOPT_URL, $url); 
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; U; Linux x86_64; en-US) AppleWebKit/534.3 (KHTML, like Gecko) Ubuntu/10.04 Chromium/6.0.472.53 Chrome/6.0.472.53 Safari/534.3'); 
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header); 
        curl_setopt($curl, CURLOPT_ENCODING, 'gzip,deflate'); 
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // very important to set it to true, otherwise the content will be not be saved to string
        $html = curl_exec($curl); // execute the curl command
        
        return $html;
    }

    public static function getAuthorId($totalUsers){

        $randId = rand(2, (int)$totalUsers);
        $authorId = User::find()->where(['id' => $randId])->scalar();
        $loop = 3;
        while($loop && !$authorId){
            $randId = rand(2, (int)$totalUsers);
            $authorId = User::find()->where(['id' => $randId])->scalar();
            $loop--;
        }

        return $authorId?$authorId:1;
    }
}