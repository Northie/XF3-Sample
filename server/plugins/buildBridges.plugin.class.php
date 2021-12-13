<?php

namespace Plugins;

class buildBridges extends \Plugins\DefaultHandler
{

    public static function RegisterMe()
    {
        \Plugins\EventManager::Load()->RegisterHandler(__CLASS__, 'onAfterFrontControllerInit');
    
        
    }

    public function Execute()
    {
        
        switch ($this->event) {
            case 'onAfterFrontControllerInit':
                \OS\App::Load()->buildBridges();
                break;
        }

        return true;
    }

}
