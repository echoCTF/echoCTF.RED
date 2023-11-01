<?php

use yii\db\Migration;

/**
 * Class m231101_181919_add_default_rules
 */
class m231101_181919_add_default_rules extends Migration
{
    public $entries=[
        ['title'=>'Respect the moderators and other participants','player_type'=>'offense','message'=>"Other participants and the moderators are not part of the competition targets so don't abuse or attack them. We take abuse reports very seriously and an offense like that can get you permanently banned from this as well as future competitions.",'weight'=>100],
        ['title'=>'DO NOT perform DoS attacks','player_type'=>'offense','message'=>"Don't perform denial of service attacks on the targets, services or players. if you need concurrency in your tools make sure you keep them into sane numbers. The systems will automatically block players who exceed the limits. If you get blocked contact our support for the block to be lifted.",'weight'=>200],
        ['title'=>'Team captains are responsible for their teams','player_type'=>'offense','message'=>"Team captains need to approve or reject members new members. It is the responsibility of the Team Captain to make sure their team members are the right ones.\r\n<p>In the team page provides an Invite URL that you can give your teammates to join your team.</p>",'weight'=>10],
        ['title'=>'Fair Play','player_type'=>'offense','message'=>"All participants are expected to maintain a spirit of fair play throughout the competition. Cheating, unauthorized collaboration, or any other form of unfair advantage is strictly prohibited",'weight'=>0],
        ['title'=>'Legal Compliance','player_type'=>'offense','message'=>"Participants must adhere to all local and national laws during the competition. Any illegal activities or actions that violate any laws will result in immediate disqualification.",'weight'=>0],
        ['title'=>'Reporting Issues','player_type'=>'offense','message'=>"If you encounter any technical issues or problems during the competition, please report them through the designated channels provided by the organizers. Do not attempt to exploit these issues for personal gain.",'weight'=>0],
        ['title'=>'Code of Conduct','player_type'=>'offense','message'=>"Participants must adhere to a code of conduct that promotes a positive and respectful environment for all. Harassment, discrimination or any form of harmful behavior will not be tolerated.",'weight'=>0],
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        foreach($this->entries as $entry)
            $this->upsert('rule',$entry);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231101_181919_add_default_rules cannot be reverted.\n";
    }

}
