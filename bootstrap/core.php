<?php

ini_set('memory_limit', '2G');
ini_set("default_socket_timeout", 360);

if (!date_default_timezone_get()) {
  date_default_timezone_set('Europe/Kiev');
}

define(TOTAL_ITERATIONS, 50);
define(TOTAL_DOCUMENTS, 10000);

define(CB_HOST, "127.0.0.1");
define(CB_USER, "");
define(CB_PASS, "");
define(CB_BUCKET_NAME, "search_cache");
define(CB_BUCKET_PASS, "");

define(DB_DRIVER, "mysql");
define(DB_HOST, "127.0.0.1");
define(DB_USER, "");
define(DB_PASS, "");
define(DB_NAME, "test");
define(DB_DSN, DB_DRIVER.':dbname='.DB_NAME.';host='.DB_HOST);

class Output
{
  static protected $resource = STDOUT;

  static public function getResource()
  { // {{{
    return static::$resource;
  } // }}}

  static public function write($v)
  { // {{{
    fwrite(static::getResource(), "{$v}");
  } // }}}

  static public function writeln($v)
  { // {{{
    fwrite(static::getResource(), "{$v}" . PHP_EOL);
  } // }}}

  static public function dump(&$v)
  { // {{{
    fwrite(static::getResource(), print_r($v, true));
  } // }}}

  static public function export(&$v)
  { // {{{
    fwrite(static::getResource(), var_export($v, true));
  } // }}}
}

class Input
{
  static public function read($prompt = "")
  { // {{{
    if (strlen($prompt))
    {
      Output::write($prompt);
    }

    return trim(str_replace(["\r","\n"], ["", ""], fgets(STDIN)));
  } // }}}
}

