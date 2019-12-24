<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%profile}}`.
 */
class m191024_055745_create_profile_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%profile}}', [
            'id' => $this->bigInteger()->unsigned(),
            'player_id' => $this->integer()->unsigned()->notNull()->unique(),
            'visibility' => "ENUM('public', 'private', 'ingame') DEFAULT 'private'",
            'bio' => $this->text(),
            'country' => $this->string(3)->defaultValue("UNK")->comment("Country code (eg GR)"),
            'avatar' => $this->string()->defaultValue("default.png")->comment("Profile avatar"),
            'discord' => $this->string()->defaultValue("")->comment("Discord handle (eg  @username#1234)"),
            'twitter' => $this->string()->defaultValue("")->comment("Twitter handle (eg @echoCTF)"),
            'github' => $this->string()->defaultValue("")->comment("Github handle (eg echoCTF)"),
            'htb' => $this->string()->defaultValue("")->comment("HTB Profile (eg 47396)"),
            'terms_and_conditions'=>$this->boolean()->defaultValue(false)->comment("Accepted Terms and Conditions?"),
            'mail_optin'=>$this->boolean()->defaultValue(false)->comment("Opt in for mail notifications?"),
            'gdpr'=>$this->boolean()->defaultValue(false)->comment("GDPR Acceptance?"),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
            'PRIMARY KEY (id)',
        ]);


        $this->createIndex(
            '{{%idx-profile-gdpr}}',
            '{{%profile}}',
            'gdpr'
        );
        $this->createIndex(
            '{{%idx-profile-mail_optin}}',
            '{{%profile}}',
            'mail_optin'
        );
        $this->createIndex(
            '{{%idx-profile-terms_and_conditions}}',
            '{{%profile}}',
            'terms_and_conditions'
        );
        $this->createIndex(
            '{{%idx-profile-allboolean}}',
            '{{%profile}}',
            [ 'gdpr','mail_optin','terms_and_conditions']
        );

        $this->createIndex(
            '{{%idx-profile-visibility}}',
            '{{%profile}}',
            'visibility'
        );
        $this->createIndex(
            '{{%idx-profile-country}}',
            '{{%profile}}',
            'country'
        );
        $this->createIndex(
            '{{%idx-profile-created_at}}',
            '{{%profile}}',
            'created_at'
        );
        $this->createIndex(
            '{{%idx-profile-updated_at}}',
            '{{%profile}}',
            'updated_at'
        );
        $this->createIndex(
            '{{%idx-profile-created-updated-at}}',
            '{{%profile}}',
            ['created_at','updated_at']
        );
        $this->createIndex(
            '{{%idx-profile-multiple}}',
            '{{%profile}}',
            [ 'visibility','country','id','player_id']
        );


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex(
            '{{%idx-profile-gdpr}}',
            '{{%profile}}'
        );
        $this->dropIndex(
            '{{%idx-profile-mail_optin}}',
            '{{%profile}}'
        );
        $this->dropIndex(
            '{{%idx-profile-terms_and_conditions}}',
            '{{%profile}}'
        );
        $this->dropIndex(
            '{{%idx-profile-allboolean}}',
            '{{%profile}}'
        );

        $this->dropIndex(
            '{{%idx-profile-visibility}}',
            '{{%profile}}'
        );
        $this->dropIndex(
            '{{%idx-profile-country}}',
            '{{%profile}}'
        );
        $this->dropIndex(
            '{{%idx-profile-created_at}}',
            '{{%profile}}'
        );
        $this->dropIndex(
            '{{%idx-profile-updated_at}}',
            '{{%profile}}'
        );
        $this->dropIndex(
            '{{%idx-profile-created-updated-at}}',
            '{{%profile}}'
        );
        $this->dropIndex(
            '{{%idx-profile-multiple}}',
            '{{%profile}}'
        );

        $this->dropTable('{{%profile}}');
    }
}
