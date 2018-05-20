<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class BaseModel extends Model
{

    public static function getTableName() {
        return with(new static)->getTable();
    }

    public static function getHiddenField() {
        return with(new static)->getHidden();
    }

    public static function getGuardedField() {
        return with(new static)->getGuarded();
    }

    public static function getFillableField() {
        return with(new static)->getFillable();
    }

    public static function getKeyField() {
        return with(new static)->getKeyName();
    }

    public static function getActive() {
        return with(new static)->where('status', config('constants.STATUS.active.DB_VALUE'));
    }

    public static function getUserData($column_name) {
        return with(new static)->where($column_name, \Auth::user()->emp_id);
    }

    
}
