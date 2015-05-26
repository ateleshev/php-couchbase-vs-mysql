#!/usr/bin/env php
<?php

require_once __DIR__ . '/bootstrap/core.php';

$document = json_decode(file_get_contents(__DIR__ . '/data/document.json'), true);

$cb = new CouchbaseCluster(CB_HOST, CB_USER, CB_PASS);
$db = $cb->openBucket(CB_BUCKET_NAME, CB_BUCKET_PASS);

$n = 1;
while ($n < TOTAL_ITERATIONS) {
  $documents = [];

  $startedAt = microtime(true);
  for ($i = 0; $i < TOTAL_DOCUMENTS; $i++) {
    $key = "key_" . md5($i) . "_" . microtime(true);

    $document["id"]     = $document["id"]++;
    $document["base64"] = base64_encode($document["content"]);
    $document["hash"]   = md5($document["content"]);
    $document["date"]   = date("Y-m-d");
    $document["time"]   = date("H:i:s");

    $documents[$key] = ['value' => $document];
  }
  // Output::writeln(sprintf("%3.3d) [%.3Fs] Prepared documents: %d", $n, microtime(true) - $startedAt, TOTAL_DOCUMENTS));

  // == SAVE ==

  $startedAt = microtime(true);
  $db->upsert($documents);
  Output::writeln(sprintf("%3.3d) [%.3Fs] Saved to Couchbase: %d", $n, microtime(true) - $startedAt, TOTAL_DOCUMENTS));

  $n++;
}

