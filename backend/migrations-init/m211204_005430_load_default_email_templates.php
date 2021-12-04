<?php

use yii\db\Migration;

/**
 * Class m211204_005430_load_default_email_templates
 */
class m211204_005430_load_default_email_templates extends Migration
{
  public $TPL=[
    'emailChangeVerify',
    'emailVerify',
    'passwordResetToken'
  ];
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $dirn=\Yii::getAlias("@app");
      foreach($this->TPL as $base)
      {
        $txt=file_get_contents("$dirn/../frontend/mail/".$base."-text.php");
        $html=file_get_contents("$dirn/../frontend/mail/".$base."-html.php");
        $this->insert('email_template',['name'=>$base,'title'=>$base,'txt'=>$txt,'html'=>$html]);
      }

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      foreach($this->TPL as $base)
      {
        $this->delete('email_template',['name'=>$base]);
      }

    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211204_005430_load_default_email_templates cannot be reverted.\n";

        return false;
    }
    */
}
