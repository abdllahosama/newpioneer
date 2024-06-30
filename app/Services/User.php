<?php

namespace App\Services;

use App\Bll\ImageUploader;
use Illuminate\Support\Facades\Hash;


class User
{

    private $model;
    private $request = [];

    protected $data;
    protected $public_path;
    protected $resource;
    protected $error, $success, $saved = "Saved successfully";

    public function __construct($model)
    {
        $this->model = $model;
    }

    protected function Get($columns, $guard, $roles)
    {
        $limit = $this->request["limit"];
        $sort_by = $this->request["sort_by"];
        $sort_direction = $this->request["sort_direction"];
        $page = $this->request["page"];
        // check if limit is numeric if not set it to 10
        $limit = isset($limit) && (int)$limit > 0 ? (int)$limit : 10;
        // check if sort_by is valid if not set it to id
        $sort_by = isset($sort_by) && in_array($sort_by, $columns) ? $sort_by : $this->model->getTable() . '.id';
        // check if sort_direction is valid if not set it to desc
        $sort_direction = isset($sort_direction) && in_array($sort_direction, ['asc', 'desc']) ? $sort_direction : 'desc';
        // check if page is numeric if not set it to 1
        $page = isset($page) && is_int($page) ? $page : 1;

        //search

        $dataX = $this->model->orderBy($sort_by, $sort_direction);
//        if (isset($guard)) {
//            $dataX = $dataX->where('guard', $guard);
//        }
        if ($roles) {
            $dataX = $dataX->whereHas('roles');
        }
        foreach ($columns as $col) {

            if (isset($this->request[$col]) && $this->request[$col] != null) {
                $dataX = $dataX->where($col, 'like', '%' . $this->request[$col] . '%');
            }
        }


        $this->data = $dataX->paginate($limit, ['*'], 'page', $page);
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
        $imageUploader = new ImageUploader(public_path($this->public_path) . $id . '/');
        $uploaded = $imageUploader->upload($this->request->file($image_name));
        $image = $this->public_path . $id . '/' . $uploaded;

        return $image;
    }

    private function getMapped($columns)
    {
        $mapped = [];
        foreach ($columns as $col) {
            if (isset($this->request[$col])) {

                if ($col == 'password') {
                    $mapped[$col] = Hash::make($this->request[$col]);
                } else {
                    $mapped[$col] = $this->request[$col];
                }
            }
        }
        return $mapped;
    }

    protected function store($primary_columns, $image_name = null)
    {
        $created = $this->model->create($this->getMapped($primary_columns));

        if ($image_name != null) {
            $this->checkImage($created, $image_name);
        }

        $this->data = $this->resource::make($created);
        return $this->saved;
    }

    protected function update($primary_columns, int $id, $image_name = null)
    {
        $update = $this->model->where('id', $id)->first();
        $update->update($this->getMapped($primary_columns));
        if ($image_name != null) {
            $this->checkImage($update, $image_name);
        }

        $this->data = $this->resource::make($update);
        return $this->saved;
    }

    private function checkImage($entity, $image_name)
    {
        if(!isset($this->request->$image_name) && $this->request->$image_name == null){
            return;
        }
        if ($this->request->hasFile($image_name)) {
            $image = $this->saveImage($entity->id, $image_name);
            $entity[$image_name] = $image;
            $entity->save();
        } else {
            $iu = new ImageUploader(public_path($this->public_path) . $entity->id . '/');
            $res = $iu->moveUploadedFile($this->request->$image_name, public_path($this->public_path) . $entity->id . '/', true);
            if($res) {
                $entity[$image_name] = $this->public_path . $entity->id . '/' . $res;
                $entity->save();
            }
        }
    }

    protected function show(int $id)
    {
    }

    protected function delete(int $id)
    {
        $data = $this->model->where('id', $id)->first();
        if ($data != null && isset($data->image)) {
            $image_path = public_path($data->image);

            if (file_exists($image_path)) {
                unlink($image_path);
            }
            $data->delete();
        }
    }
}
