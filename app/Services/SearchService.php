<?php

namespace App\Services;

use App\Bll\Constants;
use App\Models\ProductStock;

class SearchService
{
    /**
     * @param $id
     * @param $quantity
     * @param $type
     * @return void
     */
    public function __invoke($item, $request, $searchableColumns): void
    {
        $search = $request->get('term');
        $items = $item->where(function ($query) use ($searchableColumns, $search) {
            $i = 1;

            foreach ($searchableColumns as $column) {
                $isFirstColumn = $i === 1;

                if ($isFirstColumn) {
                        $query->where($column , 'like', '%' . $search . '%');

                } else {
                        $query->orWhere($column , 'like', '%' . $search . '%');

                }

                $i++;
            }
        });
    }
}
