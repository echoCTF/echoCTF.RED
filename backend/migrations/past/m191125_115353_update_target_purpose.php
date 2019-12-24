<?php

use yii\db\Migration;

/**
 * Class m191125_115353_update_target_purpose
 */
class m191125_115353_update_target_purpose extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $purposes=[
        '2'=>'A small Application Web server usually found on embedded systems',
        '3'=>'Krusty will drive you crazy. It is so straight forward and yet, if you don\'t pay attention to detail your frustration will rise',
        '4'=>'Mr. Burns the devius nuclear plant owner, if you know how to speak and understand his language you\'ll do wonders',
        '5'=>'Like the real Smithers this server will serve you well only if you manage to enter it\'s memcached store.',
        '6'=>'Maggie is young but smart. However for all its smarts it still fails to use the proper functions to pass information back end forth',
        '7'=>'Barney may seem unsofisticated but always seem to come back as quite smart on rare occasions...',
        '8'=>'Martin is an academically brilliant teacher\'s pet. However, for all his brilliance, the simplest tricks are the ones that get him',
        '9'=>'Moleman sees the world differently than the rest, so must you.',
        '10'=>'Skinner aims to be anoying, simple, hip and oldschool at the same time. His exterior may seem easy to penetrate but looks can be deceiving.',
        '11'=>'This target serves as a tutorial for new users about the platform.',
        '12'=>'A direct implementation of the CVE-2019-14813 for ghostscript 9.26a',
        '13'=>'Flanders simple and kind, always ready to to give a helping hand.',
        '14'=>'Gogo Yubari the web scrapper, named after the famous assassin',
        '15'=>'Barbrady, the central authority of echoCiTy-F.',
        '16'=>'Milhouse, a _side_ character included into the infrastructure',
        '17'=>'Terrance talks a lot, through a _simple_ and jibberish sound like language, that is old - but not outdated...',
        '18'=>'A direct implementation of the CVE-2018-11776 for Apache Struts 2.3.34/2.5.16',
        '19'=>'Chef is generally more level-headed than the other adult residents of the town, and quite sympathetic to the children that speak to him.',
        '20'=>'Dolph one of the members of Nelson\'s gang, is always trying to play smart, but always makes mistakes, see if you can discover what these are',
        '21'=>'Welcome to the South Park PHP XXE Elementary School. Your task is to go through all the grades, from 1st to 5th Grade.',
        '22'=>'Harrison is the South Park\'s main Police Force officer.',
      ];
      for($i=2;$i<23;$i++)
      {
        //printf("Updating id: %d",$i);
        $this->update('target', ['purpose'=>$purposes["$i"]],["id"=>$i]);
      }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

    }
}
