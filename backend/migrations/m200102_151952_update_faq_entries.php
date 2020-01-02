<?php

use yii\db\Migration;

/**
 * Class m200102_151952_add_faq_entry_for_ranks
 */
class m200102_151952_update_faq_entries extends Migration
{
  public $rec=[
      'id'=>5,
      'title'=>'How does leaderboard resolves ties in scores?',
      'body'=>'<p>The leaderboard resolves ties (players with same score) in the following way:
      <ul>
      <li>user with higher points (<code>points DESC</code>)
      <li>older timestamp of user points last update (<code>updated_at ASC</code>)
      <li>older user (<code>user_id ASC</code>)</ul></p>',
      'weight'=>10
    ];
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

      $this->update('faq',['weight'=>5],['id'=>11]);
      $this->insert('faq',$this->rec);
      $this->update('faq',['body'=>'<p>Every user is allowed 4 restart requests per day. User requests are added to a queue. The system processes the queue every 2 minutes.</p>'],['id'=>2]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $this->delete('faq',['id'=>$this->rec['id']]);
    }

}
