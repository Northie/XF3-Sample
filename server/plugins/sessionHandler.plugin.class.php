<?php

namespace Plugins;

class sessionHandler extends DefaultHandler
{

	public static function RegisterMe()
	{
		\Plugins\EventManager::Load()->RegisterHandler(__CLASS__, 'onRequestNormalised');
	}

	public function Execute()
	{
		/*
		switch ($this->event) {
			case 'onRequestNormalised':	
				$this->setSessionHandler();
				$this->setSecurityToken();
				break;
		}
		//*/
		return true;
	}

	private function setSessionHandler() {
		
		if(\settings\registry::Load()->get('ControllerType') != 'WEB') {
			return;
		}
		
		$request = $this->caller->request->getNormalisedRequest();
		//*
		if($request['context'] != 'api') {
			ini_set('session.serialize_handler','php_serialize');

			session_name('OS_SESS_ID');

			$sessionStorageSettings = \settings\database::Load()->get('session');

			$dataService = \services\data\factory::Build($sessionStorageSettings);

			$sessionHandler = new \utils\sessionHandler($dataService);

			session_set_save_handler($sessionHandler);

			session_start();
			
		}
		//*/

	}
	
	private function setSecurityToken() {
		
		$key = date('YmdH');
		
		$ttl = \settings\general::load()->get('xsrf','ttl');
		
		$token = $_SESSION['security_token'];
		
		if($token == '') {
			$token = \utils\Tools::encryptStr((microtime(1)), $key);
			$_SESSION['security_token'] = $token;			
		} else {
			$token = \utils\Tools::decryptStr($token, $key);
			//regenerate if expired
			if(!\is_numeric($token) || (time() > $token + $ttl)) {
				$token = \utils\Tools::encryptStr(microtime(1), $key);	
				$_SESSION['security_token'] = $token;
			}
		}
		\settings\general::load()->set(['xsrf','token'],$token);
	}

}