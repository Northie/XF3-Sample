<?php
namespace views\api;

class defaultView implements \views\iView {
    
    private $templatePath = '';
    private $template = '';
    private $data = [];
    private $dataSet = false;

    public function __construct() {
        $this->templatePath = realpath(dirname(__FILE__)."/../templates");
        $this->template = 'json.tpl.php';
        $this->headers = [
            "content-type" => "application/json"
        ];
    }
    
    public function setTemplate($template) {
        $this->template = $template.'.tpl.php';
        
    }

    public function setHeaders($headers) {
        $this->headers = array_merge($this->headers, $headers);
    }
    

    public function setData($data) {
        $this->data = $data;
        $this->dataSet = true;
    }
    
    public function sendHeaders() {
        foreach($this->headers as $key => $val) {
            header($key.": ".$val);
        }
    }
    
    public function sendBody() {
        try {
            if($this->dataSet) {
                include implode(DIRECTORY_SEPARATOR,[$this->templatePath,$this->template]);
            } else {
                throw new Exception("Trying to load template without setting data");
            }
        } catch (Exception $ex) {

        }
        
    }
    
    public function serve() {
        $this->sendHeaders();
        $this->sendBody();
    }
}
