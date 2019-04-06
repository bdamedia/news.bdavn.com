<?php
namespace app\modules\news\widgets;

use yii\base\Widget;
use yii\helpers\Html;

class AdsWidget extends Widget
{
    public $ads = [];
    public $position = 0;
    public $className = 'ads';

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        if(!$this->ads || !$this->position || !isset($this->ads[$this->position]))
            return;

        $ads = $this->ads[$this->position];
        $size = '[[728, 90], [320, 50]]';
        if($this->position == \app\modules\news\models\Ads::POS_SIDEBAR)
            $size = '[300, 600]';
        
        $html = '<div class="'.$this->className.'">';
        foreach($ads as $content){
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
                $html .= "
                <div id=\"{$id}\"></div>
                <script>
                    googletag.cmd.push(function() {
                        googletag.defineSlot('{$slot}', {$size}, '{$id}').addService(googletag.pubads());

                        googletag.pubads().enableSingleRequest();
                        googletag.pubads().collapseEmptyDivs();
                        googletag.enableServices();
                    });
                </script>";
            }else{
                $html .= $content;
            }
        }
        
        $html .= '</div>';
        return $html;
    }
}
?>