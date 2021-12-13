<?php

namespace Plugins;

class apiRouting extends \Plugins\DefaultHandler
{

    private $fc;

    public static function RegisterMe()
    {
        
        \Plugins\EventManager::Load()->RegisterHandler(__CLASS__, 'onRequestNormalised');
        \Plugins\EventManager::Load()->RegisterHandler(__CLASS__, 'onBeforeFrontControllerInit');
        \Plugins\EventManager::Load()->RegisterHandler(__CLASS__, 'onAfterFrontControllerCreateEndpoint');
        \Plugins\EventManager::Load()->RegisterHandler(__CLASS__, 'onAfterFrontControllerCreateModuleEndpoint');
        
    }

    public function Execute()
    {

        switch ($this->event) {
            case 'onBeforeFrontControllerInit':
                $this->getAPIversion();
                break;
            case 'onRequestNormalised':
                $this->addRoutes();
                break;
            case 'onAfterFrontControllerCreateEndpoint':
            case 'onAfterFrontControllerCreateModuleEndpoint':
                $this->addFilters();
                break;
        }

        return true;
    }

    private function getAPIversion() {

        switch ($this->caller->getContextType()) {
            case (\flow\controllers\FrontController::CONTEXT_TYPE_FOLDER):
                $pattern = "|api/v([1-9+][0-9]*)/|";
                break;
            case (\flow\controllers\FrontController::CONTEXT_TYPE_DOMAIN):
            case (\flow\controllers\FrontController::CONTEXT_TYPE_SUBDOMAIN):
                $pattern = "|^/v([1-9+][0-9]*)/|";
                break;
        }

        if (preg_match($pattern, $this->caller->request->REQUEST_URI, $matches)) {

            $version = $matches[1];

            $vD = explode("/v{$version}/", $this->caller->request->REQUEST_URI);

            if (count($vD) == 2) {
                $this->caller->request->REQUEST_URI = $vD[0] . DIRECTORY_SEPARATOR . $vD[1];
                $this->caller->request->API_VERSION = $version;
            }
        }
    }

    private function addRoutes()
    {
        $request = $this->caller->request->getNormalisedRequest();

        if ($request['context'] == 'api') {
            
            switch($this->caller->request->REQUEST_METHOD) {
                case 'GET':
                    $action = 'read';
                    break;
                default:
                    if($request['endpoint'] != 'auth') {
                        $action = 'read';
                    }
            }
            

            $r = explode("?",$this->caller->request->REQUEST_URI);
            $aR = explode(".",$r[0]);
            
            $exts = [
                'js',
                'xml'
            ];
            
            if(in_array($aR[count($aR)-1],$exts)) {
                $view = array_pop($aR);
            }
            
            $r[0] = implode(".", $aR);
            
            $this->caller->request->REQUEST_URI = implode("?", $r);
            
            $pathQuery = $this->caller->request->getQuery()['path'];
            
            if($request['endpoint'] == 'auth') {
                $action = key($pathQuery);
            } 
            
            $request['endpoint'] = "v".$this->caller->request->API_VERSION."\\".$request['endpoint']."\\".$action;
            
            $this->caller->request->normalise(['endpoint'=>$request['endpoint']]);

            $request = $this->caller->request->getNormalisedRequest();
            
            $i = 0;
            $newPathQuery = [];
           
            foreach($pathQuery as $key => $val) {
                if($i == 0 && is_int($key)) {
                    $newPathQuery[] = 'id';
                    $newPathQuery[] = $key;
                } else {
                    $newPathQuery[] = $key;
                    $newPathQuery[] = $val;
                }
                $i++;
            }
            if($newPathQuery) {
                $this->caller->request->addPathQuery($newPathQuery);
            }
        }
    }
    
    private function addFilters() {
        $request = $this->caller->request->getNormalisedRequest();
        
        $r = explode("?",$_SERVER['REQUEST_URI']);
        $aR = explode(".",$r[0]);

        $exts = [
            'js',
            'xml'
        ];

        $view = 'json';
        if(in_array($aR[count($aR)-1],$exts)) {
            $view = array_pop($aR);
        }  

        if($request['context'] == 'api') {
            if($this->caller->endpoint) {
                $this->caller->endpoint->filterInsertAfter('api\\view', 'dispatch',['template'=>$view]); //anchor view close to dispatch
                $this->caller->endpoint->filterInsertBefore('api\\data', 'action');
            } else {

                $this->caller->response->setData(["404"=>"Not Found"]);
                $this->caller->response->respond(["HTTP/1.1 404 Not Found"]);
            }
        }
    }

}
