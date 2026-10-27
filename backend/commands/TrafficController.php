<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\console\Exception as ConsoleException;
use app\components\NetflowV5;
use app\modules\frontend\models\Player;

/**
 * Per player, per target traffic accounting.
 *
 * pf on the VPN gateway exports every player/target state through pflow(4)
 * (NetFlow v5) to 127.0.0.1:9995. traffic/collect receives these flows,
 * resolves the VPN address to a player and the target address to a target,
 * and accumulates packets and bytes into the player_target_traffic table.
 */
class TrafficController extends Controller
{
  /** @var array pending["src:dst"] = [packets, bytes] */
  private $pending = [];
  private $stats = ['datagrams' => 0, 'invalid' => 0, 'records' => 0, 'stored' => 0, 'unmatched' => 0];
  private $running = true;

  /**
   * Receive pflow (NetFlow v5) datagrams and store per player/target counters.
   * @param string $listen address:port to bind the UDP listener on
   * @param int $flush seconds between database flushes
   * @param bool $verbose print statistics on every flush
   */
  public function actionCollect($listen = '127.0.0.1:9995', int $flush = 30, $verbose = false)
  {
    $sock = @stream_socket_server('udp://' . $listen, $errno, $errstr, STREAM_SERVER_BIND);
    if ($sock === false)
      throw new ConsoleException("Unable to bind udp://{$listen}: {$errstr} ({$errno})");

    if (function_exists('pcntl_async_signals')) {
      pcntl_async_signals(true);
      $stop = function () {
        $this->running = false;
      };
      pcntl_signal(SIGTERM, $stop);
      pcntl_signal(SIGINT, $stop);
    }

    printf("%s traffic/collect listening on udp://%s, flushing every %ds\n", date('Y-m-d H:i:s'), $listen, $flush);
    $nextFlush = time() + $flush;

    while ($this->running) {
      $r = [$sock];
      $w = $e = null;
      $wait = max(0, $nextFlush - time());
      if (@stream_select($r, $w, $e, $wait) > 0) {
        // drain everything that is queued before going back to select
        while (($data = @stream_socket_recvfrom($sock, 65535)) !== false && $data !== '') {
          $this->ingest($data);
          $r = [$sock];
          $w = $e = null;
          if (@stream_select($r, $w, $e, 0) < 1)
            break;
        }
      }

      if (time() >= $nextFlush) {
        $this->flush($verbose);
        $nextFlush = time() + $flush;
      }
    }

    $this->flush(true);
    fclose($sock);
    return ExitCode::OK;
  }

  /**
   * Show the traffic a player generated, optionally for a single target.
   * @param string $player player id or username
   * @param string|null $target target id or name
   */
  public function actionShow($player, $target = null)
  {
    $p = Player::find()->where(['username' => $player])->orWhere(['id' => $player])->one();
    if ($p === null)
      throw new ConsoleException("Player [{$player}] not found");

    $q = (new \yii\db\Query())
      ->select(['t.name', 'ptt.*'])
      ->from(['ptt' => 'player_target_traffic'])
      ->innerJoin(['t' => 'target'], 't.id=ptt.target_id')
      ->where(['ptt.player_id' => $p->id])
      ->orderBy(['ptt.bytes_sent' => SORT_DESC]);
    if ($target !== null)
      $q->andWhere(['or', ['t.name' => $target], ['t.id' => $target]]);

    printf("%-24s %12s %12s %14s %14s  %s\n", 'target', 'pkts sent', 'pkts recv', 'bytes sent', 'bytes recv', 'last seen');
    foreach ($q->all() as $row) {
      printf(
        "%-24s %12d %12d %14d %14d  %s\n",
        $row['name'],
        $row['packets_sent'],
        $row['packets_received'],
        $row['bytes_sent'],
        $row['bytes_received'],
        $row['last_seen']
      );
    }
    return ExitCode::OK;
  }

  private function ingest(string $data)
  {
    $this->stats['datagrams']++;
    $records = NetflowV5::decode($data);
    if ($records === false) {
      $this->stats['invalid']++;
      return;
    }
    foreach ($records as $rec) {
      $this->stats['records']++;
      $key = $rec['src'] . ':' . $rec['dst'];
      if (!isset($this->pending[$key]))
        $this->pending[$key] = [0, 0];
      $this->pending[$key][0] += $rec['packets'];
      $this->pending[$key][1] += $rec['bytes'];
    }
  }

  /**
   * Resolve pending flows to player/target pairs and add them to the database.
   * On database errors the pending counters are kept for the next attempt.
   */
  private function flush($verbose = false)
  {
    if ($this->pending !== []) {
      try {
        $this->store();
      } catch (\Throwable $e) {
        echo date('Y-m-d H:i:s '), "traffic/collect: flush failed, will retry: ", $e->getMessage(), "\n";
        Yii::$app->db->close();
        return;
      }
    }
    if ($verbose)
      printf(
        "%s datagrams=%d invalid=%d records=%d stored=%d unmatched=%d\n",
        date('Y-m-d H:i:s'),
        $this->stats['datagrams'],
        $this->stats['invalid'],
        $this->stats['records'],
        $this->stats['stored'],
        $this->stats['unmatched']
      );
  }

  private function store()
  {
    $db = Yii::$app->db;
    $targets = $this->targetMap();
    $players = $this->playerMap();

    // rows["player:target"] = [packets_sent, packets_received, bytes_sent, bytes_received]
    $rows = [];
    foreach ($this->pending as $key => [$packets, $bytes]) {
      [$src, $dst] = array_map('intval', explode(':', $key));
      if (isset($targets[$dst]) && ($pid = $this->resolvePlayer($src, $players)) !== null) {
        $k = $pid . ':' . $targets[$dst];
        $rows[$k] = $rows[$k] ?? [0, 0, 0, 0];
        $rows[$k][0] += $packets;
        $rows[$k][2] += $bytes;
      } elseif (isset($targets[$src]) && ($pid = $this->resolvePlayer($dst, $players)) !== null) {
        $k = $pid . ':' . $targets[$src];
        $rows[$k] = $rows[$k] ?? [0, 0, 0, 0];
        $rows[$k][1] += $packets;
        $rows[$k][3] += $bytes;
      } else {
        $this->stats['unmatched']++;
      }
    }

    if ($rows !== []) {
      $values = [];
      foreach ($rows as $k => $v) {
        [$pid, $tid] = explode(':', $k);
        $values[] = sprintf('(%d,%d,%d,%d,%d,%d,NOW(),NOW())', $pid, $tid, $v[0], $v[1], $v[2], $v[3]);
      }
      foreach (array_chunk($values, 500) as $chunk) {
        $db->createCommand(
          'INSERT INTO player_target_traffic (player_id,target_id,packets_sent,packets_received,bytes_sent,bytes_received,first_seen,last_seen) VALUES '
            . implode(',', $chunk)
            . ' ON DUPLICATE KEY UPDATE packets_sent=packets_sent+VALUES(packets_sent), packets_received=packets_received+VALUES(packets_received),'
            . ' bytes_sent=bytes_sent+VALUES(bytes_sent), bytes_received=bytes_received+VALUES(bytes_received), last_seen=VALUES(last_seen)'
        )->execute();
      }
      $this->stats['stored'] += count($rows);
    }
    $this->pending = [];
  }

  /**
   * @return array ip(int) => target_id. Instance and private network
   * addresses are added last so they win over the shared target address.
   */
  private function targetMap()
  {
    $map = [];
    $sources = [
      'SELECT ip, id AS target_id FROM target WHERE ip IS NOT NULL AND ip>0',
      'SELECT ip, target_id FROM private_network_target WHERE ip IS NOT NULL AND ip>0',
      'SELECT ip, target_id FROM target_instance WHERE ip IS NOT NULL AND ip>0',
    ];
    foreach ($sources as $sql)
      foreach (Yii::$app->db->createCommand($sql)->queryAll() as $row)
        $map[intval($row['ip'])] = intval($row['target_id']);
    return $map;
  }

  /**
   * @return array vpn_local_address(int) => player_id for connected players
   */
  private function playerMap()
  {
    $map = [];
    $rows = Yii::$app->db->createCommand('SELECT id, vpn_local_address FROM player_last WHERE vpn_local_address IS NOT NULL AND vpn_local_address>0')->queryAll();
    foreach ($rows as $row)
      $map[intval($row['vpn_local_address'])] = intval($row['id']);
    return $map;
  }

  /**
   * Resolve a VPN address to a player. Players that disconnected before the
   * flush are looked up on their most recent VPN session with that address.
   */
  private function resolvePlayer(int $ip, array &$players)
  {
    if (array_key_exists($ip, $players))
      return $players[$ip];

    $pid = Yii::$app->db->createCommand(
      'SELECT player_id FROM player_vpn_history WHERE vpn_local_address=:ip ORDER BY ts DESC, id DESC LIMIT 1',
      [':ip' => $ip]
    )->queryScalar();
    // cache misses too, so non VPN addresses are only looked up once per flush
    $players[$ip] = $pid === false || $pid === null ? null : intval($pid);
    return $players[$ip];
  }
}
