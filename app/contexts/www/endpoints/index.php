<?php

namespace endpoints\www;

class index {

    use \endpoints\endpointHelper;
    use \Plugins\helper;

    public function __construct($request, $response, $filters) {

        $this->notify(__METHOD__);

        $this->Init($request, $response, $filters);

        $filters = $this->filterInsertBefore('view', 'action');
    }

    public function Execute() {
        
    }

}
