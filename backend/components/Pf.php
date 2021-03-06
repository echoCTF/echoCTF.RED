<?php
namespace app\components;

use Yii;

use yii\base\Component;
use yii\base\InvalidConfigException;

/**
 * Handle Pf related tasks
 * @method load_table_file
 * @method load_anchor_file
 * @method store
 */
class Pf extends Component
{
  const PFCTL="/sbin/pfctl -q";


  /**
   * Load a pf table from its file
   * @param string $table
   * @param string $file
   * @return boolean
   */
  public static function load_table_file($table, $file)
  {
    passthru(self::PFCTL." -t $table -T replace -f $file",$return_var);
    return intval($return_var)===0;
  }

  /**
   * Load a pf anchor from its file
   * @param string $anchor
   * @param string $file
   * @return boolean
   */
  public static function load_anchor_file($anchor, $file)
  {
    passthru(self::PFCTL." -a $anchor -Fr -f $file",$return_var);
    return intval($return_var)===0;
  }

  /**
   * Store the imploded $contents[] into $file
   * @param string $file
   * @param array $contents
   * @return boolean
   */
  public static function store($file, $contents)
  {
    if(empty($contents)) return false;
    try
    {
      return file_put_contents($file, implode("\n", $contents)."\n")!==false;
    }
    catch(\Exception $e)
    {
      echo "Failed to save {$file}\n";
    }
    return false;
  }

  public static function allowToNetwork($target)
  {
    $clients_table=self::clients_table($target);
    $targets_table=$target->network->codename;
    return sprintf("pass quick inet from <%s> to <%s> tagged OFFENSE_REGISTERED allow-opts received-on tun keep state",$clients_table,$targets_table);
  }

  public static function allowToClient($target)
  {
    $clients_table=self::clients_table($target);
    $targets_table=$target->network->codename;
    return sprintf("pass quick from <%s> to <%s>",$targets_table,$clients_table);
  }

  public static function clients_table($target)
  {
    if($target->network->public===false)
    {
      return $target->network->codename."_clients";
    }
    else
    {
      return "offense_activated";
    }

  }

}
