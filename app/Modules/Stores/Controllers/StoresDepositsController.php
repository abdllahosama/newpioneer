<?php

namespace App\Modules\Stores\Controllers;

use App\Models\Company;
use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Stores\Models\Store;
use App\Modules\Stores\Models\Product;
use App\Modules\Stores\Models\StoresHistory;
use App\Modules\Stores\Models\ProductQuantity;

class StoresController extends Controller
{
    public function __construct()
    {
        $this->middleware('CheckSubscription:' . $this->site()->stores_allow);

        $this->middleware('CheckRule:stores_show')->only(['index']);
        $this->middleware('CheckRule:stores_add')->only(['store']);
        $this->middleware('CheckRule:stores_edit')->only(['update']);
        $this->middleware('CheckRule:stores_delete')->only(['destroy']);
    }

    public function store(Request $request)
    {
      $unsavedData = array();
      foreach (array_reverse($request->data) as $store) {
        if ($store['name'] != '' && $store['upload_key'] != ''){
          $checkStore = Store::where('upload_key', $store['upload_key'])->first();
          if(!$checkStore) {
            $store['company_id'] = $this->company();
            $store = Store::create($store);
            array_push($unsavedData, $store->upload_key);
          } else {
            array_push($unsavedData, $checkStore->upload_key);
          }
        } else {
          $safe['note'] = "رجاء  اختر اسم الخزينة";
          array_push($unsavedData, $store);
        }
      }

      return $unsavedData;
    }
    public function show($site, Store $store)
    {
        if ($store->company_id != $this->company()) {abort(404);}
        $store->productQuantities = ProductQuantity::where('store_id', $store->id)->orderBy('id', 'desc')->get();
        foreach ($store->productQuantities as $pq) {
            $pq->product = $pq->product;
        }

        $store->storesHistories = StoresHistory::where('store_id', $store->id)->orderBy('id', 'desc')->get();
        foreach ($store->storesHistories as $sh) {
            $sh->product = $sh->product;
            $sh->date = date('Y-m-d', $sh->date);
            if ($sh->invoice_id != 0) {
                $sh->invoice = $sh->invoice;
            }
            if ($sh->bill_id != 0) {
                $sh->bill = $sh->bill;
            }
            if ($sh->from_store_id != 0) {
                $sh->fromStore = $sh->fromStore;
            }

        }

        return $store;
    }
    public function quantitys ($site, $store, Request $request)
    {

        $orderBy   = $request->orderBy;
        $orderType = $request->orderType;
        $search    = $request->search;

        $products = Product::select('id', 'name','code', 'section_id', 'supplier_id', 'price', 'unit_id', 'barcode', 'description')->where('company_id', $this->company())->where('track_quantity', 1);

        //if search data
        if($search != '') {
            $products = $products->where('name', 'like', '%' . $search . '%')->orWhere('code', 'like', '%' . $search . '%')->where('company_id', $this->company())->where('track_quantity', 1)->orWhere('price', 'like', '%' . $search . '%')->where('company_id', $this->company())->where('track_quantity', 1)->orWhere('barcode', 'like', '%' . $search . '%')->where('company_id', $this->company())->where('track_quantity', 1);
        }

        //if order by
        if($orderBy != '') {
            $products = $products->orderBy($orderBy, $orderType);
        } else {
            $products = $products->orderBy('id', 'desc');
        }

        //paginaton
        $products = $products->get();

        foreach ($products as $key => $product) {
            $quantity = ProductQuantity::where('store_id', $store)->where('product_id', $product->id)->orderBy('id', 'desc')->first();

            if ($quantity) {
                $product->quantity = $quantity;
                $product->supplier = Supplier::find($product->supplier_id);
                if ($quantity->quantity == 0) {
                    unset($products[$key]);
                }
            } else {
                unset($products[$key]);
            }
        }
        return array_values($products->toArray());
    }
    public function update(Request $request, $site, Store $store)
    {
        if ($store->company_id != $this->company()) {abort(404);}
        $this->validate($request, [
            'name' => 'required'
        ]);
        $store->update($request->all());

    }
    public function destroy($site, Store $store)
    {
        if ($store->company_id != $this->company()) {abort(404);}
        ProductQuantity::where('store_id', $store->id)->delete();
        StoresHistory::where('store_id', $store->id)->delete();
        $store->delete();
    }
    public function move(Request $request)
    {
        $productQuantity = ProductQuantity::where('product_id', $request->product_id)->where('store_id', $request->store_id)->first();

        $company = Company::find($this->company());
        if (!$company->allow_minus_quantity) {
            if (!$productQuantity || $productQuantity->quantity < $request->quantity) {
                return 'quantity error';
            }
        }
        $productQuantity->quantity = $productQuantity->quantity - $request->quantity;
        $productQuantity->save();

        $toproductQuantity = ProductQuantity::where('product_id', $request->product_id)->where('store_id', $request->to_id)->first();
        if ($toproductQuantity){
            $toproductQuantity->quantity = $toproductQuantity->quantity + $request->quantity;
            $toproductQuantity->save();
        } else {
            $toproductQuantity = new ProductQuantity;
            $toproductQuantity->company_id = $this->company();
            $toproductQuantity->product_id = $request->product_id;
            $toproductQuantity->store_id   = $request->to_id;
            $toproductQuantity->quantity   = $request->quantity;
            $toproductQuantity->save();
        }

        $storesHistory                      = new StoresHistory;
        $storesHistory->store_id            = $request->store_id;
        $storesHistory->from_store_id       = $request->to_id;
        $storesHistory->product_id          = $request->product_id;
        $storesHistory->product_quantity_id = $productQuantity->id;
        $storesHistory->date                = time();
        $storesHistory->type                = 1;
        $storesHistory->quantity            = $request->quantity;
        $storesHistory->notes               = $request->notes;
        $storesHistory->save();

        $storesHistory                      = new StoresHistory;
        $storesHistory->store_id            = $request->to_id;
        $storesHistory->from_store_id       = $request->store_id;
        $storesHistory->product_id          = $request->product_id;
        $storesHistory->product_quantity_id = $toproductQuantity->id;
        $storesHistory->date                = time();
        $storesHistory->quantity            = $request->quantity;
        $storesHistory->notes               = $request->notes;
        $storesHistory->save();



    }
    public function deposit (Request $request)
    {
        $productQuantity = ProductQuantity::where('product_id', $request->product_id)->where('store_id', $request->store_id)->first();
        if ($productQuantity) {
            $productQuantity->quantity = $productQuantity->quantity + $request->quantity;
            $productQuantity->save();
        } else {
            $productQuantity = new ProductQuantity;
            $productQuantity->company_id = $this->company();
            $productQuantity->product_id = $request->product_id;
            $productQuantity->store_id   = $request->store_id;
            $productQuantity->quantity   = $request->quantity;
            $productQuantity->save();
        }
            $storesHistory                      = new StoresHistory;
            $storesHistory->store_id            = $request->store_id;
            $storesHistory->product_id          = $request->product_id;
            $storesHistory->product_quantity_id = $productQuantity->id;
            $storesHistory->date                = time();
            $storesHistory->quantity            = $request->quantity;
            $storesHistory->notes               = $request->notes;
            $storesHistory->save();

    }
    public function withdrawal (Request $request)
    {
        $productQuantity = ProductQuantity::where('product_id', $request->product_id)->where('store_id', $request->store_id)->first();

        $company = Company::find($this->company());
        if (!$company->allow_minus_quantity) {
            if (!$productQuantity || $productQuantity->quantity < $request->quantity) {
                return 'quantity error';
            }
        }
        if ($productQuantity) {
            $productQuantity->quantity = $productQuantity->quantity - $request->quantity;
            $productQuantity->save();

        } else {
            $productQuantity = new ProductQuantity;
            $productQuantity->company_id = $this->company();
            $productQuantity->product_id = $request->product_id;
            $productQuantity->store_id   = $request->store_id;
            $productQuantity->quantity   = - $request->quantity;
            $productQuantity->save();
        }

        $storesHistory                      = new StoresHistory;
        $storesHistory->store_id            = $request->store_id;
        $storesHistory->product_id          = $request->product_id;
        $storesHistory->product_quantity_id = $productQuantity->id;
        $storesHistory->date                = time();
        $storesHistory->quantity            = $request->quantity;
        $storesHistory->type                = 1;
        $storesHistory->notes               = $request->notes;
        $storesHistory->save();
    }
}
