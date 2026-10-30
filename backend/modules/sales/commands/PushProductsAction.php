<?php

namespace app\modules\sales\commands;

use yii\base\Action;
use yii\console\ExitCode;
use app\modules\sales\models\Product;

/**
 * Push a product catalog (JSON file) to Stripe and re-import it locally.
 *
 * Products are matched by their Stripe id (set by us on create, so the local
 * product.id stays predictable). Prices are matched by lookup_key. Stripe does
 * not allow changing the amount, currency or interval of a price, so when one
 * of these changes a new price is created, the lookup_key is moved to it and
 * the old price is archived. Existing subscriptions keep their old price.
 *
 * Usage:
 *   ./backend/yii sales/push-products catalog.json        apply changes
 *   ./backend/yii sales/push-products catalog.json 1      dry run, print only
 *   ./backend/yii sales/push-products catalog.json 0 1    apply and archive prices missing from the catalog
 */
class PushProductsAction extends Action
{
  private $stripe;
  private $dryRun = false;

  public function run($file, $dryRun = 0, $prune = 0)
  {
    $this->dryRun = (bool) intval($dryRun);
    $catalog = json_decode(file_get_contents($file), true, 512, JSON_THROW_ON_ERROR);
    \Stripe\Stripe::setEnableTelemetry(false);
    $this->stripe = new \Stripe\StripeClient(\Yii::$app->sys->stripe_apiKey);

    foreach ($catalog['products'] as $p) {
      $this->pushProduct($p);
      $keep = [];
      foreach ($p['prices'] ?? [] as $price) {
        $keep[] = $this->pushPrice($p['id'], $price);
      }
      if (intval($prune)) {
        $this->pruneOtherPrices($p['id'], array_filter($keep));
      }
    }

    if (!$this->dryRun) {
      echo "Re-importing products from Stripe\n";
      Product::FetchStripe();
    }
    return ExitCode::OK;
  }

  private function pushProduct(array $p)
  {
    $metadata = self::stringify($p['metadata'] ?? []);
    $fields = array_filter([
      'name' => $p['name'],
      'description' => $p['description'] ?? null,
      'active' => $p['active'] ?? true,
    ], fn($v) => $v !== null && $v !== '');

    try {
      $existing = $this->stripe->products->retrieve($p['id']);
    } catch (\Stripe\Exception\InvalidRequestException $e) {
      $existing = null;
    }

    if ($existing === null) {
      printf("[product] create %s (%s)\n", $p['id'], $p['name']);
      if (!$this->dryRun) {
        $this->stripe->products->create(['id' => $p['id'], 'metadata' => $metadata] + $fields);
      }
      return;
    }

    // Blank out metadata keys that were removed from the catalog
    foreach ($existing->metadata->toArray() as $key => $value) {
      if (!array_key_exists($key, $metadata)) {
        $metadata[$key] = '';
      }
    }
    printf("[product] update %s (%s)\n", $p['id'], $p['name']);
    if (!$this->dryRun) {
      $this->stripe->products->update($p['id'], ['metadata' => $metadata] + $fields);
    }
  }

  /**
   * @return string|null the id of the price that now holds the lookup_key
   */
  private function pushPrice(string $productId, array $price)
  {
    $key = $price['lookup_key'];
    $wanted = [
      'unit_amount' => intval($price['unit_amount']),
      'currency' => strtolower($price['currency']),
      'recurring' => isset($price['recurring']) ? [
        'interval' => $price['recurring']['interval'],
        'interval_count' => intval($price['recurring']['interval_count'] ?? 1),
      ] : null,
    ];
    $editable = array_filter([
      'nickname' => $price['nickname'] ?? null,
      'metadata' => self::stringify($price['metadata'] ?? []),
    ], fn($v) => $v !== null);

    $found = $this->stripe->prices->all(['lookup_keys' => [$key], 'limit' => 1])->data;
    $current = $found[0] ?? null;

    if ($current !== null && $current->product === $productId && $current->active && self::samePrice($current, $wanted)) {
      // Blank out metadata keys that were removed from the catalog, Stripe
      // keeps keys that are left out of an update
      $removed = array_diff(array_keys($current->metadata->toArray()), array_keys($editable['metadata']));
      foreach ($removed as $metaKey) {
        $editable['metadata'][$metaKey] = '';
      }
      printf("  [price] update %s (%s)%s\n", $current->id, $key, $removed !== [] ? ' removing metadata: ' . implode(', ', $removed) : '');
      if (!$this->dryRun) {
        $this->stripe->prices->update($current->id, $editable);
      }
      return $current->id;
    }

    printf("  [price] create %s %d %s%s\n", $key, $wanted['unit_amount'], $wanted['currency'], $wanted['recurring'] ? '/' . $wanted['recurring']['interval'] : ' one-time');
    if ($this->dryRun) {
      return $current->id ?? null;
    }
    $params = array_filter([
      'product' => $productId,
      'lookup_key' => $key,
      'transfer_lookup_key' => true,
    ] + $wanted + $editable, fn($v) => $v !== null);
    $created = $this->stripe->prices->create($params, [
      'idempotency_key' => 'price-' . sha1($productId . json_encode($wanted) . $key),
    ]);

    if ($current !== null) {
      printf("  [price] archive %s (replaced)\n", $current->id);
      $this->stripe->prices->update($current->id, ['active' => false]);
    }
    return $created->id;
  }

  private function pruneOtherPrices(string $productId, array $keep)
  {
    $prices = $this->stripe->prices->all(['product' => $productId, 'active' => true, 'limit' => 100]);
    foreach ($prices->autoPagingIterator() as $p) {
      if (!in_array($p->id, $keep, true)) {
        printf("  [price] archive %s (not in catalog)\n", $p->id);
        if (!$this->dryRun) {
          $this->stripe->prices->update($p->id, ['active' => false]);
        }
      }
    }
  }

  private static function samePrice($stripePrice, array $wanted): bool
  {
    if (intval($stripePrice->unit_amount) !== $wanted['unit_amount'] || $stripePrice->currency !== $wanted['currency']) {
      return false;
    }
    $r = $stripePrice->recurring;
    if ($wanted['recurring'] === null) {
      return $r === null;
    }
    return $r !== null
      && $r->interval === $wanted['recurring']['interval']
      && intval($r->interval_count) === $wanted['recurring']['interval_count'];
  }

  /**
   * Stripe metadata values must be strings (max 500 chars, 50 keys).
   * Lists such as network_ids are joined with commas, which is what the
   * platform already expects.
   */
  private static function stringify(array $metadata): array
  {
    $out = [];
    foreach ($metadata as $k => $v) {
      if (is_array($v)) {
        $v = implode(',', $v);
      } elseif (is_bool($v)) {
        $v = $v ? '1' : '0';
      }
      $out[$k] = (string) $v;
    }
    return $out;
  }
}
