<?php

namespace StopForumSpam;

use Psc\PSC;

class Console extends \Psc\System\Console\Console {
  
  public function addCommands() {
    // see inc.commands.php
    $this->cli->addCommands($this->includeCommands);
  }
}
?>