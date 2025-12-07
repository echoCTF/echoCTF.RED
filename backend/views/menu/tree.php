<?php

use yii\helpers\Url;
use yii\web\JqueryAsset;

\yii\web\JqueryAsset::register($this); // ensures $ is available
use yii\web\View;

$this->registerCssFile('//cdnjs.cloudflare.com/ajax/libs/nestable2/1.6.0/jquery.nestable.min.css');
$this->registerJsFile('//cdnjs.cloudflare.com/ajax/libs/nestable2/1.6.0/jquery.nestable.min.js', ['depends' => JqueryAsset::class]);
$this->title = Yii::t('app', 'Menu Tree');
$this->params['breadcrumbs'][] = ['label' => 'Menu', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['tree']];

$saveUrl = Url::to(['save-tree']);
?>
<div class="menu-tree-index">

  <h1>Menu Structure</h1>

  <div class="dd" id="menuTree">
    <?= buildMenuTree($items) ?>
  </div>

  <button id="saveBtn" class="btn btn-success" style="margin-top:20px">Save Order</button>
</div>
<?php
function buildMenuTree($items, $parent = null)
{
  $html = "<ol class='dd-list'>";
  foreach ($items as $item) {
    if ($item['parent_id'] == $parent) {
      $html .= "<li class='dd-item' data-id='{$item['id']}'>
                <div class='dd-handle'>{$item['label']}</div>";

      $children = array_filter($items, fn($c) => $c['parent_id'] == $item['id']);
      if ($children)
        $html .= buildMenuTree($items, $item['id']);

      $html .= "</li>";
    }
  }
  return $html . "</ol>";
}
$js = <<<JS
$('#menuTree').nestable();

$('#saveBtn').on('click', function () {
    var tree = $('#menuTree').nestable('serialize');
    $.post('{$saveUrl}', {tree: tree}, function (r) {
        if (r.success) {
            alert('Saved!');
        }
    });
});
JS;

$this->registerJs($js, View::POS_READY);
