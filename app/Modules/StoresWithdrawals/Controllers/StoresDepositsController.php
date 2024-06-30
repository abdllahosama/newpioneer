<?php

namespace App\Modules\StoresDeposits\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StoresDeposit;
use App\Models\StoresDepositsItem;


use App\Models\InvoiceGroup;
use App\Models\Product;
use App\Models\ProductQuantity;
use App\Models\ProductMaterial;
use App\Models\StoresHistory;

use App\Models\ProductUnit;


use App\Models\Role;
use App\Models\Company;

class StoresDepositsController extends Controller
{
  public function __construct()
  {
      $this->middleware('CheckSubscription:' . $this->site()->invoices_allow);

      $this->middleware('CheckRule:stores_deposits_show')->only(['index']);
      $this->middleware('CheckRule:stores_deposits_add')->only(['store']);
      $this->middleware('CheckRule:stores_deposits_edit')->only(['update']);
      $this->middleware('CheckRule:stores_deposits_delete')->only(['destroy']);
  }

  public function index(Request $request)
  {
      $orderBy       = $request->orderBy;
      $orderType     = $request->orderType;
      $search        = $request->search;
      $status        = $request->status;
      $store_id      = $request->store_id;

      //return $request with data;
      $storesDeposits = StoresDeposit::select('id', 'date', 'code', 'description', 'store_id', 'status', 'file')->where('company_id', $this->company());

      $user = $this->user();
        if ($user->admin == 0 && $user->role && $user->role->stores_deposits_show_allow) {
            $storesDeposits = $storesDeposits->where('user_id', $user->id);
        }

      //if search data
      if($status != '') {$storesDeposits = $storesDeposits->where('status', $status);}

      if($search != '') {
        $storesDeposits = $storesDeposits->where(function ($query) use($search) {
        $query->where('code', 'like', '%' . $search . '%')
            ->orWhere('description', 'like', '%' . $search . '%')
            ->orWhereHas('store', function($query) use($search) {$query->where('stores.name', 'like', '%' . $search . '%');});
        });
      }

      //if order by
      if($orderBy != '') {
          $storesDeposits = $storesDeposits->orderBy($orderBy, $orderType);
      } else {
          $storesDeposits = $storesDeposits->orderBy('id', 'desc');
      }

      if($store_id != '') {
            $storesDeposits = $storesDeposits->where('store_id', $store_id);
        }

      //paginaton
      $storesDeposits = $storesDeposits->paginate(15);
      return $storesDeposits;
  }
  public function store(Request $request)
  {
    $unsavedData = array();
    foreach (array_reverse($request->data) as $mstoresDeposit) {
      if ($mstoresDeposit['upload_key'] != ''){
        $checkstoresDeposit = StoresDeposit::where('upload_key', $mstoresDeposit['upload_key'])->first();
        if(!$checkstoresDeposit) {
          $mstoresDeposit['company_id'] = $this->company();
          $mstoresDeposit['user_id']    = $this->user()->id;
          $company                = Company::find($this->company());


          $storesDepositGroup = InvoiceGroup::find($mstoresDeposit['invoice_group']);

          $user = $this->user();
          $role = Role::find($user->role_id);

          if ($user->admin == 0 && $role && !$role->invoices_edit_product) {
              $mstoresDeposit['date'] = time();
          } else {
              $mstoresDeposit['date'] = is_numeric($mstoresDeposit['date']) ? $mstoresDeposit['date'] : strtotime($mstoresDeposit['date']);
          }

          $mstoresDeposit['code']        = $storesDepositGroup->prefix . $storesDepositGroup->next_id;
          $storesDeposit = StoresDeposit::create($mstoresDeposit);

          $storesDepositGroup->next_id = $storesDepositGroup->next_id + 1;
          $storesDepositGroup->save();



          foreach ($mstoresDeposit['quotationItems'] as $qi) {
              if ($qi['quantity'] > 0 && $qi['product_name'] != '') {
                  $qi['count'] = isset($qi['count']) && $qi['count'] != '' && $qi['count'] > 0 ? $qi['count'] : 0;
                  $product = Product::find($qi['product_id']);
                  if (!$product && $qi['add_product']) {
                    $product = new Product;
                    $product->company_id = $this->company();
                    $product->name = $qi['product_name'];
                    $product->save();

                  }

                  $qi['user_id'] = $this->user()->id;
                  $qi['company_id'] = $this->company();

                  $qi['date']   = $mstoresDeposit['date'];
                  $qi['stores_deposit_id'] = $storesDeposit->id;
                  if ($product && $product->track_quantity == 1) {
                      $qi['track_quantity'] = 1;
                      $productQuantity = ProductQuantity::where('product_id', $product->id)->where('store_id', $storesDeposit->store_id)->first();
                      if (!$productQuantity) {
                        $productQuantity = new ProductQuantity;
                        $productQuantity->company_id = $this->company();
                        $productQuantity->product_id = $product->id;
                        $productQuantity->store_id = $storesDeposit->store_id;
                        $productQuantity->quantity = 0;
                        $productQuantity->save();
                      }

                      $productUnitQuantity = 1;
                      if (isset($qi['unit_id'])) {
                        $productUnit = ProductUnit::where('product_id', $product->id)->where('unit_id', $qi['unit_id'])->first();
                        if ($productUnit) {$productUnitQuantity =  $productUnit->quantity;}
                      }

                      $productQuantity->quantity = $productQuantity->quantity + ($qi['quantity'] * $productUnitQuantity);
                      if ($company->count_allow) {
                        $productQuantity->count = $productQuantity->count + $qi['count'];
                      } else {
                        $qi['count'] = 0;
                      }
                      $productQuantity->save();
                      $qi['product_quantity_id'] = $productQuantity->id;

                      $storesHistory = new StoresHistory;
                      $storesHistory->store_id = $productQuantity->store_id;
                      $storesHistory->product_id = $productQuantity->product_id;
                      $storesHistory->product_quantity_id = $productQuantity->id;

                      $storesHistory->date = $storesDeposit->date;
                      $storesHistory->stores_deposit_id = $storesDeposit->id;
                      $storesHistory->type = 0;
                      $storesHistory->quantity = $qi['quantity'];

                      $storesHistory->save();
                  }

                  $storesDepositsItem = StoresDepositsItem::create($qi);

              }
          }

          array_push($unsavedData, $storesDeposit->upload_key);
        } else {
            array_push($unsavedData, $checkstoresDeposit->upload_key);
        }
      } else {
        $mstoresDeposit['note'] = "من فضلك تحقق من المدخلات";
        array_push($unsavedData, $mstoresDeposit);
      }
    }

    return $unsavedData;
  }

  public function show($site, StoresDeposit $storesDeposit)
  {
      if ($storesDeposit->company_id != $this->company()) {abort(404);}
      $storesDeposit->created        = $storesDeposit->created_at->format('Y-m-d');
      $storesDeposit->updated        = $storesDeposit->updated_at->format('Y-m-d');
      $storesDeposit->quotationItems = StoresDepositsItem::where('stores_deposit_id', $storesDeposit->id)->get();

      $storesDeposit->store     = $storesDeposit->store;
      $storesDeposit->user      = $storesDeposit->user;
      $storesDeposit->company   = $storesDeposit->company;


      $storesHistories = StoresHistory::where('stores_deposit_id', $storesDeposit->id)->get();
      foreach ($storesHistories as $storesHistore) {
          $storesHistore->product = $storesHistore->product;
          $storesHistore->store = $storesHistore->store;
      }

      foreach ($storesDeposit->quotationItems as $quotationItem) {
          $quotationItem->unit  = $quotationItem->unit;
      }
      $storesDeposit->stores_histories = $storesHistories;


      return $storesDeposit;
  }
  public function update(Request $request, $site, StoresDeposit $storesDeposit)
  {
      if ($storesDeposit->company_id != $this->company()) {abort(404);}
      $data = $request->except(['company_id', 'user_id']);
      $storesDepositsItems = StoresDepositsItem::where('stores_deposit_id', $storesDeposit->id)->get();


      $company = Company::find($this->company());
      $data['date'] = strtotime($data['date']);
      $storesDeposit->update($data);


      foreach ($storesDepositsItems as $storesDepositsItem) {
        $product = Product::find($storesDepositsItem->product_id);
          if ($product && $product->track_quantity == 1) {
              $productQuantity = ProductQuantity::find($storesDepositsItem->product_quantity_id);
              if ($productQuantity) {
                $productUnitQuantity = 1;
                $productUnit = ProductUnit::where('product_id', $storesDepositsItem->product_id)->where('unit_id', $storesDepositsItem->unit_id)->first();
                if ($productUnit) {$productUnitQuantity =  $productUnit->quantity;}

                  $productQuantity->quantity = $productQuantity->quantity - ($storesDepositsItem->quantity * $productUnitQuantity);
                  $productQuantity->count = $productQuantity->count - $storesDepositsItem->count;
                  $productQuantity->save();
              }

          }
      }




      $storesDepositsItems = StoresDepositsItem::where('stores_deposit_id', $storesDeposit->id)->delete();
      $storesHistory = StoresHistory::where('stores_deposit_id', $storesDeposit->id)->delete();


      foreach ($data['quotationItems'] as $qi) {
          if ($qi['quantity'] > 0 && $qi['product_name'] != '') {
              $qi['count'] = isset($qi['count']) && $qi['count'] != '' && $qi['count'] > 0 ? $qi['count'] : 0;
              $product = Product::find($qi['product_id']);
              $qi['user_id'] = $this->user()->id;
              $qi['company_id'] = $this->company();

              $qi['date']   = $data['date'];

              $qi['stores_deposit_id'] = $storesDeposit->id;
              if ($product && $product->track_quantity == 1) {
                  $qi['track_quantity'] = 1;
                  $productQuantity = ProductQuantity::where('product_id', $product->id)->where('store_id', $data['store_id'])->first();
                  if (!$productQuantity) {
                    $productQuantity = new ProductQuantity;
                    $productQuantity->company_id = $this->company();
                    $productQuantity->product_id = $product->id;
                    $productQuantity->store_id = $data['store_id'];
                    $productQuantity->quantity = 0;
                    $productQuantity->save();
                  }

                  $productUnitQuantity = 1;
                  if (isset($qi['unit_id'])) {
                    $productUnit = ProductUnit::where('product_id', $product->id)->where('unit_id', $qi['unit_id'])->first();
                    if ($productUnit) {$productUnitQuantity =  $productUnit->quantity;}
                  }

                  $productQuantity->quantity = $productQuantity->quantity + ($qi['quantity'] * $productUnitQuantity);
                  if ($company->count_allow) {
                    $productQuantity->count = $productQuantity->count + $qi['count'];
                  } else {
                    $qi['count'] = 0;
                  }
                  $productQuantity->save();
                  $qi['product_quantity_id'] = $productQuantity->id;

                  $storesHistory = new StoresHistory;
                  $storesHistory->store_id = $productQuantity->store_id;
                  $storesHistory->product_id = $productQuantity->product_id;
                  $storesHistory->product_quantity_id = $productQuantity->id;

                  $storesHistory->date = $storesDeposit->date;
                  $storesHistory->stores_deposit_id = $storesDeposit->id;
                  $storesHistory->type = 0;
                  $storesHistory->quantity = $qi['quantity'];

                  $storesHistory->save();
              }
              $storesDepositsItem = StoresDepositsItem::create($qi);
          }
      }

  }
  public function destroy($site, StoresDeposit $storesDeposit)
  {
      if ($storesDeposit->company_id != $this->company()) {abort(404);}
      $storesDepositsItems = StoresDepositsItem::where('stores_deposit_id', $storesDeposit->id)->get();
      foreach ($storesDepositsItems as $storesDepositsItem) {
        $product = Product::find($storesDepositsItem->product_id);
          if ($product && $product->track_quantity == 1) {
              $productQuantity = ProductQuantity::find($storesDepositsItem->product_quantity_id);
              if ($productQuantity) {

                $productUnitQuantity = 1;
                $productUnit = ProductUnit::where('product_id', $storesDepositsItem->product_id)->where('unit_id', $storesDepositsItem->unit_id)->first();
                if ($productUnit) {$productUnitQuantity =  $productUnit->quantity;}

                  $productQuantity->quantity = $productQuantity->quantity - ($storesDepositsItem->quantity * $productUnitQuantity);
                  $productQuantity->count    = $productQuantity->count - $storesDepositsItem->count;
                  $productQuantity->save();
              }

          }
      }

      $storesDepositsItems = StoresDepositsItem::where('stores_deposit_id', $storesDeposit->id)->delete();
      $storesHistory = StoresHistory::where('stores_deposit_id', $storesDeposit->id)->delete();
      $storesDeposit->delete();
  }
}
