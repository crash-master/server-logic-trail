<?php
namespace Kernel;

class Validator{
    private static $check_func;
    private static $init_flag;
    
    public static function init(){
        
        self::$init_flag = true;
        
    }
            
    public static function addRule($name,$func){
        
        self::$check_func[$name] = $func;
        
        return true;
        
    }
    
    public static function rules(&$data, $rules, $errtext = NULL){
        
        if(!self::$init_flag)
            self::init();
        
        $count_data = count($data);
        
        $data_keys = array_keys($data);
        
        $errs = array();
        
        for($i=0;$i<$count_data;$i++){
            
            if(is_array($rules[$data_keys[$i]])){
                
                $count_rules = count($rules[$data_keys[$i]]);
                
                $rules_keys = array_keys($rules[$data_keys[$i]]);
                
                for($k=0;$k<$count_rules;$k++){
                    
                    if(is_int($rules_keys[$k]) and !is_object($rules[$data_keys[$i]][$rules_keys[$k]])){
                                                
                        $func = self::$check_func[$rules[$data_keys[$i]][$rules_keys[$k]]];
                        
                        $r = $func( $data[$data_keys[$i]] );
                        
                        if( !$r ){
                            
                            if(is_array($errtext) and isset($errtext[$data_keys[$i]]))
                                $errs[$data_keys[$i]] = $errtext[$data_keys[$i]];
                            else
                                $errs[$data_keys[$i]] = 'Error of input in field of '.$data_keys[$i];
                            
                            break;
                            
                        }
                        
                        $data[$data_keys[$i]] = $r;                        
                        
                    }else{
                        if(!is_object($rules[$data_keys[$i]][$rules_keys[$k]])){
                            
                            $func = self::$check_func[$rules_keys[$k]];
                            $res = $func( $data[$data_keys[$i]], $rules[$data_keys[$i]][$rules_keys[$k]] );
                            
                        }else{
                            $func = $rules[$data_keys[$i]][$rules_keys[$k]];
                            $res = $func( $data[$data_keys[$i]] );
                        }
                        
                        $data[$data_keys[$i]] = $res;
                        
                        if(!$res){

                            if(is_array($errtext) and isset($errtext[$data_keys[$i]]))
                                $errs[$data_keys[$i]] = $errtext[$data_keys[$i]];
                            else
                                $errs[$data_keys[$i]] = 'Error of input in field of '.$data_keys[$i];

                            break;

                        }
                        
                    }
                    
                }
                
            }
            
        }
        
        return $errs;
        
    }
}
?>