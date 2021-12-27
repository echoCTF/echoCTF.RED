<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

$this->registerJs(
    '$("#rating select").on("change", function(event, jqXHR, settings) {
        var form = $("#rating");
        if(form.find(".has-error").length) {
            return false;
        }
        $.ajax({
            url: form.attr("action"),
            type: "post",
            data: form.serialize(),
        });

        return false;
    });',
    4,
    'rating-handler'
);

echo Html::beginForm([$action, 'id' => $id], 'post', ['enctype' => 'multipart/form-data', 'id'=>'rating']);
echo Html::dropDownList(
    'rating', //name
    $model->rating,  //select
    ArrayHelper::map($ratings, 'id', 'name'),
    ['class'=>'btn-block input-group-btn selectpicker','data-style'=> $model->rating>=0 ? "btn-info" : ""]
  );
echo Html::endForm();
