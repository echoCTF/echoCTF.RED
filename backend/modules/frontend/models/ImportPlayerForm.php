<?php

namespace app\modules\frontend\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class ImportPlayerForm extends Model
{
    /**
     * @var UploadedFile
     */
    public $csvFile;
    public $heading_first;
    public $csvRecords=null;
    public $player_ssl;
    public function rules()
    {
        return [
            [['heading_first', 'player_ssl'], 'boolean'],
            [['csvFile'], 'file', 'skipOnEmpty' => false, 'extensions' => ['csv'], 'checkExtensionByMimeType'=>false],
        ];
    }
    public function attributeLabels()
    {
        return [
            'heading_first' => '1st line is Heading?',
            'csvFile' => 'CSV File to import',
            'player_ssl' => 'Generate Player SSL?',
        ];
    }

    public function upload()
    {
        if($this->validate())
        {
            $this->csvFile->saveAs(\Yii::getAlias('@webroot').'/uploads/'.$this->csvFile->baseName.'.'.$this->csvFile->extension);
            return true;
        }
        else
        {
            return false;
        }
    }
    public function parseCSV()
    {
      $fname=\Yii::getAlias('@webroot').'/uploads/'.$this->csvFile->baseName.'.'.$this->csvFile->extension;
      if(($handle=fopen($fname, "r")) !== FALSE)
      {
          while(($data=fgetcsv($handle, 1000, ",")) !== FALSE) {
              //$num = count($data);
              $this->csvRecords[]=$data;
          }
          fclose($handle);
      }
      if($this->heading_first === '1')
        array_shift($this->csvRecords);

      return true;
    }

    private function processCsvRecords()
    {
      foreach($this->csvRecords as $rec)
      {
        $p=new Player;
        $p->username=$rec[0];
        $p->fullname=$rec[0];
        $p->email=$rec[0];
        $p->academic=$rec[2] == 'no' ? 0 : 1;
        $p->saveWithSsl();
        $this->processTeam($rec,$p);
      }
    }

    private function processTeam($rec,$p)
    {
      if(Team::find()->where(['name' => $rec[1]])->exists())
      {
        $team=Team::find()->where(['name' => $rec[1]])->one();
        $tp=new TeamPlayer;
        $tp->team_id=$team->id;
        $tp->player_id=$p->id;
        $tp->save();
      }
      else
      {
        $team=new Team;
        $team->name=$rec[1];
        $team->owner_id=$p->id;
        $team->academic=$rec[2] == 'no' ? 0 : 1;
        $team->save();
      }

    }

}
