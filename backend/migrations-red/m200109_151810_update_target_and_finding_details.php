<?php

use yii\db\Migration;

/**
 * Class m200109_151810_update_target_details
 */
class m200109_151810_update_target_and_finding_details extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->db->createCommand("UPDATE treasure SET points=500 WHERE id=79")->execute();
      $this->db->createCommand("UPDATE stream SET points=500 WHERE model='treasure' and model_id=79")->execute();

      $this->db->createCommand("UPDATE treasure SET points=300 WHERE id=80")->execute();
      $this->db->createCommand("UPDATE stream SET points=300 WHERE model='treasure' and model_id=80")->execute();

      $this->db->createCommand("UPDATE treasure SET points=400 WHERE id=81")->execute();
      $this->db->createCommand("UPDATE stream SET points=400 WHERE model='treasure' and model_id=81")->execute();

      $this->db->createCommand("UPDATE treasure SET points=200 WHERE id=82")->execute();
      $this->db->createCommand("UPDATE stream SET points=200 WHERE model='treasure' and model_id=82")->execute();

      $DESCRIPTION=<<<ENDHTML
      <p class="lead">This is a target with direct implementation of the CVE-2019-1010174 for the CImg Library v.2.3.3 and is here to assist in developing exploits for this vulnerability.</p> <details> <summary>Description</summary> <p>CImg The CImg Library v.2.3.3 and earlier is affected by a command injection vulnerability. This attack can lead to RCE. The vulnerable code can be found in the <code>load_network()</code> function. Loading an image from a user-controllable url can lead to command injection, because no string sanitization is done on the url.</p> </details> <details> <summary>Environment details</summary> <p>The system is accessible at <b><code>10.0.160.248</code></b> and runs a web server and a vulnerable binary utilizing CImg.</p> <p>Flags can be obtained by either accessing directly the service <code>375/tcp</code> or through the web interface at <a href="http://10.0.160.248/">http://10.0.160.248</a>. Flags can be found at the usual places:
        <ul>
        <li><b><code>/root/ETSCTF</code></b>
        <li><b><code>/etc/passwd</code></b> gecos
        <li><b><code>/etc/shadow</code></b> password hash
        <li><b><code>env</code></b> variable
        </ul>
        The source for the service listening on <code>375/tcp</code> is the following
<pre>
// https://github.com/github/security-lab/tree/master/SecurityExploits/CImg
#undef cimg_display
#define cimg_display 0
#include "CImg.h"
using namespace cimg_library;
#include <iostream>
#include <string>

// To compile and run:
//
// g++ -I./CImg poc.c -o poc
// ./poc
//
// Notice that the file ~/CImg-RCE has now been created.

int main(int argc, char **argv) {
  CImg<> img;
  std::cout << "Provide image url: " << std::endl;
  for (std::string line; std::getline(std::cin, line);) {
        std::cout << line << std::endl;
        img.assign(line.c_str());
  }
  return 0;
}
<pre>
        </p>
      </details> <details> <summary>References</summary> <ul> <li><a target="_blank" href="https://cve.mitre.org/cgi-bin/cvename.cgi?name=CVE-2019-1010174">CVE-2019-1010174</a> <li><a target="_blank" href="https://framagit.org/dtschump/CImg/commit/5ce7a426b77f814973e56182a0e76a2b04904146">Fix commit</a> </ul></details>
ENDHTML;
    $this->update('target',['image'=>'registry.echoctf.com:5000/red/lfi-tutorial:v0.9'],['id'=>26]);
    $this->update('target',['image'=>'registry.echoctf.com:5000/red/cve-2019-1010174:v0.9'],['id'=>23]);
    $this->update('target',['description'=>$DESCRIPTION],['id'=>23]);
    $this->db->createCommand("insert into player_score (player_id,points) select player_id,sum(points) from stream group by player_id ON DUPLICATE KEY UPDATE points=values(points)")->execute();
    $this->db->createCommand("CALL calculate_ranks()")->execute();

    $this->update('target',['description'=>'<p>Chef is generally more level-headed than the other adult residents of the
town, and quite sympathetic to the children that speak to him. His advice is
always to the point, with focus on solving the <code>root</code> cause of the
problem. See if you can manage to speak to him, in the right way and gain
entrance to his system.</p>
<p><small>This is Chef\'s own workstation, he likes nodejs and webmin. Chef is
not a noob, he knows better than to run webmin on a public port and he always
uses random passwords (so forget about brute-forcing your way)...</small></p>'],['id'=>19]); // chef

//    $this->update('target',['description'=>''],['id'=>22]); // harrison
    $this->update('target',['description'=>'<p>Tweek has setup his own blog to share his thoughts on school and the latest adventures of <i>Wonder Tweek</i>.</p>
<p><small>His blog is closely associated with Craig\'s, since both systems are installed by Wendy. And those who know Wendy, know that she likes her drupal crontabs executed every minute on the spot, by the infamous <code>DrupalConsole</code>...</small><p>'],['id'=>24]); // tweek
    $this->update('target',['description'=>'<p>Craig has setup his own blog to share his thoughts on school and the latest adventures of <i>Super Craig</i>.</p>
<p><small>His blog is closely associated with Tweek\'s, since both systems are installed by Wendy. And those who know Wendy, know that she likes her drupal crontabs executed every minute on the spot, by the infamous <code>DrupalConsole/</code>...</small><p>'],['id'=>25]); // craig


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $this->update('target',['image'=>'registry.echoctf.com:5000/red/lfi-tutorial:v0.7.1'],['id'=>26]);

    }
}
