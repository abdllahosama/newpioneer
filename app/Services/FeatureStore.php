<?php

namespace App\Services;

use App\Bll\ImageUploader;
use App\Bll\Utility;


class FeatureStore
{

    private $model;
    private $model_option;
    private $model_image;
    private $request = [];

    protected $data;
    protected $public_path;
    protected $resource;
    protected $error, $success, $saved = "Saved successfully";

    public function __construct($model)
    {
        $this->model = $model;
    }

    protected function Get($columns, $data)
    {
        $limit = $this->request["limit"];
        $sort_by = $this->request["sort_by"];
        $sort_direction = $this->request["sort_direction"];
        $page = $this->request["page"];
        // check if limit is numeric if not set it to 10
        $limit = isset($limit) && (int)$limit > 0 ? (int)$limit : 10;
        // check if sort_by is valid if not set it to id
        $sort_by = isset($sort_by) && in_array($sort_by, $columns) ? $sort_by : $this->model->getQuery()->from . '.id';
        // check if sort_direction is valid if not set it to desc
        $sort_direction = isset($sort_direction) && in_array($sort_direction, ['asc', 'desc']) ? $sort_direction : 'desc';
        // check if page is numeric if not set it to 1
        $page = isset($page) && is_int($page) ? $page : 1;

        if($this->request->query('feature_id')):
            $model = $this->model->where('feature_id',$this->request->query('feature_id'));
        else:
            $model = $this->model;
        endif;
        //search
        if ($data) {
            $data = $model->whereHas('Data')->orderBy($sort_by, $sort_direction);
            foreach ($columns as $col) {

                if (isset($this->request[$col]) && $this->request[$col] != null) {
                    $data = $data->whereHas('Data', function ($query) use ($col) {
                        $query->where($col, 'like', '%' . $this->request[$col] . '%');
                    });
                }
            }
        } else {
            $data = $model->orderBy($sort_by, $sort_direction);
            foreach ($columns as $col) {

                if (isset($this->request[$col]) && $this->request[$col] != null) {
                    $data = $data->where($col, 'like', '%' . $this->request[$col] . '%');
                }
            }
        }

        // get brands by pagination
        $this->data = $data->paginate($limit, ['*'], 'page', $page);
        if ($this->data->count() == 0) {
            return ($this->error);
        }
        $this->data = $this->resource::collection($this->data)->response()->getData();
        return $this->success;
    }

    public function GetData()
    {
        return $this->data;
    }

    public function setRequest($request)
    {
        $this->request = $request;
    }

    protected function saveImage($id, $image_name)
    {
        $imageUploader = new ImageUploader(public_path($this->public_path) . '/');
        $uploaded = $imageUploader->upload($image_name);
        $image = $this->public_path . $uploaded;

        return $image;
    }

    private function getMapped($columns)
    {
        $mapped = [];
        foreach ($columns as $col) {
            $mapped[$col] = $this->request[$col];
        }
        return $mapped;
    }

    private function getMappedOption($columns, $optionData)
    {
        $mapped = [];

        foreach ($columns as $col) {
            if (isset($optionData[$col])) {
                $mapped[$col] = $optionData[$col];
            }
        }

        return $mapped;
    }


    protected function store($primary_columns, $data_columns, $foreign_key, $image_name = null, $option = null)
    {
        $created = $this->model->create($this->getMapped($primary_columns));

        if ($data_columns != []) {

            if ($option && is_array($this->request[$option])) {
                foreach ($this->request[$option] as $optionData) {
                    // Map and insert the data for each option
                    $data = $this->getMappedOption($data_columns, $optionData);
                    $data[$foreign_key] = $created->id;
                    $created->Data()->create($data);
                }
            } else {
                $data = $this->getMapped($data_columns);

                $data[$foreign_key] = $created->id;

                $created->Data()->create($data);
            }
        }

        if ($image_name != null) {
            $image = $this->saveImage(null, $this->request[$image_name]);
            $entity[$image_name] = $image;
            $entity[$foreign_key] = $created->id;
            $created->image()->create($entity);
        }

        $this->data = $this->resource::make($created);
        return $this->saved;
    }

    protected function update($primary_columns, $data_columns, $foreign_key, int $id, $image_name = null, $option = null)
    {

        $updateFeature = $this->model->where('id', $id)->first();


        $updated = $updateFeature->update($this->getMapped($primary_columns));

        if ($data_columns != []) {
            $data = $this->getMapped($data_columns);
            $data[$foreign_key] = $updated->id;

            $updated->Data()->create($data);
        }

        if ($image_name != null) {
            $image = $this->saveImage(null, $this->request[$image_name]);
            $entity[$image_name] = $image;
            $entity[$foreign_key] = $updated->id;
            $updated->image()->update($entity);
        }

        $this->data = $this->resource::make($updateFeature);
        return $this->saved;
    }

//    private function checkImage($entity, $image_name)
//    {
//        if ($this->request->hasFile($image_name)) {
//            $image = $this->saveImage(null, $image_name);
//            $entity[$image_name] = $image;
//            $this->model_image->save($entity);
//
//        }
//    }

    protected function show(int $id)
    {
    }

    protected function delete(int $id)
    {
        $data = $this->model->where('id', $id)->first();
        if ($data != null) {
            $data->delete();
        }

    }
}
