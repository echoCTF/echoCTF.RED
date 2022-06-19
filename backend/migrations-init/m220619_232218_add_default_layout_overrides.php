<?php

use yii\db\Migration;

/**
 * Class m220619_232218_add_default_layout_overrides
 */
class m220619_232218_add_default_layout_overrides extends Migration
{
    public $events=[
        [
          'name' => 'echoCTF Birthday',
          'route' => '',
          'player_id' => '',
          'guest' => 0,
          'repeating' => 1,
          'css' => '.vscomplete::before { font-family: "Font Awesome 5 Free"; font-weight: 900; content: "\f1fd";  } .vsincomplete::before { font-family: "Font Awesome 5 Free"; font-weight: 900; content: "\f786";  }',
          'js' => '',
          'valid_from' => '2022-10-14 00:00:00',
          'valid_until' => '2022-10-21 23:59:59',
        ],
        [
          'name' => 'Valentines Day',
          'route' => '',
          'player_id' => '',
          'guest' => 0,
          'repeating' => 1,
          'css' => '.vscomplete::before { color: red; font-family: "Font Awesome 5 Free"; font-weight: 900; content: "\f004";  } .vsincomplete::before { color: red; font-family: "Font Awesome 5 Free"; font-weight: 900; content: "\f21e";  }',
          'js' => '',
          'valid_from' => '2022-02-14 00:00:00',
          'valid_until' => '2022-02-15 06:00:00',
        ],
      ];

      /**
       * {@inheritdoc}
       */
      public function safeUp()
      {
        foreach($this->events as $key => $rec)
          $this->insert('layout_override',$rec);
      }

      /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220619_232218_add_default_layout_overrides cannot be reverted.\n";

    }
}
