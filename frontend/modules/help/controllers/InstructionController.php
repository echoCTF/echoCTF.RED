<?php

namespace app\modules\help\controllers;

use Yii;
use app\modules\help\models\Instruction;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;

/**
 * InstructionController implements the CRUD actions for Instruction model.
 */
class InstructionController extends Controller
{
    /**
     * Lists all Instruction models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider=new ActiveDataProvider([
            'query' => Instruction::find()->orderBy(['weight'=>SORT_ASC, 'id'=>SORT_ASC]),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }
}
