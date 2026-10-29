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
  const PFCTL = "/sbin/pfctl -q";


  /**
   * Load a pf table from its file
   * @param string $table
   * @param string $file
   * @return boolean
   */
  public static function load_table_file($table, $file)
  {
    @passthru(self::PFCTL . " -t " . escapeshellarg($table) . " -T replace -f " . escapeshellarg($file), $return_var);
    return intval($return_var) === 0;
  }

  /**
   * Load a pf anchor from its file
   * @param string $anchor
   * @param string $file
   * @return boolean
   */
  public static function load_anchor_file($anchor, $file)
  {
    @passthru(self::PFCTL . " -a " . escapeshellarg($anchor) . " -Fr -f " . escapeshellarg($file) . " >/dev/null 2>&1", $return_var);
    return intval($return_var) === 0;
  }

  /**
   * Store the imploded $contents[] into $file
   * @param string $file
   * @param array $contents
   * @return boolean
   */
  public static function store($file, $contents)
  {
    if (empty($contents)) return file_put_contents($file, "\n") !== false;
    try {
      return file_put_contents($file, implode("\n", $contents) . "\n") !== false;
    } catch (\Exception $e) {
      echo "Failed to save {$file}\n";
    }
    return false;
  }

  public static function allowToNetwork($target, $clients_table = null, $targets_table = null)
  {
    if ($clients_table === null)
      $clients_table = self::clients_table($target);
    if ($targets_table === null)
      $targets_table = $target->network->codename;
    return sprintf("pass quick inet from <%s> to <%s> tagged OFFENSE_REGISTERED allow-opts received-on tun keep state %s", $clients_table, $targets_table, \Yii::$app->sys->pf_state_limits);
  }

  public static function allowToClient($target, $clients_table = null, $targets_table = null)
  {
    if ($clients_table === null)
      $clients_table = self::clients_table($target);
    if ($targets_table === null)
      $targets_table = $target->network->codename;
    return sprintf("pass quick from <%s> to <%s> keep state (pflow)", $targets_table, $clients_table);
  }

  /**
   * Check that a name can be used as a pf table name and is safe to place in a rule
   * (pf table names are limited to 31 characters)
   * @param string $name
   * @return boolean
   */
  public static function valid_table_name($name)
  {
    return is_string($name) && preg_match('/^[A-Za-z0-9_]{1,31}$/', $name) === 1;
  }

  public static function clients_table($target)
  {
    if ($target->network->public === false) {
      return $target->network->codename . "_clients";
    } else {
      return "offense_activated";
    }
  }

  /**
   * Escape a space separated list of addresses as separate shell arguments
   * @param string|null $ips
   * @return string
   */
  protected static function escape_ips($ips)
  {
    return implode(' ', array_map('escapeshellarg', preg_split('/\s+/', trim((string) $ips), -1, PREG_SPLIT_NO_EMPTY)));
  }

  public static function add_table_ip($table, $ip, $replace = false)
  {
    if ($replace)
      @passthru(self::PFCTL . " -t " . escapeshellarg($table) . " -T replace " . self::escape_ips($ip) . " >/dev/null 2>&1", $return_var);
    else
      @passthru(self::PFCTL . " -t " . escapeshellarg($table) . " -T add " . self::escape_ips($ip) . " >/dev/null 2>&1", $return_var);
  }

  public static function del_table_ip($table, $ip)
  {
    @passthru(self::PFCTL . " -t " . escapeshellarg($table) . " -T delete " . self::escape_ips($ip) . " >/dev/null 2>&1", $return_var);
  }

  public static function flush_table($table, $unlink = false)
  {
    @passthru(self::PFCTL . " -t " . escapeshellarg($table) . " -T flush >/dev/null 2>&1", $return_var);
    if ($unlink)
      @unlink('/etc/' . $table . '.conf');
  }

  public static function kill_table($table, $unlink = false)
  {
    @passthru(self::PFCTL . " -t " . escapeshellarg($table) . " -T kill >/dev/null 2>&1", $return_var);
    if ($unlink)
      @unlink('/etc/' . $table . '.conf');
  }
}
