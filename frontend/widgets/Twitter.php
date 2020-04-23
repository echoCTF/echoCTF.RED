<?php
/**
 * Twitter widget
 *
 * use app\widgets\Twitter;
 * Twitter::widget([
 *    'message'=>'Message to tweet',
 *    'url'=>'url to include to tweet',
 *    'related'=>'related twitter account',
 *    'linkOptions'=>['class'=>'TweetThis'],
 *    'icon'=>'<i class="fab fa-twitter-round"></i>'
 * ]);
 *
 */
namespace app\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Url;

class Twitter extends Widget{
    private $twUrl="https://twitter.com/intent/tweet";
    private $_query;
    /* The message to render */
    public $message;
    /* The model to draw info from */
    public $model;
    /* the URL to include to the tweet */
    public $url;
    /* default related twitter account */
    public $related='echoCTF';
    public $linkOptions=['class'=>'TweetThis','target'=>'_blank','rel'=>"noreferrer" ];
    public $icon='<i class="fab fa-twitter"></i>';
    public $hashtags='#echoCTF #CTF #Hacking';
    public $via="echoCTF";

    public function init(){
        parent::init();
        if($this->message===null){
            $this->message= 'Tweet this';
        }
        if($this->url===NULL) {
                  $this->url=Url::to('','https');
        }

        $this->message=sprintf("%s via @%s\n%s",Html::encode(strip_tags($this->message)),$this->via,Html::encode($this->hashtags));
    }

    public function run(){
        $linkParams=[
          'url'=>Html::encode($this->url),
          'text'=>$this->message,
          'related'=>Html::encode($this->related)
        ];

        $tweet_query=http_build_query($linkParams);
        $linkTo=sprintf("%s?%s",$this->twUrl,$tweet_query);
        $this->linkOptions['aria-label']="Tweet this!";
        $this->linkOptions['title']="Tweet this!";
        return Html::a($this->icon,$linkTo , $this->linkOptions);
    }
}
