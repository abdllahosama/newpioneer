<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\CrudServices\IndexService;
use App\Services\CrudServices\ShowService;
use App\Services\CrudServices\EditService;
use App\Services\CrudServices\UpdateService;
use App\Services\CrudServices\StoreService;
use App\Services\CrudServices\DestroyService;

class CRUDController extends Controller
{

    protected $model;
    protected $listResorse;
    protected $showResorse;
    protected $editResorse;
    protected $listRequest;
    protected $storeRequest;
    protected $updateRequest;
    protected $searchableColumns = [];
    protected $translatedColumns = [];
    protected $relations = [];
    protected $filters = [];
    protected $relationSearchableColumn = "";

    public function __construct(
        private IndexService $IndexService,
        private ShowService $ShowService,
        private EditService $EditService,
        private UpdateService $UpdateService,
        private StoreService $StoreService,
        private DestroyService $DestroyService,

    ) {

    }
    /**
     * @param Request $request
     * @return mixed
     */
    protected function index(Request $request): mixed
    {

        return $this->IndexService->__invoke(
            $request,
            $this->listRequest,
            $this->model,
            $this->searchableColumns,
            $this->relations,
            $this->relationSearchableColumn,
            $this->listResorse,
            $this->filters
        );
    }

    /**
     * @param Request $request
     * @return mixed
     */
    protected function store(Request $request): mixed
    {
        return $this->StoreService->__invoke(
            $request,
            $this->storeRequest,
            $this->model,
            $this->translatedColumns,
            $this->showResorse
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    protected function show($id): JsonResponse
    {
        return $this->ShowService->__invoke(
            $id,
            $this->showResorse,
            $this->model
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    protected function edit($id): JsonResponse
    {
        return $this->EditService->__invoke(
            $id,
            $this->editResorse,
            $this->model
        );
    }

    /**
     * @param Request $request
     * @param $id
     * @return mixed
     */
    protected function update(Request $request, $id): mixed
    {
        return $this->UpdateService->__invoke(
            $id,
            $request,
            $this->updateRequest,
            $this->model,
            $this->translatedColumns,
            $this->showResorse
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    protected function destroy($id): JsonResponse
    {
        return $this->DestroyService->__invoke(
            $id,
            $this->model
        );
    }
}
