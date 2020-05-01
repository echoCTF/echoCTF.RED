<?php

namespace app\widgets;

use yii\helpers\Html;

/**
 * This is just an example.
 */
class Card extends \yii\base\Widget
{
    /*
     * boolean/string Card header type
     * true | false | header-icon | header-text | img-top | img-bottom | chart
     */
    public $header=true;
    /*
     * string Card header title (optional) .card-title class
     */
    public $title;
    /*
     * string Card header subtitle (optional) .category class
     */
    public $subtitle;
    /*
     * string Card header color
     * primary | info | success | warning | danger | rose
     */
    public $color='transparent';
    /*
     * string Take effect only if $header = img-top | img-bottom
     */
    public $url;
    /*
     * string material icon class, Take effect only if $header = header-icon
     */
    public $icon;
    /*
     * string Take effect only if $header = img-top | img-bottom
     */
    public $overlay;
    /*
     * string Take effect only if $header = chart
     */
    public $chartId;
    /*
     * boolean If you want to use the cards on white background you can remove it’s shadow
     * default to false
     */
    public $plain=false;
    /*
     * string If you want align car's content
     * text-right | text-center | card-stats
     */
    public $type='';
    /*
     * boolean/string Card footer html
     * @var string|false $footer
     */
    public $footer=false;

    /*
     *
     * FOR THE BODY CONTENT (inside .card-body class div) user can use varies content types Ex.:
     *
     * <h4 class="card-title">Card title</h4>
     * <h6 class="card-subtitle mb-2 text-muted">Card subtitle</h6>
     * <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
     * <a href="#0" class="card-link">Card link</a>
     *
     * <ul class="list-group list-group-flush">
     *  <li class="list-group-item">Cras justo odio</li>
     *  <li class="list-group-item">Dapibus ac facilisis in</li>
     *  <li class="list-group-item">Vestibulum at eros</li>
     * </ul>
     *
     * <blockquote class="blockquote mb-0">
     *   <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante.</p>
     *   <footer class="blockquote-footer">Someone famous in <cite title="Source Title">Source Title</cite></footer>
     * </blockquote>
     *
     * FOR MORE DETAILS GO https://demos.creative-tim.com/material-dashboard/docs/2.1/components/card.html
     */

    public function init()
    {
        parent::init();
        ob_start();
    }

    public function run()
    {
        $content=ob_get_clean();
        $body='';
        if(!empty($content) && $content != null && $content != '')
            $body='<div class="card-body">'.$content.'</div>';

        return '<div class="card '.$this->type.'">'.
                  $this->getHeaderhtml().$body.$this->getFooterhtml().
                '</div>';
    }

    /**
     * @return string
     */
    public function getHeaderhtml()
    {
        if($this->header !== false)
        {
            switch($this->header) {
                case 'img-top':
                    return '<img class="card-img-top" src="'.Html::encode($this->url).'" alt="">';
                case 'header-icon':
                    if($this->type != 'card-stats')
                        return '<div class="card-header card-header-icon card-header-'.$this->color.'">
                                <div class="card-icon">
                                  '.$this->icon.'
                                </div>
                              </div>';
                    else
                        return '<div class="card-header card-header-icon card-header-'.$this->color.'">
                                <div class="card-icon">
                                  '.$this->icon.'
                                </div>
                                <p class="card-category">'.Html::encode($this->subtitle).'</p>
                                <h4 class="card-title">'.Html::encode($this->title).'</h4>
                              </div>';
                case 'header-text':
                    return '<div class="card-header card-header-text card-header-'.$this->color.'">
                                <div class="card-text">
                                    <h4 class="card-title">'.Html::encode($this->title).'</h4>
                                    <p class="category">'.Html::encode($this->subtitle).'</p>
                                </div>
                              </div>';
                case 'chart':
                    return '<div class="card-header card-chart card-header-'.$this->color.'">
                              <div class="ct-chart" id="'.$this->chartId.'"></div>
                            </div>';
                default:
                    return '<div class="card-header">
                                <h4 class="card-title">'.Html::encode($this->title).'</h4>
                                <p class="category">'.Html::encode($this->subtitle).'</p>
                              </div>';
            }
        }
        return '<div></div>';
    }

    /**
     * @return string
     */
    public function getFooterhtml()
    {
        if($this->header == 'img-bottom')
            return '<img class="card-img-bottom" src="'.Html::encode($this->url).'" alt="">';

        if(is_string($this->footer))
            return '<div class="card-footer text-muted">'.$this->footer.'</div>';

        return '<div></div>';
    }

}
