<?php

use yii\db\Migration;

/**
 * Class m200102_151952_add_faq_entry_for_ranks
 */
class m200102_151952_update_faq_entries extends Migration
{
  public $rec=[
      [
        'id'=>5,
        'title'=>'How does leaderboard resolves ties in scores?',
        'body'=>'<p>The leaderboard resolves ties (players with same score) in the following way:
        <ul>
        <li>user with higher points (<code>points DESC</code>)
        <li>older timestamp of user points last update (<code>updated_at ASC</code>)
        <li>older user (<code>user_id ASC</code>)</ul></p>',
        'weight'=>60
      ],
      [
        'id'=>6,
        'title'=>'Is brute-forcing allowed?',
        'body'=>"<p>Lightweight Brute-forcing is allowed and should be more
        than enough for any case. <b>You should be able to crack or guess
        passwords by just using the John standard list (password.lst)</b>.
        If you can't, then it means that the password is not meant to be
        guessed/cracked unless it is the same as the username.</p>",
        'weight'=>40
      ],
      [
        'id'=>7,
        'title'=>"Where can I get some hints?",
        'body'=>"<p>Here are some hints on how to get hints!</p>
        <ul>
        <li>Always check the <b>description of the target</b> you want to mess with.</li>
        <li>Check your hints regularly on the top-right corner.</li>
        <li>Don't be afraid to ask for help through our
            <b><a href=\"https://discord.gg/gQuAdzz\" title=\"echoCTF Discord Server\" target=\"_blank\">discord server</a></b>,
            but take the conversation to private messages so that you don't disclose information to other users.</li>
        </ul>",
        'weight'=>50
      ]
    ];
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      // fix weights
      $this->update('faq',['weight'=>10],['id'=>1]);
      $this->update('faq',['weight'=>20],['id'=>2]);
      $this->update('faq',['weight'=>30],['id'=>3]);
      $this->update('faq',['weight'=>100],['id'=>4]);

      // update existing ones
      $this->update('faq',['body'=>'<p>Every user is allowed 4 restart requests per day. User requests are added to a queue. The system processes the queue every 2 minutes.</p>'],['id'=>2]);
      $this->update('faq',['title'=>'How can I run my own echoCTF?'],['id'=>4]);
      // add new records
      foreach($this->rec as $rec)
        $this->insert('faq',$rec);



    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      foreach($this->rec as $rec)
        $this->delete('faq',['id'=>$rec['id']]);
    }

}
