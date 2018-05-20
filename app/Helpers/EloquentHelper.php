<?php

function commit() {
    DB::commit();
}

function rollback() {
    DB::rollback();
    
}

function errorLog($e) {
    Log::error($e);
}

function abortPage() {
    abort(500, 'Something went wrong, please try again later!');
}

function beginTransaction() {
    DB::beginTransaction();
}

/**
 * @param string $model
 * @param array $data
 * @return inserted id
 */
function store($model, array $data) {
    $res = "";
    $res = $model::create($data);
    return [$res->getKey()];
}

function create($model, array $data) {

    try {
            // store call
            $res = $model::create($data);
    } catch(Exception $e) {
            rollback();
            errorLog($e);
            abortPage();
    }
    return [$res->getKey()];
}


/**
 * @param  array  $data
 * @param  $id
 * @return mixed
 */
function updateOrCreate($model, array $data, $id, $attribute = "id") {
    $res = "";
    // try {
        
        $result = $model->where($attribute, $id)->first();
        if (is_null($result)) {
            return create($model, $data);

        } else {
            $res = $model::where($attribute, $id);
            $res->update($data);
            $res = $res->first()->getKey();
            return [$res];
        }
    // } catch(Exception $e) {
        // rollback();
        // errorLog($e);
        // abortPage();
    // }
}

/**
 * @param  array  $data
 * @param  $id
 * @return mixed
 */
function updateOrCreateWithStatus($model, array $data, $cond) {
    $res = "";
    // try {
        
        $result = $model->find($data)->where('status',2);
        if (is_null($result)) {
            create($model, $data);
        } else {
            $input = [];
            $res = $model::where($data)->get();
            
            if ($res && count($res)) {
                $res_data = $res->toArray();
                foreach ($res_data as $key => $value) {
                    if ($value['status'] == config('constants.STATUS.inactive.DB_VALUE')) {
                        $input['status'] = config('constants.STATUS.active.DB_VALUE');
                        $model::where($model->getKeyField(), $value[$model->getKeyField()])->update($input);
                        $input = [];
                    }
                }
            } else {
                
                $input['status'] = config('constants.STATUS.closed.DB_VALUE');
                $model::where($cond, $data[$cond])->update($input);
                $data['status'] = config('constants.STATUS.active.DB_VALUE');
                create($model, $data);
            }
            return ['res', $res];
        }
    // } catch(Exception $e) {
        // rollback();
        // errorLog($e);
        // abortPage();
    // }
}

/**
 * @param string $model
 * @param array $data
 * @param $id
 * @param string $attribute
 * @return updated record
 */
function update($model, array $data, $id, $attribute="id") {
    // dd($attribute);
    $res = "";
    // try {
        $res = $model::where($attribute, '=', $id)->update($data);
    // } catch(Exception $e) {
    //     rollback();
    //     errorLog($e);
    //     abortPage();
    // }
        // dd($res);
    return ['res', $res];
}

/**
 * @param string $model
 * @param array $data
 * @param $id
 * @param string $attribute
 * @return updated record
 */
 function fillUpdate($model, array $data, $id, $attribute = "id") {
    // dd($model);
    // $mod = checkModule($model);
    // if(isset($mod) && isset($mod['mod']) && isset($mod['mod'][0])) {
    //     $prefix = config('constants.'.$mod['mod'][0].'.prefix');
    // }
    // $preimage = auditPreImage($model, $id, $attribute, $prefix);

    $res = "";
    try {
        $res = $model::where($attribute, $id)->first();
        $res->fill($data);
        $res->save();
    } catch(Exception $e) {
        rollback();
        errorLog($e);
        abortPage();
    }

    // $common_result = auditCommon($res, 2, $prefix);
    // $post_result = auditPostImage($res, $prefix);
    // $result = array_merge($common_result, $post_result, $preimage);

    // $modelNameSpace = "\\App\\Models\\Audit\\".$mod['mod'][0];
    // $audit_res = $modelNameSpace::create($result);
    // dd($audit_res);
    return ['res', $res];
}


 function fillUpdateRaw($model, array $data, $id, $attribute = "id") {
    $res = "";
    try {
        $res = $model::where($attribute, $id)->first();
        
        $res->slug = '';
        $res->save();

        $res->fill($data);
        $res->save();

    } catch(Exception $e) {
        rollback();
        errorLog($e);
        abortPage();
    }
    return ['res', $res];
}
/**
 * @param string $model
 * @param array $data
 * @param $id
 * @param string $attribute
 * @return updated record
 */
 function fillUpdateWithWhere($model, array $data, array $where) {
    $res = "";

    try {
        $res = $model::where($where)->first();
        $res->fill($data);
        $res->save();
    } catch(Exception $e) {
        rollback();
        errorLog($e);
        abortPage();
    }
    return ['res', $res];
}

/**
 * @param string $model
 * @param $id
 * @param string $attribute
 * @return deleted record
 */
function delete($model, $id, $attribute="id") {
    $res = "";
    // try {
        if (is_array($id)) {
            $res = $model::destroy($id);
        } else {
            if ($model::where($attribute, $id)->exists())
            $res = $model::where($attribute, $id)->delete();
        }
    // } catch(Exception $e) {
        // rollback();
        // errorLog($e);
        // abortPage();
    // }

}

/**
 * @param $model
 * @param $id
 * @param array $columns
 * @param array $order
 * @return record by id
 */
function find($model, $id, array $order, $columns = array('*')) {
    $pdo = DB::connection()->getPdo();
    $pdo->exec('SET TRANSACTION ISOLATION LEVEL READ COMMITTED');
    return $model::sharedLock()->orderBy($order['column_name'], $order['preference'])->find($id, $columns);
}

/**
 * @param $model
 * @param $attribute
 * @param $value
 * @param array $columns
 * @param array $order
 * @return record by param
 */
function findBy($model, array $match_condition, array $order, $columns = array('*')) {
    $pdo = DB::connection()->getPdo();
    $pdo->exec('SET TRANSACTION ISOLATION LEVEL READ COMMITTED');
    return $model::sharedLock()->where($match_condition)->orderBy($order['column_name'], $order['preference'])->get($columns)->toArray();
}

/**
 * @param  string $model
 * @param  string $value
 * @param  string $key
 * @param  array $order
 * @return array
 */
function lists($model, $value, $key = null, array $order) {
    $pdo = DB::connection()->getPdo();
    $pdo->exec('SET TRANSACTION ISOLATION LEVEL READ COMMITTED');
    $lists = $model::sharedLock()->orderBy($order['column_name'], $order['preference'])->lists($value, $key);
    return $lists;
}

/**
 * @param  string $value
 * @param  string $key
 * @return array
 */
function execSelect($query, $array) {
    $pdo = DB::connection()->getPdo();
    $pdo->exec('SET TRANSACTION ISOLATION LEVEL READ COMMITTED');
    $res = DB::select($query, $array);
    return $res;
}

/**
 * @param  string $value
 * @param  string $key
 * @return array
 */
function execSQL($query, $array) {
    $res = DB::statement($query, $array);
    return $res;
}

function getColumnList($model) {
    return removePrefix(excludeColumn($model::getFillableField(), array_merge($model::getHiddenField(), $model::getGuardedField(), config('constants.timestamp'), config('constants.exclude_cd'))), $model::getPrefix());
}

function getColumns($model) {
    
    return excludeColumn($model::getFillableField(), array_merge($model::getHiddenField(), $model::getGuardedField(), config('constants.timestamp'), config('constants.exclude_cd')));
}