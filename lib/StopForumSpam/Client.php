<?php

namespace StopForumSpam;

use Psc\System\File;
use Psc\System\Console\Process;
use Psc\JS\JSONConverter;

class Client {
  
  /**
   * die bin-datei zur CLI von StopForumSpam
   *
   * @var Psc\System\File
   */
  protected $daemon;
  
  public function __construct(File $daemon) {
    $this->daemon = $daemon;
  }
  
  /**
   * @returns array $result
   */
  public function queryByEmail($email) {
    $process = Process::build($this->daemon, array('query-email',$email))->end();
    
    if (($exitCode = $process->run()) !== 0) {
      throw new \RuntimeException('Cannot Query Daemon: exit('.$exitCode.') '.$process->getErrorOutput().' '.$process->getOutput().' Running '.$process->getCommandLine());
    }
    
    $json = $process->getOutput();
    
    $result = JSONConverter::create()->parse($json);
    
    return $result;
  }
}
?>