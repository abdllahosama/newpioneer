<?php

namespace App\Services\CrudServices;

use App\Services\BaseService;

class ShowService extends BaseService
{
    public function __invoke($id,$showResorse,$model)
    {
        if (is_numeric($id)) {
            // get item
            $item = $model::where('id', $id)->first();
            // return item
            if ($item)
                return $this->successResponse($showResorse::make($item)->resolve(), null, 200);
        }
        // return errors
        return $this->errorResponse(__('api.not_found.item'));
    }
}
