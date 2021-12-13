<?php

namespace endpoints\api;

abstract class apiEndpoint extends \endpoints\endpoint {
    /*
    use \libs\rest\read {
        \libs\rest\read::Execute as restReadExecute;
    }
    //*/
    
    use apiAuth;
    
    public function getQuery() {
        $cls = get_called_class();
        
        $fqc = explode("\\",$cls);
        $mode = array_pop($fqc);
        
        $resource = false;
        
        $fields = null;
        
        if($mode == 'read') {
            $resource = array_pop($fqc);
            
            $q = explode("?",$this->request->REQUEST_URI);
            
            if($_GET['fields']) {
                $fields = explode(",",$_GET['fields']);
            }
            
            //$readMode = str_replace("/api/".$resource."/","",$q[0]);
            $tmp = explode("/",$q[0]);
            $readMode = array_pop($tmp);
            
            $q[1] = urldecode($q[1]);
            
            $searchParams = [];
            
            switch($readMode) {
                case 'search':
                case 'filter':
                    
                    $params = explode("&",$q[1]);
                    
                    /**
                     * >< = contains, eg a field contains a value from the query
                     * <> = does not contain
                     * enhancement => comma separate potential values
                     */
                    
                    $operatorPattern = "/(><|<>|==|!=|<=|>=|<|>|=|LIKE)/";
                    
                    
                    $pi = -1;
                    foreach($params as $param) {
                        $pi++;           
                        preg_match($operatorPattern,$param,$matches);

                        if($matches[0]) {
                        
                            list($field,$value) = explode($matches[0],$param);
                            if($field == 'fields') {
                                continue;
                            }
                            $field = "`".implode("`.`",explode(".",$field))."`";

                            switch($matches[0]) {
                                case '><':
                                    //contains
                                    if(is_numeric($value)) {
                                        $n1qlPart = '(any v'.$pi.' in TO_NUMBER('.$field.') SATISFIES v'.$pi.' = $v'.$pi.' END)';
                                        $value = $value * 1;
                                    } else {
                                        $n1qlPart = '(any v'.$pi.' in '.$field.' SATISFIES v'.$pi.' = $v'.$pi.' END)';    
                                    }
                                    
                                    $searchParams[] = [
                                        'n1ql'=>$n1qlPart,
                                        'args'=>['v'.$pi => $value]
                                    ];
                                    break;
                                case '<>':
                                    //does not contain
                                    if(is_numeric($value)) {
                                        $n1qlPart = '(any v'.$pi.' in TO_NUMBER('.$field.') SATISFIES v'.$pi.' != $v'.$pi.' END)';    
                                        $value = $value * 1;
                                    } else {
                                        $n1qlPart = '(any v'.$pi.' in '.$field.' SATISFIES v'.$pi.' != $v'.$pi.' END)';    
                                    }
                                    $searchParams[] = [
                                        'n1ql'=>$n1qlPart,
                                        'args'=>['v'.$pi => $value]
                                    ];
                                    break;
                                default:
                                    if($matches[0] == 'LIKE') {
                                        $value = str_replace("*", "%",strtolower($value));
                                        $field = "LOWER($field)";
                                    }

                                    $searchParams[] = [
                                        $field,
                                        $matches[0],
                                        $value
                                    ];
                            
                            }
                        }
                        
                    }                    
                    break;

            }
            
        }
        
        return [
            'resource' => $resource,
            'mode'=>$mode,
            'type'=>$readMode,
            'params'=>$searchParams,
            'fields'=>$fields
        ];
    }
    
    public function readQuery($db,$q) {
        
        $req = $this->request->getCombinedQuery();
        reset($req);
        \modules\ChromePHP\libs\Logger::log([$req,key($req)]);
        
        $id0 = false;
        foreach($req as $k => $v) {
            if($k && !$v) {
                $id0 = $k;
                break;
            }
        }
        $id1 = $req['id'];
        $id2 = $req[$this->keyField];

        $id = \utils\Tools::coalesceFalsy($id0,$id1,$id2);

        

        $args = [];
        
        $r = $q['resource'];
        
        $fields = 'data.*';
        
        if($q['fields']) {
            $fields = array_unique(array_merge([$this->keyField],$q['fields']));
            $fields = "data.".implode(", data.data.",$fields);
        }

        switch(true) {
            case ($q['type'] == 'search'):

                $args = [];
                $n1qls = [];
                foreach($q['params'] as $i => $p) {
                    
                    if(isset($p['n1ql'])) {
                        $n1qls[] = $p['n1ql'];
                        if(isset($p['args'])) {
                            foreach($p['args'] as $k => $v) {
                                $args[$k] = $v;
                            }
                        }
                    } else {

                        if($p[2] === 'true' || $p[2] === 'false') {
                            $n1qls[] = " {$p[0]} {$p[1]} {$p[2]}";
                        } else {
                            if(is_numeric($p[2])) {
                                $n1qls[] = " TO_NUMBER(`data`.`data`.{$p[0]}) {$p[1]} \$q$i";
                                $args['q'.$i] = ($p[2] * 1);
                            } else {
                                $n1qls[] = " {$p[0]} {$p[1]} \$q$i";
                                $args['q'.$i] = $p[2];
                            }                
                        }
                    }
                }
                
                $n1ql = "SELECT $fields from data where META(data).id like '".$r."_%' and (".implode(" or ",$n1qls).")";
                break;
            case ($q['type'] == 'filter'):
                
                $args = [];
                $n1qls = [];
                foreach($q['params'] as $i => $p) {
                    
                    
                    if($p[2] === 'true' || $p[2] === 'false') {
                        $n1qls[] = " {$p[0]} {$p[1]} {$p[2]}";
                    } else {
                        if(is_numeric($p[2])) {
                            $n1qls[] = " TO_NUMBER(`data`.`data`.{$p[0]}) {$p[1]} \$q$i";
                            $args['q'.$i] = ($p[2] * 1);
                        } else {
                            $n1qls[] = " {$p[0]} {$p[1]} \$q$i";
                            $args['q'.$i] = $p[2];
                        }                
                    }
                }
                $n1ql = "SELECT $fields from data where META(data).id like '".$r."_%' and (".implode(" and ",$n1qls).")";

                break;            
            case $id:
                
                $n1ql = "SELECT $fields from data where META(data).id = \$key";
                
                $args['key'] = $r.'_'.$id;
                
                break;
            default:
                $n1ql = "SELECT $fields from data where META(data).id like '".$r."_%'";

        }
   

        //\modules\ChromePHP\libs\Logger::log([$n1ql,$args]);

        //print_r([$n1ql,$args]); die();

        $rs = $db->query($n1ql,$args);

        foreach($rs->rows as $i => $row) {
        
            if($row->data) {
                $this->data['resources'][$row->data->{$this->keyField}] = $row->data;
                $this->data['resources'][$row->data->{$this->keyField}]->key = $r.'_'.$row->data->{$this->keyField};                
            } else {
                $this->data['resources'][$row->{$this->keyField}] = $row;
                $this->data['resources'][$row->{$this->keyField}]->key = $row->{$this->keyField};                
            }

        }
        unset($rs->rows);

        //$this->data['meta'] = $rs;
    }
    
}