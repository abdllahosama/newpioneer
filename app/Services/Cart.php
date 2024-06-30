<?php

namespace App\Services;

use App\Bll\ImageUploader;
use App\Bll\Utility;


class Cart
{

    private $model;
    private $request = [];

    protected $data;
    protected $resource;
    protected $error, $success,$delete,$cart_imit, $saved = "Saved successfully";
    public function __construct($model)
    {
        $this->model = $model;
    }


    protected function Get($columns)
    {

        $data = $this->getMapped($columns);

        $this->data = $this->model->where('token',$data['token'])->get();

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

    private function getMapped($columns)
    {
        $mapped = [];
        foreach ($columns as $col) {
            $mapped[$col] = $this->request[$col];
        }
        return $mapped;
    }


    protected function store($primary_columns)
    {
        $data = $this->getMapped($primary_columns);

        $this->checkLimit($data['token']);

        $cart_item = null;
        if (isset($data['token']) && $data['token'] != null){
             $cart_item = $this->model->where('token',$data['token'])->where('item_id', $data['item_id'])->first();
        }

        if ($cart_item != NULL){
            $cart_item->update([
                'qty' => $cart_item->qty + $data['qty']
            ]);
            $this->data = $this->resource::make($cart_item);
        }else{
            $data['custom_fields'] = json_encode($data['custom_fields']);
            $data['token'] = md5(rand());
            $created = $this->model->create($data);
            $this->data = $this->resource::make($created);
        }

        return $this->saved;
    }

    protected function update($primary_columns,int $id)
    {
        $data = $this->getMapped($primary_columns);

        $cart_item = $this->model->where('token',$data['token'])->where('id', $id)->where('item_id',$data['item_id'])->first();

        if ($data['qty'] < 1){
            $cart_item->delete();
            return $this->delete;
        }

        if ($cart_item == null){
            return $this->error;
        }else{
            $cart_item->update([
                'qty' => $cart_item->qty + $data['qty']
            ]);
            $this->data = $this->resource::make($cart_item);
        }

        return $this->saved;
    }

    protected function delete($primary_columns , int $id)
    {
        $request = $this->getMapped($primary_columns);

        $data = $this->model->where('token',$request['token'])->where('id', $id)->first();
        if ($data != null) {
            $data->delete();
        }
    }


    protected function checkLimit($token)
    {
        $cartItem = $this->model->where('token',$token)->get();

        if ($cartItem > config('constants.CART_LIMIT')){
            return $this->cart_imit;
        }

    }

}
