<?php
namespace Kernel;

class CodeTemplate{
    
    public static function create($templateName, $params, $dir = false, $resPath = false){           
        $dir = !$dir ? 'fw/templates/' : $dir; 
        $template = $dir . $templateName . '.php';
        try{
            if(!file_exists($template)){
                throw new Exception("File '{$template}' not found!");
                return false;
            }
        }catch(Exception $e){
            exception($e);
        }
        
        $file = file_get_contents($template);
        $path = self::extractPath($file);
        $resPath = !$resPath ? $path . $params['filename'] . '.php' : $resPath . $params['filename'] . '.php';
        if(file_exists($resPath)){
            return true;
        }

        $file = self::replaceVars($file, $params);
        $file = self::removePathFromFile($file, $path, $template);
        try{
            if(file_put_contents($resPath, $file)){
                Log::add('CoreTemplate::create()', "Template '{$resPath}' was generated");
                return true;
            }
            throw new Exception("Template '{$resPath}' is not generated");
        }catch(Exception $e){
            exception($e);
        }
        
        return false;
    }
    
    public static function extractPath($file){
        $path = explode('PATH:', $file);
        $path = explode('*/', $path[1]);
        return trim($path[0]);
    }
    
    public static function replaceVars($file, $params){
        $count = count($params);
        foreach($params as $key => $val){
            $file = str_replace('/*$' . $key . '*/', $val, $file);
        }
        return $file;
    }
    
    public static function removePathFromFile($file, $path, $template){
        $file = str_replace('PATH:', '', $file);
        $file = str_replace($path, 'Automatically was generated from a template '. $template, $file);
        return $file;
    }
    
}