<?php

namespace Extend;

class Set{
    
    public function tableName(){
        
        return 'tablename';
        
    }

    public function ifNotFound(){ // be call if not found searching entry
        
        return NULL;
        
    }

    public function afterAdding(){ // be called after adding entry
        
        return NULL;
        
    }

    public function afterUpdating(){ // be called after updating entry
        
        return NULL;
        
    }
    
    public function afterRemoving(){ // be called after removing entry

        return NULL;

    }

    public function errs(){
        
        return [];
        
    }

    public function rules(){
        
        return [];
        
    }

    public function defaultRows(){
        
        return [];
        
    }
    
}

?>