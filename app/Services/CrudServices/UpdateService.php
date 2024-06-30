<?php

namespace App\Services\CrudServices;

use Throwable;
use App\Services\BaseService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;

class UpdateService extends BaseService
{
    public function __invoke($id, $request, $updateRequest, $model, $translatedColumns, $showResorse)
    {
        // validate request
        $validation = new $updateRequest();

        $validator = Validator::make($request->all(), $validation->rules(), $validation->messages());
        if ($validator->fails()) return $validator->validate();

        if (is_numeric($id)) {
            // get item
            $item = $model::where('id', $id)->first();

            if ($item) {
                try {
                    // get validated data
                    $data = $validator->validated();
                    // decode all translate data
                    // update item
                    $item->update($data);
                    // return item
                    return $this->successResponse($showResorse::make($item), __('api.updated'), 200);
                } catch (Throwable $e) {
                    return $e;
                }
            }
        }
        // if item not exist
        return $this->errorResponse(__('api.not_found.item'));
    }
}
