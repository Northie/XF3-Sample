<?php

namespace endpoints\api;

trait apiAuth{
    public function addAuthFilter() {
        $this->filterInsertBefore('api\\auth', 'action');
    }
}