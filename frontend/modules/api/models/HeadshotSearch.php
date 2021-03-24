<?php
namespace app\modules\api\models;

use yii\base\Model;

class HeadshotSearch extends Model
{
    public $target_id;
    public $profile_id;
    public $created_at;
    public $first;
    public $rating;
    public $timer;

    public function rules()
    {
        return [
            [['target_id','profile_id','rating','timer'], 'integer'],
            [['first'], 'boolean'],
            [['target_name'], 'string'],
            [['created_at'], 'datetime']
        ];
    }
}
