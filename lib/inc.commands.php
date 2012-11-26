<?php

use Psc\System\File;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Configuration;

/**
 *
 * $createCommand = function ($name, array|closure $configure, closure $execute, $help = NULL)
 * 
 * $arg = function ($name, $description = NULL, $required = TRUE) // default: required
 * $opt = function($name, $short = NULL, $withValue = TRUE, $description = NULL) // default: mit value required
 * $flag = function($name, $short = NULL, $description) // ohne value
 */

$createCommand('import-emails',
  array(
    $arg('file', 'the zipFile to the downloaded E-Mail-Archive')
  ),
  function ($input, $output, $command) {
    $config = new Configuration();
    $connectionParams = array(
      'dbname' => 'stop-forum-spam',
      'user' => 'stop-forum-spam',
      'password' => 'aJ7U7Y87jJwyDdrG',
      'host' => 'localhost',
      'driver' => 'pdo_mysql',
      'charset' => 'utf8'
    );
    $conn = DriverManager::getConnection($connectionParams, $config);
    
    $sm = $conn->getSchemaManager();
    if (!$sm->tablesExist(array('emails'))) {
      $emails = new \Doctrine\DBAL\Schema\Table('emails');
      $emails->addColumn("email", "string", array("length" => 200));
      $emails->addColumn("count", "smallint", array("unsigned"=>true, "length" => 3));
      $emails->addColumn("lastseen", "datetime");
      $emails->setPrimaryKey(array("email"));
      $sm->createTable($emails);
      $command->info('table emails created.');
    }

    $zipFile = new File($input->getArgument('file'));
    $zipFile->resolvePath();
    
    $zip = new \ZipArchive();
    $zip->open($zipFile);
    $zip->extractTo((string) $zipFile->getDirectory());
    
    $txtFile = clone $zipFile;
    $txtFile->setExtension('txt');
    
    if (!$txtFile->exists()) {
      throw $command->exitException('no .txt file was found in archive: '.$zipFile.' i tried to find: '.$txtFile);
    }
    
    $csvFile = new Keboola\Csv\CsvFile((string) $txtFile);
    $sql = "INSERT INTO emails VALUES\n ";
    $sqlFile = 'out.sql';
    file_put_contents($sqlFile, '');
    
    foreach($csvFile as $key=>$row) {
      if ((($key+1) % 200) == 0) {
        $sql = mb_substr($sql, 0, -2).";\n\n\n";
        
        file_put_contents($sqlFile, $sql, FILE_APPEND);
        print ".";
          
        $sql = '';
        $sql .= "INSERT INTO emails VALUES\n ";
      }
      
      list($email,$count,$lastseen) = $row;
      
      $sql .= sprintf('(%1$s, %2$s, %3$s) ON DUPLIKATEY KEY UPDATE email = %1$s, count = %2$s, lastseen = %3$s '."\n",
                      $conn->quote($email), $conn->quote($count, \PDO::PARAM_INT), $conn->quote($lastseen));
    }
  },
  'Generiert das SQL fuer den Import der E-Mails und führt diesen aus'
);

$createCommand('query-email',
  array(
    $arg('email', 'die E-Mail-Adresse die zu überprüfen ist')
  ),
  function ($input, $output, $command) {
    $email = $input->getArgument('email');
    
    $config = new Configuration();
    //$config->setSQLLogger(new \Doctrine\DBAL\Logging\EchoSQLLogger());
    $connectionParams = array(
      'dbname' => 'stop-forum-spam',
      'user' => 'stop-forum-spam',
      'password' => 'aJ7U7Y87jJwyDdrG',
      'host' => 'localhost',
      'driver' => 'pdo_mysql',
      'charset' => 'utf8'
    );
    $conn = DriverManager::getConnection($connectionParams, $config);
    
    $sql = "SELECT * FROM emails WHERE email = ?";
    $stmt = $conn->executeQuery($sql, array($email));
    
    $res = $stmt->fetchAll();

    $json = new \Psc\JS\JSONConverter();
    $output->writeln($json->stringify($res));
    
    return 0;
  },
  'Überprüft eine E-Mail nach ihrem Count in der stop-forum-spam datenbank. Gibt das Result als JSON zurück: .count, .lastseen'
);

?>