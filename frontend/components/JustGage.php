<?php

/**
 * JustGage class file.
 *
 * @author Simon Smith <simon@simonsmith.ca>
 * @link https://github.com/simonmesmith/yii2-justgage/
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @version 1.0.0
 */

namespace app\components;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;
use yii\web\JsExpression;

class JustGage extends \yii\base\Widget
{


    /**
     * @var array|null $options
     */
  public $options=[];

    /**
     * @var array|null $htmlOptions
     */
  public $htmlOptions=[];

  /**
   * Renders the widget.
   */
  public function run()
  {
    // 1. Get or generate an ID.
    if(isset($this->htmlOptions['id']))
    {
      $this->id=$this->htmlOptions['id'];
    }
    else
    {
      $this->id=$this->htmlOptions['id']=$this->getId();
    }

    // 2. Render the div for the chart.
    echo Html::tag('div', '', $this->htmlOptions);

    // 3. Merge options with default options.
    $defaultOptions=array('id'=> $this->id, 'min'=>0, 'max'=>100);
    $this->options=ArrayHelper::merge($defaultOptions, $this->options);

    // 4. Register assets.
    $this->registerAssets();

    // 5. Render the widget.
    parent::run();

  }

  /**
   * Registers required assets.
   */
  protected function registerAssets()
  {

    // 1. Register the necessary assets
    JustGageAssets::register($this->view);
    // 2. Prepare and register the JavaScript code block.
    $jsOptions=Json::encode($this->options);
    $jsOptions=str_replace('"textRenderer":"func', '"textRenderer":func', $jsOptions);
    $jsOptions=str_replace('\"\";}"', '"";}', $jsOptions);
    //die(var_dump($jsOptions));
    $js="var gage = new JustGage($jsOptions);";
    $key=__CLASS__.'#'.$this->id;
    $this->view->registerJs($js, View::POS_LOAD, $key);

  }

}
