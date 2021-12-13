<?php

namespace flow\filters\api;

class viewFilter {
	use \flow\filters\filter;

	public function in() {

            $options = $this->getOptions();
            
            $this->view = new \views\api\defaultView();
            
            switch(true) {
                case isset($options['template']):
                    $template = $options['template'];
                    break;
                case ($this->request->HTTP_X_REQUEST_TYPE):
                    $template = strtolower(trim($this->request->HTTP_X_REQUEST_TYPE));
                    break;
                default:
                    $template = 'json';
                    
            }
            
                switch($template) {
                    case 'js':
                        $contentType = 'text/javascript';
                        break;
                    case 'xml':
                        $contentType = 'text/xml';
                        break;
                    case 'json':
                        $contentType = 'application/json';
                        break;
                    default:
                        $contentType = 'text/plain';
                }
            
            
            $this->view->setTemplate($template);
            $this->view->setHeaders(["content-type" => $contentType]);
            
            $this->FFW();
	}

	public function out() {
            
            $data = $this->response->getData();
            
            $this->view->setData($data);
                
            $this->view->serve();
                
            $this->RWD();
	}

}