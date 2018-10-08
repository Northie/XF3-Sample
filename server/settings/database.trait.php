<?php

namespace settings;

trait _database {

	private $settings = [];

        protected function readSettings(){

            $settings['app_cache']['type'] = 'file';
            $settings['app_cache']['path'] =  X1_DAT_PATH.'/cache/';
            
            $settings['app_cache']['type'] = 'none';
//            $settings['app_cache']['host'] = '127.0.0.1';
//            $settings['app_cache']['user'] = '';
//            $settings['app_cache']['pass'] = 'cachepass';
//            $settings['app_cache']['name'] = 'cache';
            
            $settings['data']['type'] = '';
            $settings['data']['host'] = '';
            $settings['data']['user'] = '';
            $settings['data']['pass'] = '';
            $settings['data']['name'] = '';
            

            
            $this->settings = &$settings;
        }
        
}
