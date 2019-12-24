<?php
class GameCommand extends CConsoleCommand {
  public function run($args)
  {
    parent::run($args);
  }

  public function actionStart($at=false,$in=false)
  {
    // Start the competition $at a specific time
    // Start the competition $in number of seconds from now
  }

  public function actionStop($at=false,$in=false)
  {
    // Stop the competition $at a specific time
    // Stop the competition $in number of seconds from now
  }

  public function actionPause($at=false,$in=false)
  {
    // Pause the competition $at a specific time
    // Pause the competition $in number of seconds from now
  }
}
