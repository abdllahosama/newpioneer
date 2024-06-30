<?php

namespace App\Traits;

use App\Bll\Constants;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\QueryBuilder;

trait DashboardResponseTrait
{
    /**
     * @param $data
     * @param string|null $message
     * @param int $code
     * @return JsonResponse
     */
    public function successResponse($data, string $message = null, int $code = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'data' => $data,
            'message' => $message
        ];

        return response()->json($response, $code);
    }

    /**
     * @param string $message
     * @param int $code
     * @return JsonResponse
     */
    public function errorResponse(string $message, int $code = 404): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message
        ];

        return response()->json($response, $code);
    }

    /**
     * @param $items
     * @param $currentPage
     * @param $lastPage
     * @param $total
     * @return array
     */
    protected function createPaginationMeta($items, $currentPage, $lastPage, $total): array
    {
        return [
            'data' => $items,
            'current_page' => $currentPage,
            'last_page' => $lastPage,
            'items_count' => $total,
        ];
    }


    /**
     * @param Builder $collection
     * @param $request
     * @return Builder
     */
//    protected function orderByIndex( $collection, $request)
//    {

//        $orderBy = $request->get('orderBy');
//        $orderType = $request->get('orderType');

//        if ($orderBy == '' || $orderBy == null) $orderBy = 'id';
//        if ($orderType == '' || $orderType == null) $orderType = 'desc';

//        if(request()->routeIs('api.dashboard.banners.index') ||
//            request()->routeIs('api.dashboard.sliders.index')){
//            $orderBy = 'order';
//            $orderType = 'asc';
//        }
//     }

//
//        if (
//            in_array($orderBy, Constants::TRANSLATABLE_COLUMNS) &&
        //    !in_array(request()->route()->getName(), Constants::TRANSLATABLE_COLUMNS_EXCLUDES)
//        ) {
//            $lang = $request->header('Accept-Language');
//            if ($lang == '' || $lang == null) $lang = 'en';
//            $collection = $collection->orderBy($orderBy . '->' . $lang, $orderType);
//        } else {
//
////            if ((request()->routeIs('api.dashboard.sections.index') && $orderBy == "order")) {
////
////                $collection = $collection->orderBy('type', 'asc')->orderBy($orderBy, $orderType);
////            }
//            if (((request()->routeIs('api.dashboard.sliders.index') ||
//                    request()->routeIs('api.dashboard.sections.index')) &&
//                    $orderBy == "order")) {
//                $collection = $collection->orderBy('page', 'asc')->orderBy($orderBy, $orderType);
//            }
//
//            $collection = $collection->orderBy($orderBy, $orderType);
//        }
//
//
//        return $collection;
//    }
}
