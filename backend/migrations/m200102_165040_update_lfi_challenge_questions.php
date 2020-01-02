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
      <b><code>config.php, db.php, /admin/, <small>/admin/ETSCTF.html</small></code></b>.</p>
      <p>Enter the flag hidden in the <b><code>config.php</code></b> to complete this step  (you need to find this flag by yourself).</p>
      <p><small>There is a flag under <code>/admin/ETSCTF.html</code>, but it is not accessible by a web request. See if you can read it to get your target headshot.</small></p>'],['id'=>11]);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $this->update('question',['description'=>'<p>Always include sensitive files in your tests, such as <b><code>config.php</code></b>, <b><code>db.php</code></b> etc. Enter the flag hidden in the <b><code>config.php</code></b> to complete this step  (you need to find this flag also by yourself).</p> <p><small>There are two more ETSCTF flag files hidden in the application URLs. See if you can find them to get your headshot</small></p>'],['id'=>11]);
    }

}
