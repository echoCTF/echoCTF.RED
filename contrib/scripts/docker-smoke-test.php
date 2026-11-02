<?php
// Smoke test for the pure parts of DockerContainer / Pved (no docker daemon needed)
$S = __DIR__;
$R = '/root/echoCTF.RED/backend';
require $S . '/yii2/framework/Yii.php';
spl_autoload_register(function ($c) use ($S, $R) {
  $map = [
    'Docker\\API\\' => $S . '/dapi/src/',
    'Docker\\' => $S . '/dphp/src/',
    'app\\' => $R . '/',
  ];
  foreach ($map as $p => $d) {
    if (strncmp($c, $p, strlen($p)) === 0) {
      $f = $d . str_replace('\\', '/', substr($c, strlen($p))) . '.php';
      if (is_file($f)) { require $f; return; }
    }
  }
});
new yii\console\Application(['id' => 't', 'basePath' => $R, 'params' => []]);

use app\modules\infrastructure\models\DockerContainer as DC;
use app\components\virtualization\Pved;

$fail = 0;
function ok($c, $m) { global $fail; echo ($c ? "PASS " : "FAIL ") . $m . "\n"; if (!$c) $fail++; }

// parseMemory
ok(DC::parseMemory('{"hostConfig":{"Memory":512}}') === 512 * 1024 * 1024, 'memory MB');
ok(DC::parseMemory('{"hostConfig":{"Memory":536870912}}') === 536870912, 'memory bytes');
ok(DC::parseMemory(null) === null && DC::parseMemory('{}') === null, 'memory empty');

// registryAuth
$a = DC::registryAuth('{"username":"u","password":"p+/?","junk":1}');
$dec = json_decode(base64_decode(strtr($a, '-_', '+/')), true);
ok($dec === ['username' => 'u', 'password' => 'p+/?'], 'registry auth base64url, filtered keys');
ok(strpbrk($a, '+/=') === false, 'registry auth url-safe');
ok(DC::registryAuth('') === null && DC::registryAuth('{"Authorization":"x"}') === null, 'registry auth none');

// demux
$frame = "\x01\0\0\0" . pack('N', 5) . "hello" . "\x02\0\0\0" . pack('N', 3) . "err";
ok(DC::demuxLogs($frame) === 'helloerr', 'demux');

// fromTarget, unknown config keys ignored, vars cloned
$var = new stdClass; $var->key = 'ETSCTF_FLAG'; $var->val = 'ETSCTF_abc';
$vol = new stdClass; $vol->volume = '/a'; $vol->bind = '/b';
$target = new stdClass;
foreach (['name' => 't1', 'fqdn' => 't1.example', 'image' => 'img:1', 'imageparams' => null,
  'parameters' => '{"hostConfig":{"Memory":256,"PidsLimit":100,"Bogus":1}}', 'dns' => null, 'net' => 'targets',
  'mac' => null, 'server' => 'tcp://127.0.0.1:2376'] as $k => $v) $target->$k = $v;
$target->targetVariables = [$var];
$target->targetVolumes = [$vol];
$dc = DC::fromTarget($target, ['name' => 't1_7', 'nonsense' => 1]);
ok($dc->name === 't1_7' && $dc->memory === 256 * 1024 * 1024, 'fromTarget overrides + memory');

// dynamic treasures
$dc->applyDynamicTreasures(['fs' => ['env' => [['src' => 'abc', 'dest' => 'XYZ']]]], 7, 3);
ok($dc->targetVariables[0]->val === 'ETSCTF_XYZ', 'env flag replaced');
ok($var->val === 'ETSCTF_abc', 'target variable not mutated');
ok($dc->labels['player_id'] === '7' && isset($dc->labels['treasures_0']), 'labels');
$dc2 = DC::fromTarget($target);
$dc2->applyDynamicTreasures(['fs' => []], 1, 1);
ok($dc2->targetVariables[0]->val === 'ETSCTF_abc', 'no env treasures no warning');

// hostConfig
$hc = $dc->hostConfig();
ok($hc->getMemory() === 256 * 1024 * 1024, 'hostConfig memory bytes wins over raw MB');
ok($hc->getPidsLimit() === 100, 'hostConfig passes parameters');
ok($hc->getDns() === null, 'no [null] dns');
ok($hc->getBinds() === ['/a:/b'] && $hc->getNetworkMode() === 'targets', 'binds + network');

// Pved
ok(Pved::handles('pve:https://h/') && !Pved::handles('tcp://h') && !Pved::handles(null), 'pve detection');
ok(Pved::getHeaders('{"Authorization":"PVEAPIToken=a"}')[2] === 'Authorization: PVEAPIToken=a', 'pve headers');
try { Pved::getHeaders("{\"X\":\"a\\r\\nEvil: 1\"}"); ok(false, 'pve header injection'); } catch (yii\base\UserException $e) { ok(true, 'pve header injection rejected'); }
ok(Pved::getHeaders(null) === ['Accept: application/json', 'Content-Type: application/json'], 'pve null params');

// client requires a server
try { DC::client(null); ok(false, 'client null'); } catch (yii\base\UserException $e) { ok(true, 'client null server rejected'); }

exit($fail ? 1 : 0);
