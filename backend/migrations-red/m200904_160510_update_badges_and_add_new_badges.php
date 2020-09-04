<?php

use yii\db\Migration;

/**
 * Class m200904_160510_update_badges_and_add_new_badges
 */
class m200904_160510_update_badges_and_add_new_badges extends Migration
{
  public $update_badges=[
    [
      'id'=>1,
      'name'=>'OG Player!',
      'pubname'=>'<i class="fab fa-old-republic text-primary" style="text-shadow: 1px 1px 2px lightgray;font-weight: 100;font-size: 6vw;"></i>',
      'description'=>"This user is with us since day one, words cannot express our gratitude for your support.\n\nThank you for being with us since the start.",
      'pubdescription'=>"This user is with us since day one and this badge is a <b>Thank You!</b> for that.",
    ],
    [
      'id'=>2,
      'pubname'=>'<i class="fas fa-bug text-primary" style="text-shadow: 1px 1px 2px lightgray;font-size: 6vw"></i>',
      'description'=>'Reported a bug on the echoCTF RED platform or helped us to trace and fix one.',
      'pubdescription'=>'Reported a bug on the echoCTF RED platform or helped us to trace and fix one.',
    ],
    [
      'id'=>3,
      'name'=>'Creator of LFI Tutorial &amp; dolph!',
      'pubname'=>'<i class="fab fa-galactic-senate text-warning" style="text-shadow: 1px 1px 2px lightgray;font-weight: 100;font-size: 6vw;"></i>',
      'description'=>"This is the badge for developing the target Dolph and the challenge LFI Tutorial.",
      'pubdescription'=>"This user is the developer of Dolph and LFI Tutorial.",
    ],
    [
      'id'=>4,
      'name'=>'Patreon Supporter!',
      'pubname'=>'<i class="fab fa-patreon text-danger" style="text-shadow: 1px 1px 2px lightgray;font-weight: 100;font-size: 6vw;"></i>',
      'description'=>"Supported us through Patreon with a pledge.",
      'pubdescription'=>"Supported us through Patreon with a pledge",
    ],
    [
      'id'=>5,
      'name'=>'Challenge Tester!',
      'pubname'=>'<i class="fas fa-cubes" style="text-shadow: 1px 1px 2px lightgray;color: #29b6f6;font-size: 6vw;"></i>',
      'description'=>"This is your badge for being an awesome tester of echoCTF.RED challenges.",
      'pubdescription'=>"This user is so awesome that is testing echoCTF.RED challenges before they get released to make sure they work properly.",
    ],
    [
      'id'=>6,
      'name'=>'Staff Member!',
      'pubname'=>'<i class="fas fa-users-cog"  style="color: #29b6f6;text-shadow: 1px 1px 2px lightgray;font-size: 6vw"></i>',
      'description'=>"A member of our team for providing help and development of challenges.",
      'pubdescription'=>"A member of our team for providing help and development of challenges.",
    ],
  ];
  public $badges=[
    [
      'name'=>'Challenge Developer!',
      'pubname'=>'<i class="far fa-file-code" style="color: #29b6f6;text-shadow: 1px 1px 2px lightgray;font-size: 6vw"></i>',
      'description'=>'Thank you for contributing challenges to echoCTF.RED <i class="fas fa-heart text-danger"></i>',
      'pubdescription'=>'This user has contributed challenges to echoCTF.RED <i class="fas fa-heart text-danger"></i>',
      'points'=>0,
    ],
    [
      'name'=>'Target Developer!',
      'pubname'=>'<i class="fas fa-server" style="color: #29b6f6;text-shadow: 1px 1px 2px lightgray;font-size: 6vw"></i>',
      'description'=>"Thank you for contributing targets for echoCTF.RED.",
      'pubdescription'=>"This badge is given to users who have contributing targets for echoCTF.RED.",
      'points'=>0,
    ]
  ];
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      foreach($this->update_badges as $badge)
        $this->update("badge", $badge, ['id'=>$badge['id']]);
      foreach($this->badges as $badge)
        $this->insert("badge", $badge);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200904_160510_update_badges_and_add_new_badges cannot be reverted.\n";

        return false;
    }
    */
}
