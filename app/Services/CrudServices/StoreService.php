<?php

namespace App\Services\CrudServices;

use Throwable;
use App\Services\BaseService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;

class StoreService extends BaseService
{
    public function __invoke($request, $storeRequest, $model, $translatedColumns, $showResorse)
    {
        // validate request
        $validation = new $storeRequest();

        $validator = Validator::make($request->all(), $validation->rules(), $validation->messages());
        if ($validator->fails()) return $validator->validate();
        try {
            // get validated data
            $data = $validator->validated();
            // decode all translate data
            foreach ($translatedColumns as $column) {
                if (isset($data[$column])) $data[$column] = json_encode($data[$column]);
            }
            // insert to database
            $item = $model::create($data);
            // return response
            return $this->successResponse($showResorse::make($item), __('api.created'), 200);
        } catch (Throwable $e) {
            return $e;
        }
    }
}
