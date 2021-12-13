<?php

namespace flow\filters\api;

class dataFilter {
	use \flow\filters\filter;

	public function in() {

            $rawPost = file_get_contents("php://input");
            
            $rawJson = json_decode($rawPost,1);
            
            if($rawJson) {
                $this->request->POST_DATA = array_merge($this->request->POST_DATA,$rawJson);
            }
            
            $this->FFW();
	}

	public function out() {
                
            $this->RWD();
	}

}