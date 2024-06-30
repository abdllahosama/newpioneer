<?php

namespace App\Services\CrudServices;

use App\Modules\Customers\Models\Customers;
use App\Services\BaseService;
use App\Services\SearchService;
use Illuminate\Support\Facades\Validator;

class IndexService extends BaseService
{
    public function __invoke($request, $listRequest, $model, $searchableColumns, $relations, $relationSearchableColumn, $listResorse , $filters = [])
    {
        $validation = new $listRequest();
        $rules = $validation->rules();
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) return $validator->validate();
        // prepare model
        $items = $model::query()->with($relations)->orderBy('created_at', 'desc');
        // $items = Customers::all();
        // if search

        if ($request->has('term')) {
            $search = new SearchService;
            $search($items, $request, $searchableColumns);
        }

        // if filters
        if($filters && count($filters) > 0){
            foreach ($filters as $filter) {
                if ($request->has($filter)) {
                    $items = $items->where($filter, $request->$filter);
                }
            }
        }

        // if order by

        // $items = $this->orderByIndex($items, $request);
        // paginate params
        if($items){
            $items = $items->paginate($request->per_page ?? 10);
        }
        // resolve collection
        $response = $listResorse::collection($items)->resolve();
        // build response
        $responseData = $this->createPaginationMeta($response, $items->currentPage(), $items->lastPage(), $items->total());
        // return data
        return $this->successResponse($responseData, null, 200);
    }
}
