<?php
use yii\widgets\ListView;
use yii\data\ActiveDataProvider;
use app\modules\speedprogramming\models\SpeedSolution;

$dataProvider = new ActiveDataProvider([
    'query' => SpeedSolution::find()->where(['problem_id'=>$problem->id])->orderBy(['created_at'=>SORT_ASC]),
    'pagination' => [
        'pageSize' => 20,
    ],
]);
?>
<h3 class="text-info">Speed programming Submissions:</h3>
<?php
echo ListView::widget([
    'dataProvider' => $dataProvider,
    'summary'=>false,
    'itemView' => '_speed_item',
]);
?>
<hr/>
