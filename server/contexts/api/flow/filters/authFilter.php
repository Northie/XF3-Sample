<?php

namespace flow\filters\api;

class authFilter {

    use \flow\filters\filter;

    private $messages = [];
    private $status = [];
    
    /**
     * 
     * @return boolean
     * @desc determine if the client is allowed to access the requested resource
     */
    
    public function in() {

        if (!$this->before(__METHOD__, $this)) {
            $this->out();
        }
        
        //expected header:
        //Authorization: Bearer {token}
        //where token will match a key api_{token} in the session bucket
        
        $authString = trim($this->request->HTTP_AUTHORIZATION);

        list($bearer, $token) = explode(" ", $authString);

        //so, if the value can be split into litersl "Bearer" and something, we'll proceed
        
        if ($bearer == 'Bearer' && $token != '') {
            
            //get connection to data bucket
            $dbs = \settings\database::Load()->get('data');
            $db = \services\data\object\vendor\couchbase\factory::Build($dbs);
            
            //get connection to session bucket
            $dba = \settings\database::Load()->get('session');
            $adb = \services\data\object\vendor\couchbase\factory::Build($dba);
            
            //look in the session bucket for the key defined by the token
            $user = $adb->read('api_'.$token);
            if($user) {
                //check the expires timestamp of the dataset
                if($user['expires'] < time()) {
                    $this->unAuthorised('TokenExpired');
                } else {
                    //the key "key" from the session data should reference a user with key "key" value in the data bucket
                    $userAcc = $db->read($user['key']);
                    //if the user exists
                    if($userAcc) {
                        
                        //merge the user account details into the session account details
                        $user = array_merge($userAcc,$user);
                        
                        //lets look for what was actually requested
                        $r = new \ReflectionObject($this->request->getEndpoint());
                        
                        //split endpoint class on backslash.
                        $endpointNs = explode("\\",$r->getName());
                        
                        //The last two elements give the resource (eg page, order, etc) and the (CRUD) method, uisng array pop, work backwards
                        $method = array_pop($endpointNs);
                        $resource = array_pop($endpointNs);
                        
                        //look for an array key in the acl for the resource and the method, eg ['order'['read']
                        $allowed = @$user['acl'][$resource][$method];
                        //allowed will be a boolean if found or null if not foucd
                        
                        //if found and true
                        if($allowed) {
                            //set the user to the session
                            $session = \utils\XSession::Load('apiUser');
                            $session->set('user',$user);

                            $this->notify('ApiUserAuthenticated');

                            if (!$this->after(__METHOD__, $this)) {
                                return false;
                            }
                            //proceed if plugins don't prohibit getting this far
                            $this->FFW();

//all failures trigger the unAuthorised method, with a relevant error message
                            
                        } else {
                            $this->unAuthorised('ACL Said No');
                        }
                        
                    } else {
                        $this->unAuthorised('NoUser');
                    }
                }
            } else {
                $this->unAuthorised('TokenNotFound');
            }
        } else {
            $this->unAuthorised('NoToken');
        }
    }

    public function out() {

        if (!$this->before(__METHOD__, $this)) {
            $this->RWD();
        }

        //set error messages in response data
        if(key($this->status) == 401) {
            $data = $this->response->getData();

            $response = [
                "HTTP/1.1 401 Unauthorized",
            ];
        
            foreach($this->messages as $i => $msg) {
                $response["X-MSG-".$i] = $msg;
            }

            $this->response->respond($response);

            $data['messages'] = $this->messages;
            $data['status'] = $this->status;

            $this->response->setData($data);            
        }
        
        if (!$this->after(__METHOD__, $this)) {
            return false;
        }

        $this->RWD();
    }
    
    /**
     * 
     * @desc log the message $msg, prevent the filter list reaching the action filter by calling the out method.
     */
    private function unAuthorised($msg='') {
        $this->notify('UnAuthorisedRequest');
        $this->messages[] = 'UnAuthorisedRequest';
        if($msg) {
            $this->notify('UnAuthorisedRequest'.$msg);
            $this->messages[] = $msg;
        }
        
        $this->status = [401=>'Unauthorized'];
        
        $this->out();
    }
}
