<?php

namespace App\Modules\StoresTransfers\Controllers;

use Illuminate\Http\Request;
use App\Modules\Roles\Models\Role;
use App\Http\Controllers\Controller;
use App\Modules\Stores\Models\Store;
use App\Modules\Stores\Models\Product;
use App\Modules\Company\Models\Company;
use App\Modules\Stores\Models\StoresHistory;
use App\Modules\Stores\Models\ProductQuantity;
use App\Modules\StoresTransfers\Models\StoresTransfer;
use App\Modules\StoresTransfers\Models\StoresTransfersItem;




class StoresTransfersController extends Controller
{
  public function __construct()
  {
      $this->middleware('CheckSubscription:' . $this->site()->invoices_allow);

      $this->middleware('CheckRule:stores_transfers_show')->only(['index']);
      $this->middleware('CheckRule:stores_transfers_add')->only(['store']);
      $this->middleware('CheckRule:stores_transfers_edit')->only(['update']);
      $this->middleware('CheckRule:stores_transfers_delete')->only(['destroy']);
  }

  public function index(Request $request)
  {
      $orderBy       = $request->orderBy;
      $orderType     = $request->orderType;
      $search        = $request->search;
      $status        = $request->status;
      $from_store_id = $request->from_store_id;
      $to_store_id   = $request->to_store_id;

      //return $request with data;
      $storesTransfers = StoresTransfer::select('id', 'date', 'code', 'description', 'from_store_id', 'to_store_id', 'status', 'file')->where('company_id', $this->company());

      $user = $this->user();
      if ($user->admin == 0 && $user->role && $user->role->stores_transfers_show_allow) {
          $storesTransfers = $storesTransfers->where('user_id', $user->id);
      }

      //if search data
      if($status != '') {$storesTransfers = $storesTransfers->where('status', $status);}

      if($search != '') {
        $storesTransfers = $storesTransfers->where(function ($query) use($search) {
        $query->where('code', 'like', '%' . $search . '%')
            ->orWhere('description', 'like', '%' . $search . '%')
            ->orWhereHas('fromStore', function($query) use($search) {$query->where('stores.name', 'like', '%' . $search . '%');})
            ->orWhereHas('toStore', function($query) use($search) {$query->where('stores.name', 'like', '%' . $search . '%');});
        });
      }

      //if order by
      if($orderBy != '') {
          $storesTransfers = $storesTransfers->orderBy($orderBy, $orderType);
      } else {
          $storesTransfers = $storesTransfers->orderBy('id', 'desc');
      }

      if($from_store_id != '') {
          $storesTransfers = $storesTransfers->where('from_store_id', $from_store_id);
      }

      if($to_store_id != '') {
          $storesTransfers = $storesTransfers->where('to_store_id', $to_store_id);
      }

      //paginaton
      $storesTransfers = $storesTransfers->paginate(15);
      return $storesTransfers;
  }
  public function store(Request $request)
  {
    $unsavedData = array();
    foreach (array_reverse($request->data) as $mstoresTransfer) {
      if ($mstoresTransfer['upload_key'] != ''){
        $checkstoresTransfer = StoresTransfer::where('upload_key', $mstoresTransfer['upload_key'])->first();
        if(!$checkstoresTransfer) {
          $mstoresTransfer['company_id'] = $this->company();
          $mstoresTransfer['user_id']    = $this->user()->id;
          $company                = Company::find($this->company());


          $storesTransferGroup = InvoiceGroup::find($mstoresTransfer['invoice_group']);

          $user = $this->user();
          $role = Role::find($user->role_id);

          if ($user->admin == 0 && $role && !$role->invoices_edit_product) {
              $mstoresTransfer['date'] = time();
          } else {
              $mstoresTransfer['date'] = is_numeric($mstoresTransfer['date']) ? $mstoresTransfer['date'] : strtotime($mstoresTransfer['date']);
          }

          $mstoresTransfer['code']        = $storesTransferGroup->prefix . $storesTransferGroup->next_id;
          $storesTransfer = StoresTransfer::create($mstoresTransfer);

          $storesTransferGroup->next_id = $storesTransferGroup->next_id + 1;
          $storesTransferGroup->save();



          foreach ($mstoresTransfer['quotationItems'] as $qi) {
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

                  $qi['date']   = $mstoresTransfer['date'];
                  $qi['stores_transfer_id'] = $storesTransfer->id;
                  if ($product && $product->track_quantity == 1) {
                      $qi['track_quantity'] = 1;
                      $productQuantity = ProductQuantity::where('product_id', $product->id)->where('store_id', $storesTransfer->from_store_id)->first();
                      if (!$productQuantity) {
                        $productQuantity = new ProductQuantity;
                        $productQuantity->company_id = $this->company();
                        $productQuantity->product_id = $product->id;
                        $productQuantity->store_id = $storesTransfer->from_store_id;
                        $productQuantity->quantity = 0;
                        $productQuantity->save();
                      }

                      $productUnitQuantity = 1;
                      if (isset($qi['unit_id'])) {
                        $productUnit = ProductUnit::where('product_id', $product->id)->where('unit_id', $qi['unit_id'])->first();
                        if ($productUnit) {$productUnitQuantity =  $productUnit->quantity;}
                      }

                      $productQuantity->quantity = $productQuantity->quantity - ($qi['quantity'] * $productUnitQuantity);
                      if ($company->count_allow) {
                        $productQuantity->count = $productQuantity->count - $qi['count'];
                      } else {
                        $qi['count'] = 0;
                      }
                      $productQuantity->save();
                      $qi['product_quantity_id'] = $productQuantity->id;

                      $storesHistory = new StoresHistory;
                      $storesHistory->store_id = $productQuantity->store_id;
                      $storesHistory->product_id = $productQuantity->product_id;
                      $storesHistory->product_quantity_id = $productQuantity->id;

                      $storesHistory->date = $storesTransfer->date;
                      $storesHistory->stores_transfer_id = $storesTransfer->id;
                      $storesHistory->type = 1;
                      $storesHistory->quantity = $qi['quantity'];

                      $storesHistory->save();

                      $productQuantity = ProductQuantity::where('product_id', $product->id)->where('store_id', $storesTransfer->to_store_id)->first();
                      if (!$productQuantity) {
                        $productQuantity = new ProductQuantity;
                        $productQuantity->company_id = $this->company();
                        $productQuantity->product_id = $product->id;
                        $productQuantity->store_id = $storesTransfer->to_store_id;
                        $productQuantity->quantity = 0;
                        $productQuantity->save();
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

                      $storesHistory->date = $storesTransfer->date;
                      $storesHistory->stores_transfer_id = $storesTransfer->id;
                      $storesHistory->type = 0;
                      $storesHistory->quantity = $qi['quantity'];

                      $storesHistory->save();


                  }

                  $storesTransfersItem = StoresTransfersItem::create($qi);

              }
          }

          array_push($unsavedData, $storesTransfer->upload_key);
        } else {
            array_push($unsavedData, $checkstoresTransfer->upload_key);
        }
      } else {
        $mstoresTransfer['note'] = "من فضلك تحقق من المدخلات";
        array_push($unsavedData, $mstoresTransfer);
      }
    }

    return $unsavedData;
  }

  public function show($site, StoresTransfer $storesTransfer)
  {
      if ($storesTransfer->company_id != $this->company()) {abort(404);}
      $storesTransfer->created        = $storesTransfer->created_at->format('Y-m-d');
      $storesTransfer->updated        = $storesTransfer->updated_at->format('Y-m-d');
      $storesTransfer->quotationItems = StoresTransfersItem::where('stores_transfer_id', $storesTransfer->id)->get();

      $fromStore = Store::find($storesTransfer->from_store_id);
      $storesTransfer->fromStore = $fromStore;

      $toStore = Store::find($storesTransfer->to_store_id);
      $storesTransfer->toStore = $toStore;

      $storesTransfer->user      = $storesTransfer->user;
      $storesTransfer->company   = $storesTransfer->company;


      $storesHistories = StoresHistory::where('stores_transfer_id', $storesTransfer->id)->get();
      foreach ($storesHistories as $storesHistore) {
          $storesHistore->product = $storesHistore->product;
          $storesHistore->store = $storesHistore->store;
      }
      $storesTransfer->stores_histories = $storesHistories;

      foreach ($storesTransfer->quotationItems as $quotationItem) {
          $quotationItem->unit  = $quotationItem->unit;
      }
      return $storesTransfer;
  }
  public function update(Request $request, $site, StoresTransfer $storesTransfer)
  {
      if ($storesTransfer->company_id != $this->company()) {abort(404);}
      $data = $request->except(['company_id', 'user_id', 'from_store_id', 'to_store_id']);
      $storesTransfersItems = StoresTransfersItem::where('stores_transfer_id', $storesTransfer->id)->get();


      $company = Company::find($this->company());
      $data['date'] = strtotime($data['date']);
      $storesTransfer->update($data);


      foreach ($storesTransfersItems as $storesTransfersItem) {
        $product = Product::find($storesTransfersItem->product_id);
          if ($product && $product->track_quantity == 1) {
            $productQuantities = ProductQuantity::where('product_id', $product->id)->whereIn('store_id', array($storesTransfer->from_store_id, $storesTransfer->to_store_id))->get();
            foreach ($productQuantities as $productQuantity) {
              $storesHistory = StoresHistory::where('product_quantity_id', $productQuantity->id)->where('stores_transfer_id', $storesTransfer->id)->first();
                if ($storesHistory && $productQuantity) {
                  $productUnitQuantity = 1;
                  $productUnit = ProductUnit::where('product_id', $storesTransfersItem->product_id)->where('unit_id', $storesTransfersItem->unit_id)->first();
                  if ($productUnit) {$productUnitQuantity =  $productUnit->quantity;}

                  if($storesHistory->type == 1) {
                    $productQuantity->quantity = $productQuantity->quantity + ($storesTransfersItem->quantity * $productUnitQuantity);
                    $productQuantity->count = $productQuantity->count + $storesTransfersItem->count;
                    $productQuantity->save();
                  } elseif ($storesHistory->type == 0) {
                    $productQuantity->quantity = $productQuantity->quantity - ($storesTransfersItem->quantity * $productUnitQuantity);
                    $productQuantity->count = $productQuantity->count - $storesTransfersItem->count;
                    $productQuantity->save();
                  }
              }
            }
          }
      }

      $storesTransfersItems = StoresTransfersItem::where('stores_transfer_id', $storesTransfer->id)->delete();
      $storesHistory = StoresHistory::where('stores_transfer_id', $storesTransfer->id)->delete();


      foreach ($data['quotationItems'] as $qi) {
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

              $qi['date']   = $data['date'];
              $qi['stores_transfer_id'] = $storesTransfer->id;
              if ($product && $product->track_quantity == 1) {
                  $qi['track_quantity'] = 1;
                  $productQuantity = ProductQuantity::where('product_id', $product->id)->where('store_id', $storesTransfer->from_store_id)->first();
                  if (!$productQuantity) {
                    $productQuantity = new ProductQuantity;
                    $productQuantity->company_id = $this->company();
                    $productQuantity->product_id = $product->id;
                    $productQuantity->store_id = $storesTransfer->from_store_id;
                    $productQuantity->quantity = 0;
                    $productQuantity->save();
                  }

                  $productUnitQuantity = 1;
                  if (isset($qi['unit_id'])) {
                    $productUnit = ProductUnit::where('product_id', $product->id)->where('unit_id', $qi['unit_id'])->first();
                    if ($productUnit) {$productUnitQuantity =  $productUnit->quantity;}
                  }

                  $productQuantity->quantity = $productQuantity->quantity - ($qi['quantity'] * $productUnitQuantity);
                  if ($company->count_allow) {
                    $productQuantity->count = $productQuantity->count - $qi['count'];
                  } else {
                    $qi['count'] = 0;
                  }
                  $productQuantity->save();
                  $qi['product_quantity_id'] = $productQuantity->id;

                  $storesHistory = new StoresHistory;
                  $storesHistory->store_id = $productQuantity->store_id;
                  $storesHistory->product_id = $productQuantity->product_id;
                  $storesHistory->product_quantity_id = $productQuantity->id;

                  $storesHistory->date = $storesTransfer->date;
                  $storesHistory->stores_transfer_id = $storesTransfer->id;
                  $storesHistory->type = 1;
                  $storesHistory->quantity = $qi['quantity'];

                  $storesHistory->save();

                  $productQuantity = ProductQuantity::where('product_id', $product->id)->where('store_id', $storesTransfer->to_store_id)->first();
                  if (!$productQuantity) {
                    $productQuantity = new ProductQuantity;
                    $productQuantity->company_id = $this->company();
                    $productQuantity->product_id = $product->id;
                    $productQuantity->store_id = $storesTransfer->to_store_id;
                    $productQuantity->quantity = 0;
                    $productQuantity->save();
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

                  $storesHistory->date = $storesTransfer->date;
                  $storesHistory->stores_transfer_id = $storesTransfer->id;
                  $storesHistory->type = 0;
                  $storesHistory->quantity = $qi['quantity'];

                  $storesHistory->save();
              }

              $storesTransfersItem = StoresTransfersItem::create($qi);

          }
      }

  }
  public function destroy($site, StoresTransfer $storesTransfer)
  {
      if ($storesTransfer->company_id != $this->company()) {abort(404);}
      $storesTransfersItems = StoresTransfersItem::where('stores_transfer_id', $storesTransfer->id)->get();

      foreach ($storesTransfersItems as $storesTransfersItem) {
        $product = Product::find($storesTransfersItem->product_id);
          if ($product && $product->track_quantity == 1) {
            $productQuantities = ProductQuantity::where('product_id', $product->id)->whereIn('store_id', array($storesTransfer->from_store_id, $storesTransfer->to_store_id))->get();
            foreach ($productQuantities as $productQuantity) {
              $storesHistory = StoresHistory::where('product_quantity_id', $productQuantity->id)->where('stores_transfer_id', $storesTransfer->id)->first();
                if ($storesHistory && $productQuantity) {

                  $productUnitQuantity = 1;
                  $productUnit = ProductUnit::where('product_id', $storesTransfersItem->product_id)->where('unit_id', $storesTransfersItem->unit_id)->first();
                  if ($productUnit) {$productUnitQuantity =  $productUnit->quantity;}

                  if($storesHistory->type == 1) {
                    $productQuantity->quantity = $productQuantity->quantity + ($storesTransfersItem->quantity * $productUnitQuantity);
                    $productQuantity->count    = $productQuantity->count + $storesTransfersItem->count;
                    $productQuantity->save();
                  } elseif ($storesHistory->type == 0) {
                    $productQuantity->quantity = $productQuantity->quantity - ($storesTransfersItem->quantity * $productUnitQuantity);
                    $productQuantity->count    = $productQuantity->count - $storesTransfersItem->count;
                    $productQuantity->save();
                  }
              }
            }
          }
      }

      $storesTransfersItems = StoresTransfersItem::where('stores_transfer_id', $storesTransfer->id)->delete();
      $storesHistory = StoresHistory::where('stores_transfer_id', $storesTransfer->id)->delete();
      $storesTransfer->delete();
  }
}
