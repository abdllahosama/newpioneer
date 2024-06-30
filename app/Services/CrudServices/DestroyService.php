<?php

namespace App\Services\CrudServices;

use App\Services\BaseService;
use Illuminate\Support\Facades\App;

class DestroyService extends BaseService
{
    public function __invoke($id,$model)
    {
        // get item

        $item = $model::where('id', $id)->first();
        // if itexist
        if ($item) {
            // delete item
            $item->delete();
            // return delete
            return $this->successResponse([], __('api.deleted'), 200);
        }
        // if item not found
        return $this->errorResponse(__('api.not_found.item'));
    }
}
