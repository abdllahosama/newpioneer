<?php

namespace App\Services\CrudServices;

use App\Services\BaseService;

class EditService extends BaseService
{
    public function __invoke($id, $editResorse, $model)
    {
        // get item
        if (is_numeric($id)) {
            $item = $model::where('id', $id)->first();
            // return item
            if ($item) {
                return $this->successResponse($editResorse::make($item)->resolve(), null, 200);
            }
        }
        // if item not exist
        return $this->errorResponse(__('api.not_found.item'));
    }
}
