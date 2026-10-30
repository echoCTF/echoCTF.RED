#!/usr/bin/env php
<?php
/**
 * stripe-to-catalog.php
 *
 * @version 1.0.0
 *
 * Convert Stripe CLI JSON output into the catalog format used by
 * ./backend/yii sales/push-products (see docs/stripe-catalog.example.json).
 *
 * Get the input with the Stripe CLI:
 *   stripe get /v1/products -d limit=100 -d active=true > products.json
 *   stripe get /v1/prices -d limit=100 -d active=true > prices.json
 *
 * Then convert:
 *   php stripe-to-catalog.php products.json prices.json > catalog.json
 *   php stripe-to-catalog.php --version
 *
 * Warnings go to stderr, the catalog goes to stdout.
 */

const VERSION = '1.0.0';

if ($argc === 2 && in_array($argv[1], ['--version', '-v'], true)) {
  echo 'stripe-to-catalog ', VERSION, "\n";
  exit(0);
}

if ($argc < 3) {
  fwrite(STDERR, 'stripe-to-catalog ' . VERSION . "\n");
  fwrite(STDERR, "Usage: php {$argv[0]} products.json prices.json > catalog.json\n");
  exit(1);
}

function load_list(string $file): array
{
  $json = json_decode(file_get_contents($file), true, 512, JSON_THROW_ON_ERROR);
  if (!empty($json['has_more'])) {
    fwrite(STDERR, "WARNING: $file has more results than one page (has_more=true). Some items are missing.\n");
  }
  return $json['data'] ?? $json;
}

/**
 * Stripe stores metadata as strings. Turn integers back into numbers and
 * network_ids back into a list so the file matches the example catalog.
 * push-products converts them back to strings when pushing.
 */
function unstringify(array $metadata): array
{
  $out = [];
  foreach ($metadata as $k => $v) {
    if ($k === 'network_ids') {
      $out[$k] = array_values(array_map('intval', array_filter(array_map('trim', explode(',', $v)), 'strlen')));
    } elseif (preg_match('/^-?(0|[1-9][0-9]*)$/', $v)) {
      $out[$k] = intval($v);
    } else {
      $out[$k] = $v;
    }
  }
  return $out;
}

/**
 * Suggest a lookup_key such as vip_month_eur_1000 for prices that have none.
 */
function suggest_key(array $product, array $price): string
{
  $base = $product['metadata']['shortcode'] ?? $product['name'];
  $base = strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '_', $base), '_'));
  $period = 'once';
  if (!empty($price['recurring'])) {
    $count = intval($price['recurring']['interval_count']);
    $period = ($count > 1 ? $count : '') . $price['recurring']['interval'];
  }
  return sprintf('%s_%s_%s_%d', $base, $period, $price['currency'], $price['unit_amount']);
}

$products = load_list($argv[1]);
$pricesByProduct = [];
foreach (load_list($argv[2]) as $price) {
  $productId = is_array($price['product']) ? $price['product']['id'] : $price['product'];
  $pricesByProduct[$productId][] = $price;
}

$catalog = ['products' => []];
foreach ($products as $product) {
  $out = ['id' => $product['id'], 'name' => $product['name']];
  if (!empty($product['description'])) {
    $out['description'] = $product['description'];
  }
  if (empty($product['active'])) {
    $out['active'] = false;
  }
  $metadata = unstringify($product['metadata'] ?? []);
  if ($metadata !== []) {
    $out['metadata'] = $metadata;
  }

  $out['prices'] = [];
  foreach ($pricesByProduct[$product['id']] ?? [] as $price) {
    if (empty($price['active'])) {
      continue; // push-products would recreate archived prices
    }
    if ($price['unit_amount'] === null) {
      fwrite(STDERR, "SKIP: {$price['id']} on {$product['id']} is tiered, metered or pay-what-you-want, push-products can't handle it\n");
      continue;
    }

    $key = $price['lookup_key'] ?? null;
    if (empty($key)) {
      $key = suggest_key($product, $price);
    }

    $p = ['lookup_key' => $key];
    if (!empty($price['nickname'])) {
      $p['nickname'] = $price['nickname'];
    }
    $p['unit_amount'] = intval($price['unit_amount']);
    $p['currency'] = $price['currency'];
    if (!empty($price['recurring'])) {
      $p['recurring'] = [
        'interval' => $price['recurring']['interval'],
        'interval_count' => intval($price['recurring']['interval_count']),
      ];
    }
    $priceMeta = unstringify($price['metadata'] ?? []);
    if ($priceMeta !== []) {
      $p['metadata'] = $priceMeta;
    }
    $out['prices'][] = $p;
  }

  if ($out['prices'] === []) {
    fwrite(STDERR, "NOTE: {$product['id']} ({$product['name']}) has no usable active prices\n");
  }
  $catalog['products'][] = $out;
}

echo json_encode($catalog, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR), "\n";
