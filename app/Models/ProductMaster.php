<?php

namespace App\Models;

use App\Models\BaseModel;

class ProductMaster extends BaseModel
{
    
    public function __construct($attributes = array()) {

        $this->setTable('product_master'); // table name
        
        $this->setKeyName('product_master_id'); // primary key name
       
        $this->guard([$this->getKeyName()]); // Add more field to guard
 
        $nonFillable = array_merge(['created_at', 'updated_at'], $this->getGuarded()); 

        // Fillables;
        $this->fillable(
            array_values(array_diff(
                array_merge(
                    \Schema::getColumnListing($this->getTable()),
                    $nonFillable
                ),
                $nonFillable
            ))
        );

        parent::__construct($attributes);
    }
}
