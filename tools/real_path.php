<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>realpath() cache size tuner</title>
</head>
<body>
    <?php
    function iniSizeToBytes($value)
    {
        $value = trim($value);
        $last = strtolower(substr($value, -1));
        if (in_array($last, ['g', 'm', 'k'], true)) {
            $value = substr($value, 0, strlen($value) - 1);
            
            switch ($last) {
                case 'g':
                    $value *= 1024;
                case 'm':
                    $value *= 1024;
                case 'k':
                    $value *= 1024;
            }
        }
        return $value;
    }

    $current = realpath_cache_size();
    $max = iniSizeToBytes(ini_get('realpath_cache_size'));
    $ttl = ini_get('realpath_cache_ttl');
    $percentUsed = $current * 100 / $max;
    ?>

    <h1>realpath() cache size tuner</h1>

    <ol>
        <li>Read <a href="https://jpauli.github.io/2014/06/30/realpath-cache.html">Julien Pauli article</a>.</li>
        <li>Warm up cache (visit all your project pages).</li>
        <li>Run this script.</li>
        <li>Adjust values in php.ini.</li>
    </ol>

    <h2>Cache size</h2>

    <p><?= $current ?> of <?= $max ?> bytes used. It's <?= (int) $percentUsed ?>% of available cache.</p>

    <?php if ($percentUsed > 90): ?>
        <p>Consider increasing <code>realpath_cache_size</code> in your php.ini.</p>
    <?php elseif ($percentUsed < 50): ?>
        <p>Consider decreasing <code>realpath_cache_size</code> in your php.ini.</p>
    <?php else: ?>
        <p>Everything seems to be OK.</p>
    <?php endif ?>


    <h2>Cache TTL</h2>

    <p>TTL is <?= $ttl ?> seconds.</p>
</body>
</html>
