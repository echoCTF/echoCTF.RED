<?php

use yii\db\Migration;

class m260903_205144_add_default_writeup_rules extends Migration
{
  /**
   * {@inheritdoc}
   */
  public function safeUp()
  {
    $this->upsert('sysconfig', [
      'id' => 'writeup_rules',
      'val' => "<p><code class='text-warning'>Write or paste your writeup in plain text format. Markdown format is preferred. If you are submitting a video writeup feel free to use the <kbd>&lt;embed&gt;</kbd> code provided to you</code></p><p class='text-danger'><b>Note:</b> Please don't waste our time with non writeup submissions.</p> <p>A few notes:</p> <ul> <li>Leave the flag contents out. Nobody needs to see the actual string.</li> <li>Skip the big H1 title at the top of your writeup, that gets added automatically.</li> <li>No need to include hostnames or IPs. We already have that info attached to the submission, so repeating it just adds clutter.</li> <li>Same goes for your name or any author details. We already have that info attached to the submission, so repeating it just adds clutter.</li> <li>Cut down on raw tool output. A giant RustScan banner or wall of scan results doesn't teach anyone anything, just trim it to what matters.</li> <li>Do not paste source code from the challenge files unless it's actually needed to explain what you found.</li> <li>And the one that matters most: tell us how you got there. We want to know why you tried something, what made you think to pivot, what the output actually told you. A list of commands with no reasoning behind them isn't really a writeup, it's just a log.</li> </ul>"
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function safeDown()
  {
    $this->delete('sysconfig', ['id' => 'writeup_rules']);
  }
}
