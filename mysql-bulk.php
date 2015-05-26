#!/usr/bin/env php
<?php

/**
 * !!! NEED CONFIGURE MySQL SERVER !!!
 * # echo -n "max_allowed_packet=100M" >> /etc/mysql/my.cnf
 */

require_once __DIR__ . '/bootstrap/core.php';

$table = "search_cache_bulk";
$document = json_decode(file_get_contents(__DIR__ . '/data/document.json'), true);

$db = new PDO(DB_DSN, DB_USER, DB_PASS);
$count = $db->exec("CREATE TABLE IF NOT EXISTS `{$table}` (`key` varchar(255) NOT NULL, `document` longblob NOT NULL, PRIMARY KEY (`key`)) ENGINE=InnoDB DEFAULT CHARSET=utf8");

$n = 1;
while ($n < 50) {
  $documents = [];
  try {
    $startedAt = microtime(true);

    for ($i = 0; $i < 10000; $i++) {
      $key = "key_" . md5($i) . "_" . microtime(true);

      $document["id"]     = $document["id"]++;
      $document["base64"] = base64_encode($document["content"]);
      $document["hash"]   = md5($document["content"]);
      $document["date"]   = date("Y-m-d");
      $document["time"]   = date("H:i:s");

      $documents[] = $key;
      $documents[] = json_encode($document);
    }

    $cnt = ceil(count($documents) / 2);
    $query = "INSERT INTO `{$table}` (`key`,`document`) VALUES (?,?)" . str_repeat(',(?,?)', $cnt - 1); 
    
    // Output::writeln(sprintf("%3.3d) [%.3Fs] Prepared documents: %d", $n, microtime(true) - $startedAt, TOTAL_DOCUMENTS));

    // == SAVE ==

    $startedAt = microtime(true);

      $db->beginTransaction();
      $stmt = $db->prepare($query);
      $stmt->execute($documents);
      $db->commit();

    Output::writeln(sprintf("%3.3d) [%.3Fs] Saved to MySQL: %d", $n, microtime(true) - $startedAt, TOTAL_DOCUMENTS));
  } catch (Exception $e) {
    Output::writeln("[ Error ] Cannot save to MySQL: {$e}");
  }
  ++$n;
}

