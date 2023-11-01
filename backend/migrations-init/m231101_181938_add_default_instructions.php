<?php

use yii\db\Migration;

/**
 * Class m231101_181938_add_default_instructions
 */
class m231101_181938_add_default_instructions extends Migration
{
    public $entries=[
        ['title'=>'Connecting to the infrastructure','player_type'=>'offense', 'message'=>"<p>In order to connect to the infrastructure and be able to access the targets and gain points you need to connect to our VPN.</p>\r\n<p>\r\n      <ul>\r\n                <li>Download and Install <a href=\"https://openvpn.net/community-downloads/\" alt=\"OpenVPN Community Downloads\" target=\"_blank\">OpenVPN</a>\r\n          <li>Visit your <a href=\"/profile/me\" title=\"Profile\">Profile</a>\r\n                <li>Download your OpenVPN connection pack and take not of the download location of the file (to be used at the next step)\r\n                <li>Connect and start hacking <b><code>sudo openvpn ~/Downloads/echoCTF.ovpn</code></b>,</ul> <small>NOTE: Replace <code>~/Downloads/echoCTF.ovpn</code> with the path to the file you downloaded on the previous step</small>\r\n    </ul>\r\n</p>",'weight'=>0],
        ['title'=>'Gameplay','player_type'=>'offense', 'message'=>"<p>Υou earn points when you discover and claim <b>ETSCTF</b> flags. These flags can be found anywhere on the target system; in the form of files, variable names, database names etc. These are the most common  you can find on the targets:\r\n<ul>\r\n<li><code>root</code>: Flag under <code>/root</code>\r\n<li><code>env</code>: Environment variable flags \r\n<li><code>system</code>: Flags on system file (eg. <code>/etc/shadow, /etc/passwd</code>)\r\n<li><code>app</code>: Application specific flags (eg. mysql database name flags, memcache keys etc)\r\n<li><code>other</code>: For any flags that do not fit into the above categories.\r\n</ul>\r\n\r\nYou need to discover and claim all the flags from each system.</p>\r\n\r\n<p>Besides flags, you can also gain points from <code>findings</code>, which represent remotely accessible services on the target system. Discovering the open ports of a system will award you points as well as provide you with some extra hints.</p>\r\n\r\n<p>As you progress, new <b>Hints</b> will be made available for your consideration. Check your progress by visiting the page for target you currently working on, as it provides you with a list of the tasks you have completed and the ones still left to do. Any hints associated with the target will be displayed underneath the target description of each of the target pages.</p>\r\n\r\n<p>Keep an eye at your <b>notifications</b> on top, as they may contain important information like target additions, spins (resets), removals etc.</p>",'weight'=>1],
        ['title'=>'Help','player_type'=>'offense', 'message'=>"Don't be afraid to ask for help through our support server.",'weight'=>9],
        ['title'=>'Targets','player_type'=>'offense', 'message'=>"<p>The list of available targets is available at the <b><a href=\"/targets\" title=\"Targets\">Targets</a></b> menu. For each of the targets, you will be able to find the following details:\r\n<ul>\r\n<li>the name and IP of the target\r\n<li>the difficulty of the target\r\n<li>the number of flags and services\r\n<li>if the system is <abbr title=\"Systems that have a known way to gain root\">rootable</abbr> or not\r\n<li>restart request and detailed view actions for each target\r\n</ul>\r\n\r\nSome targets may require power up first, make sure to visit the target page for instructions on how to start them up.\r\n</p>\r\n<p><b>NOTE:</b> Please note that the targets are not allowed to connect to the internet. They can however connect to the IPs assigned to you by the VPN. Take special care when connecting to our VPN, ensure that you only allow connections by the targets you choose.\r\n</p>",'weight'=>1],
        ['title'=>'Have fun!!!','player_type'=>'offense', 'message'=>"This is not an instruction, <b>this is a rule!!</b>",'weight'=>100],
    ];


    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        foreach($this->entries as $entry)
            $this->upsert('instruction',$entry);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231101_181938_add_default_instructions cannot be reverted.\n";
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231101_181938_add_default_instructions cannot be reverted.\n";

        return false;
    }
    */
}
