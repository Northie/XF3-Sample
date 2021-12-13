<?php

namespace Plugins;

class initAllModules extends \Plugins\DefaultHandler
{

    public static function RegisterMe()
    {
        //\Plugins\EventManager::Load()->RegisterHandler(__CLASS__, 'onAfterOS\App::start');
        \Plugins\EventManager::Load()->RegisterHandler(__CLASS__, 'onBeforeFrontControllerInit');
        
    }

    public function Execute()
    {
        
        switch ($this->event) {
            case 'onBeforeFrontControllerInit':
                $this->initModules();
                break;
        }

        return true;
    }

   
    private function initModules()
    {
        
        $request = $this->caller->request->getNormalisedRequest();
        
        //if ($request['context'] == 'admin') {
            
            $modulePath = realpath(\X1_APP_PATH."/modules");

            $moduleDirs = scandir($modulePath);
            
            $modules = [];
            
            foreach($moduleDirs as $module) {
                
                $moduleInitClass = "modules\\".$module."\\init";
                
                if(class_exists($moduleInitClass)) {
                    $modules[] = $module;
                }
            }
            foreach ($modules as $module) {
                \modules\factory::Build($module);
            }       
        //}
    }
}
