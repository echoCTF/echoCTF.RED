<?php

use yii\db\Migration;

/**
 * Class m231101_182034_add_default_faq
 */
class m231101_182034_add_default_faq extends Migration
{
    public $entries=[
        ['title'=>'How many restarts are allowed?','body'=>"<p>Every user is allowed 10 restart requests per day. User requests are added to a queue which is processed every minute, at which point the user who made the request will receive a notification of completion.</p>",'weight'=>42],
        ['title'=>'What are non rootable targets?','body'=>"<p>There are targets that have no pre-defined way, by us, to gain root access. These targets do have a flag under the <code>/root</code> folder, but depend on you discovering a 0day exploit to get it.</p>",'weight'=>30],
        ['title'=>'How does leaderboard resolve ties in scores?','body'=>"<p>The leaderboard determines the position of the players in the ranks in the following way:\r\n        <ul>\r\n        <li>user with higher points (<small><code class=\"text-warning\">points DESC</code></small>)</li>\r\n        <li>older timestamp of user points last update (<small><code class=\"text-warning\">updated_at DESC</code></small>)</li>\r\n        <li>older user (<small><code class=\"text-warning\">user_id ASC</code></small>)</li>\r\n</ul></p>",'weight'=>60],
        ['title'=>'Is brute-forcing allowed?','body'=>"<p>Lightweight Brute-forcing is allowed and should be more than enough for any case. <b class=\"text-warning\">You should be able to crack or guess passwords by using the standard John lists (eg password.lst, rockyou.txt)</b>.</p>\r\n<p>If you can't, then it means that the password is not meant to be guessed/cracked. If you are certain that a username/password combination should work join our support server and let us know.</p>",'weight'=>40],
        ['title'=>'How to restart a target?','body'=>"<p>Often times, during your attacks on a target, you may cause the target to become unresponsive or mis-behave. In such situations you can request for a target restart by going to the target page and clicking the restart icon <i class=\"fas fa-power-off text-primary\" style=\"font-size: 1.5em;\"></i>. This will put the target on a queue to be restarted. The queue is processed every minute. Once the system has been restarted, you will receive a notification informing you of the fact.</p>\r\n\r\n<p>NOTE: <i>Keep in mind that in order to request a target restart you need to either be connected to the VPN or have progress on the target</i></p>",'weight'=>41],
        ['title'=>'What are the target difficulty classifications?','body'=>"<p>The targets are classified into the following difficulty levels\r\n<ul>\r\n<li><i class=\"fas fa-battery-empty text-gray\" style=\"font-size: 1.35vw;\"></i> Beginner\r\n<li><i class=\"fas fa-battery-quarter red-success\" style=\"font-size: 1.35vw;\"></i> Basic\r\n<li><i class=\"fas fa-battery-half text-secondary\" style=\"font-size: 1.35vw;\"></i> Intermediate\r\n<li><i class=\"fas fa-battery-three-quarters text-warning\" style=\"font-size: 1.35vw;\"></i> Advanced\r\n<li><i class=\"fas fa-battery-full text-danger\" style=\"font-size: 1.35vw;\"></i> Expert\r\n</ul>\r\n</p>",'weight'=>80],
        ['title'=>'I think I found an unexpected way to gain access on a target where do I report it?','body'=>"<p>We generally do not develop our targets to try and limit your way to a specific path. Rather we try to verify that at least one way exists to solve the targets. If you think you have found a way outside of the expected feel free to submit a writeup with details of your method so others can also learn.</p>",'weight'=>55],
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        foreach($this->entries as $entry)
            $this->upsert('faq',$entry);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231101_182034_add_default_faq cannot be reverted.\n";
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231101_182034_add_default_faq cannot be reverted.\n";

        return false;
    }
    */
}
