<?php

use yii\db\Migration;

/**
 * Class m200102_165040_update_lfi_challenge_questions
 */
class m200102_165040_update_lfi_challenge_questions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->update('question',['description'=>
      '<p>Always include sensitive files in your tests, such as
      <code>config.php, db.php, /admin/, <small>/admin/ETSCTF.html</small></code>.</p>
      <p>Enter the flag hidden in the <b><code>config.php</code></b> to complete this step  (you need to find this flag by yourself).</p>
      <p><small>There are two more flags, one of them should be easy to find (if you followed the echoCTF.RED Tutorial 101), the other is under <code>/admin/ETSCTF.html</code>, but this flag is not accessible by a web request. See if you can read it, based on what you learned so far, to get your headshot for the target.</small></p>'],['id'=>11]);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $this->update('question',['description'=>'<p>Always include sensitive files in your tests, such as <b><code>config.php</code></b>, <b><code>db.php</code></b> etc. Enter the flag hidden in the <b><code>config.php</code></b> to complete this step  (you need to find this flag also by yourself).</p> <p><small>There are two more ETSCTF flag files hidden in the application URLs. See if you can find them to get your headshot</small></p>'],['id'=>11]);
    }

}
