<?php

namespace StopForumSpam;

class ClientTest extends \PHPUnit_Framework_TestCase {
  
  public function testClientAcceptanceQueryByEmail() {
    $daemon = \Psc\System\Dir::factory(__DIR__.DIRECTORY_SEPARATOR)->sub('../../bin/')->getFile('cli');
    
    $client = new Client($daemon);
    
    $this->assertInternalType('array', $client->queryByEmail('maitrisemonaco@monaco.mc'));
  }
}
?>