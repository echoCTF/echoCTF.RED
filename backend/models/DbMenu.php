<?php

namespace app\models;

use yii\db\ActiveRecord;

class DbMenu extends ActiveRecord
{
  public static function tableName()
  {
    return 'mui_menu';
  }

  public function rules()
  {
    return [
      [['label'], 'required'],
      [['url'], 'string', 'max' => 255],
      ['visibility', 'each', 'rule' => ['in', 'range' => ['all', 'guest', 'user', 'admin']]],
      [['parent_id', 'sort_order'], 'integer'],
      ['enabled', 'boolean'],
    ];
  }

  public function beforeSave($insert)
  {
    if (parent::beforeSave($insert)) {
      if (is_array($this->visibility)) {
        $this->visibility = implode(',', $this->visibility);
      }
      return true;
    }
    return false;
  }

  public function getParent()
  {
    return $this->hasOne(self::class, ['id' => 'parent_id']);
  }

  public function getChildren()
  {
    return $this->hasMany(self::class, ['parent_id' => 'id'])->orderBy(['sort_order' => SORT_ASC, 'label' => SORT_ASC]);
  }
}
