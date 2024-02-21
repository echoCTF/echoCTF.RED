<?php
namespace app\commands;

use Yii;
use yii\console\Controller;
use app\modules\infrastructure\models\TargetMetadata;
use yii\console\ExitCode;

class MetadataController extends Controller
{
  public $defaultAction='view';

  /**
   * Delete a given target metadata
   * @param $id The id of a specific target to delete metadata
   * @param $name The name of a specific target to delete metadata
   */
  public function actionDelete(int $id=0,$name=false)
  {
    if($id>0 && ($metadata = TargetMetadata::findOne($id))==null)
    {
      $this->stderr("Error: No record with given ID found!\n");
      return ExitCode::UNSPECIFIED_ERROR;
    }
    if($name!==false && ($metadata = TargetMetadata::find()->joinWith('target')->where(['target.name'=>$name])->one())==null)
    {
      $this->stderr("Error: No record with given name found!\n");
      return ExitCode::UNSPECIFIED_ERROR;
    }
    if($metadata->delete()!==false)
      return ExitCode::OK;
  }

  /**
   * View target metadata
   * @param $id The id of a specific target to view metadata
   * @param $name The name of a specific target to view metadata
   */
  public function actionView(int $id=0,$name=false)
  {
    if($id>0 && ($metadata = TargetMetadata::findOne($id))==null)
    {
      $this->stderr("Error: No record with given ID found!\n");
      return ExitCode::UNSPECIFIED_ERROR;
    }
    if($name!==false && ($metadata = TargetMetadata::find()->joinWith('target')->where(['target.name'=>$name])->one())==null)
    {
      $this->stderr("Error: No record with given name found!\n");
      return ExitCode::UNSPECIFIED_ERROR;
    }
    echo $this->renderPartial('view',[
      'metadata'=>$metadata
    ]);
  }

  /**
   * Get/Set target scenario
   * @param $id The id of a specific target to view metadata
   * @param $content (optional) If provided set the scenario instead of displaying it
   */
  public function actionScenario(int $id,$content=null)
  {
    if($id>0 && ($metadata = TargetMetadata::findOne($id))==null)
    {
      $this->stderr("Error: No record with given ID found!\n");
      return ExitCode::UNSPECIFIED_ERROR;
    }
    if($content!==null)
    {
      $metadata->scenario=$content;
      $metadata->save();
    }
    echo $metadata->scenario,"\n";
  }

  /**
   * Get/Set target solution
   * @param $id The id of a specific target to view metadata
   * @param $content (optional) If provided set the solution instead of displaying it
   */
  public function actionSolution(int $id,$content=null)
  {
    if($id>0 && ($metadata = TargetMetadata::findOne($id))==null)
    {
      $this->stderr("Error: No record with given ID found!\n");
      return ExitCode::UNSPECIFIED_ERROR;
    }
    if($content!==null)
    {
      $metadata->solution=$content;
      $metadata->save();
    }
    echo $metadata->solution,"\n";
  }

  /**
   * Get/Set target pre exploitation credits
   * @param $id The id of a specific target to view metadata
   * @param $content (optional) If provided set the pre_credits instead of displaying it
   */
  public function actionPrecredits(int $id,$content=null)
  {
    if($id>0 && ($metadata = TargetMetadata::findOne($id))==null)
    {
      $this->stderr("Error: No record with given ID found!\n");
      return ExitCode::UNSPECIFIED_ERROR;
    }
    if($content!==null)
    {
      $metadata->pre_credits=$content;
      $metadata->save();
    }
    echo $metadata->pre_credits,"\n";
  }

  /**
   * Get/Set target pre exploitation details
   * @param $id The id of a specific target to view metadata
   * @param $content (optional) If provided set the pre_exploitation instead of displaying it
   */
  public function actionPreexploitation(int $id,$content=null)
  {
    if($id>0 && ($metadata = TargetMetadata::findOne($id))==null)
    {
      $this->stderr("Error: No record with given ID found!\n");
      return ExitCode::UNSPECIFIED_ERROR;
    }
    if($content!==null)
    {
      $metadata->pre_exploitation=$content;
      $metadata->save();
    }
    echo $metadata->pre_exploitation,"\n";
  }

  /**
   * Get/Set target post exploitation credits
   * @param $id The id of a specific target to view metadata
   * @param $content (optional) If provided set the post_credits instead of displaying it
   */
  public function actionPostcredits(int $id,$content=null)
  {
    if($id>0 && ($metadata = TargetMetadata::findOne($id))==null)
    {
      $this->stderr("Error: No record with given ID found!\n");
      return ExitCode::UNSPECIFIED_ERROR;
    }
    if($content!==null)
    {
      $metadata->post_credits=$content;
      $metadata->save();
    }
    echo $metadata->post_credits,"\n";
  }

  /**
   * Get/Set target post exploitation details
   * @param $id The id of a specific target to view metadata
   * @param $content (optional) If provided set the post_exploitation instead of displaying it
   */
  public function actionPostexploitation(int $id,$content=null)
  {
    if($id>0 && ($metadata = TargetMetadata::findOne($id))==null)
    {
      $this->stderr("Error: No record with given ID found!\n");
      return ExitCode::UNSPECIFIED_ERROR;
    }
    if($content!==null)
    {
      $metadata->post_exploitation=$content;
      $metadata->save();
    }
    echo $metadata->post_exploitation,"\n";
  }


  /**
   * Override for the command view paths
   */
  public function getViewPath()
  {
      return Yii::getAlias('@app/views/console/metadata');
  }

}