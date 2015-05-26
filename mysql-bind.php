#!/usr/bin/env php
<?php

require_once __DIR__ . '/bootstrap/core.php';

$table = "search_cache_bind";
$document = json_decode(file_get_contents(__DIR__ . '/data/document.json'), true);

$db = new PDO(DB_DSN, DB_USER, DB_PASS);
$count = $db->exec("CREATE TABLE IF NOT EXISTS `{$table}` (`key` varchar(255) NOT NULL, `document` longblob NOT NULL, PRIMARY KEY (`key`)) ENGINE=InnoDB DEFAULT CHARSET=utf8");

$n = 1;
while ($n < TOTAL_ITERATIONS) {
  $startedAt = microtime(true);

  try {
    $db->beginTransaction();
    $stmt = $db->prepare("INSERT INTO `{$table}` (`key`, `document`) VALUES (:key, :document)");

    for ($i = 0; $i < TOTAL_DOCUMENTS; $i++) {
      $key = "key_" . md5($i) . "_" . microtime(true);

      $document["id"]     = $document["id"]++;
      $document["base64"] = base64_encode($document["content"]);
      $document["hash"]   = md5($document["content"]);
      $document["date"]   = date("Y-m-d");
      $document["time"]   = date("H:i:s");

      $stmt->bindParam(':key', $key);
      $stmt->bindParam(':document', json_encode($document));
      $stmt->execute();
    }

    $db->commit();
    Output::writeln(sprintf("%3.3d) [%.3Fs] Saved to MySQL: %d", $n++, microtime(true) - $startedAt, TOTAL_DOCUMENTS));
  } catch (Exception $e) {
    Output::writeln("[ Error ] Cannot save to MySQL: {$e}");
  }
}

