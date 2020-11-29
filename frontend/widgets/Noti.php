<?php
namespace app\widgets;
use Yii;
use yii\bootstrap\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

class Noti extends Widget
{
    /**
     * @var array the alert types configuration for the flash messages.
     * This array is setup as $key => $value, where:
     * - $key is the name of the session flash variable
     * - $value is the bootstrap alert type (i.e. danger, success, info, warning)
     */
    public $alertTypes = [
        'error' => 'danger',
        'danger' => 'danger',
        'success' => 'success',
        'info' => 'info',
        'warning' => 'warning',
    ];

    public $iconTypes = [
        'error' => 'error',
        'danger' => 'not_interested',
        'success' => 'done',
        'info' => 'info',
        'warning' => 'warning',
    ];
    /**
     * @var bool All the flash messages stored for the session are displayed and removed from the session
     * Defaults to true.
     */
    public $useSessionFlash = true;
    /**
     * @var bool render the AnimateAsset
     * Defaults to true.
     */
    public $useAnimation = true;
    /**
     * @inheritdoc
     */
    public function run()
    {
        parent::run();
        if ($this->useSessionFlash) {
            $this->renderMultipleFlashes();
        } else {
            $this->renderMessage();
        }
    }
    /**
     * Render the message
     */
    protected function renderMessage()
    {
        $view = $this->getView();
        $js = "$.notify({$this->getOptions()},{$this->getClientOptions()});";
        $view->registerJs($js, $view::POS_READY);
    }
    /**
     * Get options in the json format
     *
     * @return string
     */
    protected function getOptions()
    {
        $this->options['icon'] = $this->iconTypes[$this->clientOptions['type']];
        return Json::encode($this->options);
    }
    /**
     * Get client options in the json format
     *
     * @return string
     */
    protected function getClientOptions()
    {
        return Json::encode($this->clientOptions);
    }

    protected function renderMultipleFlashes()
    {
      $session = Yii::$app->getSession();
      $flashes = $session->getAllFlashes();
      foreach ($flashes as $type => $data)
      {
        if (isset($this->alertTypes[$type]))
        {
          if (ArrayHelper::isAssociative($data))
          {
              $this->options = ArrayHelper::merge($this->options, $data);
              $this->clientOptions['type'] = $this->alertTypes[$type];
              $this->renderMessage();
          }
          else
          {
              $data = (array)$data;
              $this->processArrayFlashData($data,$type);
          }
          $this->options = [];
          $session->removeFlash($type);
        }
      }
    }

    protected function processArrayFlashData($data,$type)
    {
      foreach ($data as $i => $message)
      {
        $this->options['message'] = $message;
        $this->options['icon'] = $this->iconTypes[$type];
        $this->clientOptions['type'] = $this->alertTypes[$type];
        $this->clientOptions['offset']['y'] = "40";
        $this->clientOptions['offset']['x'] = "20";
        $this->renderMessage();
      }
    }
}
