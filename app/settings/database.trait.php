<?php

namespace settings;

trait _database {

	private $settings = [];

        protected function readSettings(){
            
            /*
            $settings['app_cache']['type'] = 'file';
            $settings['app_cache']['host'] = '127.0.0.1';
            $settings['app_cache']['user'] = '';
            $settings['app_cache']['pass'] = 'cachepass';
            $settings['app_cache']['name'] = 'cache';
            //*/
            
//            $settings['app_cache'] = [];
//
//            $settings['app_cache']['type'] = 'file';
//            $settings['app_cache']['path'] =  X1_DAT_PATH.'/cache/';
            
            $settings['app_cache']['type'] = 'couchbase';
            $settings['app_cache']['host'] = '127.0.0.1';
            $settings['app_cache']['user'] = '';
            $settings['app_cache']['pass'] = 'cachepass';
            $settings['app_cache']['name'] = 'cache';
            
            $settings['data']['type'] = 'couchbase';
            $settings['data']['host'] = '127.0.0.1';
            $settings['data']['user'] = 'projects';
            $settings['data']['pass'] = 'password';
            $settings['data']['name'] = 'projects';
            
//            $settings['session']['type'] = 'couchbase';
//            $settings['session']['host'] = '127.0.0.1';
//            $settings['session']['user'] = 'sessionuser';
//            $settings['session']['pass'] = 'sessionpass';
//            $settings['session']['name'] = 'sessions';
            
//            $settings['tracker']['type'] = 'couchbase';
//            $settings['tracker']['host'] = '127.0.0.1';
//            $settings['tracker']['user'] = 'tracker';
//            $settings['tracker']['pass'] = 'tracker_pass';
//            $settings['tracker']['name'] = 'track';

            
            $this->settings = &$settings;
        }
        
}
