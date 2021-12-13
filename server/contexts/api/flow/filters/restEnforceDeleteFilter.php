<?php

namespace flow\filters\api;

class restEnforceDeleteFilter {
	use \flow\filters\filter;

	public function in() {

            if(strtoupper($this->request->REQUEST_METHOD) != 'DELETE') {
                
                $this->response->respond(["HTTP/1.1 405 Method Not Allowed"]);

                $data['messages'] = ["Method Not Allowed","This resource can only be accessed via HTTP DELETE"];
                $data['status'] = [405 => "Method Not Allowed"];

                $this->response->setData($data);   
                
                $this->out();
            } else {
                $this->FFW();
            }
            
	}

	public function out() {
                
            $this->RWD();
	}

}