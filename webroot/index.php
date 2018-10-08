<?php

$t1 = microtime(1);
include("../server/bootstrap.php");

$contexts = [
    'www',
    //'admin',
    //'api',
];

$app = new \flow\controllers\FrontController([
    'contexts'=>[
        'names' => $contexts,
        'default'=>'www',
        'type'=>  \flow\controllers\FrontController::CONTEXT_TYPE_FOLDER,
        'base'=>'x1-empty-app/webroot'
    ]
]);

$app->Init();
$app->Execute();
//*/
//$c->getResponse()->respond();

//end
/*
$t2 = microtime(1);

$m0 = memory_get_peak_usage(0);
$m1 = memory_get_peak_usage(1);

$debug = [
   "Execution time"=>(1000 * ($t2-$t1))." ms",
   "Memory used"=>round(($m0 / (1024 * 1024)),3)." Mb",
   "Memory reserved"=>round(($m1 / (1024 * 1024)),3)." Mb",
   "Plugins"=> \Plugins\Plugins::Load()->debug(),
   "Files included"=>get_included_files()
];

echo "<pre>".print_r($debug,1)."</pre>";
//*/