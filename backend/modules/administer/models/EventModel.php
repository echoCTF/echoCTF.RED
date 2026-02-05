<?php

namespace app\modules\administer\models;

use yii\base\Model;
use Yii;

class EventModel extends Model
{
  public $Name;
  public $Definer;
  public $Time_zone;
  public $Type;
  public $Execute_at;
  public $Interval_value;
  public $Interval_field;
  public $Starts;
  public $Ends;
  public $Status;
  public $On_completion;
  public $Created;
  public $Last_altered;
  public $Last_executed;
  public $Event_comment;
  public $Originator;
  public $character_set_client;
  public $collation_connection;
  public $Database_collation;

  public function getPrimaryKey()
  {
    return 'Name';
  }

  public function getPrimaryKeyValue()
  {
    return $this->Name;
  }
  public function rules()
  {
    return [[
      [
        'Name',
        'Definer',
        'Time_zone',
        'Type',
        'Execute_at',
        'Interval_value',
        'Interval_field',
        'Starts',
        'Ends',
        'Status',
        'On_completion',
        'Created',
        'Last_altered',
        'Last_executed',
        'Event_comment',
        'Originator',
        'character_set_client',
        'collation_connection',
        'Database_collation'
      ],
      'safe'
    ]];
  }

  public static function getAll()
  {
    $rows = Yii::$app->db->createCommand('SHOW EVENTS')->queryAll();
    $models = [];

    foreach ($rows as $row) {
      $model = new self();
      $model->Name = $row['Name'];
      $model->Definer = $row['Definer'];
      $model->Time_zone = $row['Time zone'];
      $model->Type = $row['Type'];
      $model->Execute_at = $row['Execute at'];
      $model->Interval_value = $row['Interval value'];
      $model->Interval_field = $row['Interval field'];
      $model->Starts = $row['Starts'];
      $model->Ends = $row['Ends'];
      $model->Status = $row['Status'];
      $model->Originator = $row['Originator'];
      $model->character_set_client = $row['character_set_client'];
      $model->collation_connection = $row['collation_connection'];
      $model->Database_collation = $row['Database Collation'];
      $models[] = $model;
    }

    return $models;
  }

  public static function dropEvent($name)
  {
    return Yii::$app->db->createCommand("DROP EVENT `$name`")->execute();
  }
}
