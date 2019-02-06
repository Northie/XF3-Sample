<?php

error_reporting(E_ALL &~ E_NOTICE);

define("X1_PATH",  realpath(dirname(__FILE__)."/../../x1i/"));

define("X1_APP_PATH",realpath(dirname(__FILE__)));
define("X1_WEB_PATH",realpath(X1_APP_PATH."/../"));
define("X1_DAT_PATH",realpath(X1_APP_PATH."/X1/"));

define("APP_CLASS_LIST",X1_DAT_PATH."/class-list.static.php");
//define("APP_CLASS_LIST_JSON",X1_DAT_PATH."/class-list.js");

echo __FILE__;
echo "<br />\n";
die("Define constants in above file to contine; then delete these lines");

define("X1_APP_NAME","New App");

include(X1_PATH."/bootstrap.php");

\settings\general::Load()->set(['XENECO','ENV'], 'DEV');
\settings\general::Load()->set(['CACHE_LIFETIME'], 0);

\OS\App::Load()->start();
