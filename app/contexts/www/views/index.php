<?php
namespace views\www;

class index implements \views\iView {
    
    use \views\view;
         
    public function serve()
    {
        include dirname(__FILE__).'/../templates/index/index.phtml';
        
    }
     
}
