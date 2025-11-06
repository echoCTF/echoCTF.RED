<?php

namespace app\components;

use Yii;
use yii\apidoc\helpers\ApiMarkdown;
use yii\apidoc\models\Context;
use yii\helpers\HtmlPurifier;
use yii\helpers\Markdown;
use yii\helpers\StringHelper;
use yii\helpers\Html;

class Formatter extends \yii\i18n\Formatter
{
  public $dateFormat = 'medium';
  public $timeFormat = 'medium';
  public $datetimeFormat = 'medium';
  public $timeZone = 'UTC';
  public $divID = 'markdown-content';
  public $nullDisplay = '<span class="not-set small">(empty)</span>';

  public $purifierConfig = [
    'URI' => [
      'SafeIframeRegexp' => '%^(https?:)?//(www\.youtube(?:-nocookie)?\.com/embed/|youtu\.be|player\.vimeo\.com/video/)%',
    ],
    'Core' => [
      'Encoding' => 'UTF-8',
    ],
    'HTML' => [
      'SafeIframe' => true,
      'SafeObject' => true,
      'SafeEmbed' => true,
      'AllowedElements' => [
        'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
        'strong', 'em', 'b', 'i', 'u', 's',
        'span', 'pre', 'code',
        'table', 'tr', 'td', 'th', 'a', 'p',
        'br', 'blockquote', 'ul', 'ol', 'li',
        'img', 'embed', 'iframe'
      ],
    ],
    'Attr' => [
      'EnableID' => true,
    ],
  ];


  /**
   * {@inheritdoc}
   */
  public function init()
  {
    parent::init();
  }

  public function asDatetimerel($value)
  {
    if ($value === null) {
      return $this->nullDisplay;
    }
    return $this->asDatetime($value) . ' (' . $this->asRelativeTime($value) . ')';
  }

  /**
   * Format as normal markdown without class link extensions.
   *
   * @param $markdown
   * @return string
   */
  public function asMarkdown($markdown)
  {
    Markdown::$flavors['gfm'] = [
      'class' => \app\components\Markdown::class,
      'html5' => true,
    ];

    $html = Markdown::process($markdown, 'gfm');

    $html = $this->replaceHeadlines($html);

    $output = HtmlPurifier::process($html, $this->purifierConfig);

    return '<div id="' . $this->divID . '" class="markdown">' . $output . '</div>';
  }

  /**
   * Replace headlines in markdown to avoid users using H1 and H2 tags.
   * @param string $html
   * @return string
   */
  private function replaceHeadlines($html)
  {
    // replace level of headline tags, h1 -> h3, ...
    return preg_replace_callback('~(</?h)(\d)( |>)~i', function ($matches) {
      $level = $matches[2] + 2;
      if ($level > 6) {
        $level = 6;
      }
      return $matches[1] . $level . $matches[3];
    }, $html);
  }

  /**
   * Formats the value as an HTML-encoded <pre><code></code></pre> blocks
   * @param string|null $value the value to be formatted.
   * @return string the formatted result.
   */
  public function asCodeblock($value)
  {
    if ($value === null) {
      return $this->nullDisplay;
    }

    return sprintf("<pre><code>%s</pre></code>", Html::encode($value));
  }

  /**
   * Formats the value as an HTML link to player view
   * @param string|null $value the value to be formatted.
   * @return string the formatted result.
   */
  public function asLinkPlayer($value)
  {
    if ($value === null || ($model = \app\modules\frontend\models\Player::findOne(['username' => $value])) === null) {
      return $this->nullDisplay;
    }
    return Html::a($model->username, ['/frontend/player/view', 'id' => $model->id]);
  }

  /**
   * Formats the value as an HTML link to profile full view
   * @param string|null $value the value to be formatted.
   * @return string the formatted result.
   */
  public function asLinkProfile($value)
  {
    if ($value === null || ($model = \app\modules\frontend\models\Player::findOne(['username' => $value])) === null) {
      return $this->nullDisplay;
    }
    return Html::a($model->username, ['/frontend/profile/view-full', 'id' => $model->profile->id], ['class' => 'profile-link', 'title' => \Yii::t('app', 'Go to profile of [{username}]', ['username' => $model->username])]);
  }

  /**
   * Returns a displayable offense type
   * @param string|null $value the value to be formatted.
   * @return string the formatted result.
   */
  public function asPlayerType($value)
  {
    switch ($value) {
      case 'offense':
        return 'Offense';
      case 'defense':
        return 'Defense';
      default:
        return 'Both';
    }
  }

  /**
   * Formats the value as a player status
   * @param string|null $value the value to be formatted.
   * @return string the formatted result.
   */
  public function asPlayerStatus($value)
  {
    if (array_key_exists($value, \app\modules\frontend\models\Player::STATUSES))
      return \app\modules\frontend\models\Player::STATUSES[$value];
    return $value;
  }
}
