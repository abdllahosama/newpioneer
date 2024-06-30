<?php
namespace App\Modules\Bll;
class Utility {

    protected function createPaginationMeta($items, $currentPage, $lastPage, $total): array
    {
        return [
            'data'         => $items,
            'current_page' => $currentPage,
            'last_page'    => $lastPage,
            'items_count'  => $total,
        ];
    }

    public function sendResponse($request, $response, $model){

        $items = $model::query()->paginate($request->per_page ?? 10);
        $responseData = $this->createPaginationMeta($response, $items->currentPage(), $items->lastPage(), $items->total());
        return response()->json([$responseData], 200);

    }

    public function sendResponseAlt($request, $response, $model){

        $items = $model::query()->paginate($request->per_page ?? 10);
        $responseData = $this->createPaginationMeta($response, $items->currentPage(), $items->lastPage(), $items->total());
        return response()->json(['success' => true , 'data' => $responseData], 200);

    }

}
