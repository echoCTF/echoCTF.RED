<?php
use yii\helpers\Html;

echo Html::tag('h1','Create Menu Item');
echo $this->render('_form', ['model'=>$model]);
