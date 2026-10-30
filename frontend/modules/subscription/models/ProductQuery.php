<?php

namespace app\modules\subscription\models;

/**
 * This is the ActiveQuery class for [[Product]].
 *
 * @see Product
 */
class ProductQuery extends \yii\db\ActiveQuery
{
  public function active()
  {
    return $this->andWhere('[[active]]=1');
  }

  public function hasPrice()
  {
    return $this->andWhere('id in (SELECT DISTINCT product_id FROM price WHERE active=1)');
  }

  public function purchasable()
  {
    return $this->active()->hasPrice();
  }

  /**
   * Purchasable products plus contact_only products, which have no price
   * and show a contact button instead.
   */
  public function listed()
  {
    return $this->active()->andWhere(['or',
      'id in (SELECT DISTINCT product_id FROM price WHERE active=1)',
      new \yii\db\Expression("JSON_UNQUOTE(JSON_EXTRACT(metadata, '$.contact_only'))='1'"),
    ]);
  }

  public function onetime()
  {
    return $this->andWhere([
      '=',
      new \yii\db\Expression("JSON_UNQUOTE(JSON_EXTRACT(metadata, '$.standalone_perk'))"),
      '1'
    ]);
  }

  public function notOwned()
  {
    return $this->andWhere([
      'not exists',
      (new \yii\db\Query())
        ->from('player_product pp')
        ->where('pp.product_id = product.id')
        ->andWhere(['pp.player_id' => \Yii::$app->user->id])
    ]);
  }
  public function recurring()
  {
    return $this->andWhere(
      new \yii\db\Expression("JSON_UNQUOTE(JSON_EXTRACT(metadata, '$.standalone_perk')) IS NULL"),
    );
  }

  public function ordered()
  {
    return $this->orderBy(['weight' => SORT_ASC, 'name' => SORT_ASC]);
  }
  /**
   * {@inheritdoc}
   * @return Product[]|array
   */
  public function all($db = null)
  {
    return parent::all($db);
  }

  /**
   * {@inheritdoc}
   * @return Product|array|null
   */
  public function one($db = null)
  {
    return parent::one($db);
  }
}
