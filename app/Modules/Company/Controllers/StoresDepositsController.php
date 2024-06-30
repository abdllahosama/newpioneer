<?php

namespace App\Modules\Company\Controllers;

use Illuminate\Http\Request;
use App\Exports\SuppliersExport;



use App\Imports\SuppliersImport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Modules\Company\Models\Company;
use App\Modules\Suppliers\Models\Supplier;

class SuppliersController extends Controller
{
    public function __construct()
    {
        $this->middleware('CheckSubscription:' . $this->site()->suppliers_allow);

        $this->middleware('CheckRule:suppliers_show')->only(['index']);
        $this->middleware('CheckRule:suppliers_add')->only(['store']);
        $this->middleware('CheckRule:suppliers_edit')->only(['update']);
        $this->middleware('CheckRule:suppliers_delete')->only(['destroy']);
    }

    public function store(Request $request)
    {
      $count = Supplier::where('company_id', $this->company())->count();
      if($this->site()->suppliers_count == !0 && $this->site()->suppliers_count <= $count) {return $request->data;}

      $company      = Company::find($this->company());

      $unsavedData = array();
      foreach (array_reverse($request->data) as $supplier) {
        if ($supplier['name'] != '' && $supplier['upload_key'] != ''){
          $checkSupplier = Supplier::where('upload_key', $supplier['upload_key'])->first();
          if(!$checkSupplier) {
            $supplier['company_id'] = $this->company();

            if (isset($supplier['balance_type']) && $supplier['balance_type'] == 1) {
                $supplier['creditor'] = isset($supplier['creditor']) && $supplier['creditor'] != '' ? $supplier['creditor'] : 0;
                $supplier['debit']    = isset($supplier['debit']) && $supplier['debit'] != '' ? $supplier['debit'] : 0;
                $supplier['balance']  = $supplier['creditor'] - $supplier['debit'] ;
            } else {
                $supplier['creditor'] = 0;
                $supplier['debit']    = 0;
                $supplier['balance'] = isset($supplier['balance']) && $supplier['balance'] != '' ? $supplier['balance'] : 0;
                if ($company->balance_type == 1) {
                    $supplier['balance'] = $supplier['balance'] * -1;
                }
                if ($supplier['balance'] > 0) {
                    $supplier['creditor'] = $supplier['balance'];
                }
                if ($supplier['balance'] < 0) {
                    $supplier['debit'] = $supplier['balance'] * -1;
                }
            }

            $supplier['opening_balance'] = $supplier['balance'];
            $supplier = Supplier::create($supplier);
            array_push($unsavedData, $supplier->upload_key);
          } else {
            array_push($unsavedData, $checkSupplier->upload_key);
          }
        } else {
          $supplier['note'] = "رجاء  اختر اسم المستخدم";
          array_push($unsavedData, $supplier);
        }
      }

      return $unsavedData;
    }
    public function show($site, Supplier $supplier)
    {
        if ($supplier->company_id != $this->company()) {abort(404);}
        //get suppliers Counts
        $supplier->bills_count          = $supplier->billsCount();
        $supplier->purchase_order_count = $supplier->purchaseOrderCount();

        return $supplier;
    }
    public function update(Request $request, $site, Supplier $supplier)
    {
        if ($supplier->company_id != $this->company()) {abort(404);}
        //validate data
        $this->validate($request, [
            'name' => 'required'
        ]);

        //validate tax number
        $checkTaxNumber = Supplier::where('company_id', $this->company())->where('tax_number', $request->tax_number)->where('id', '!=', $supplier->id)->count();
        if ($checkTaxNumber && $request->tax_number != '') {return 'repeated';}

        //request data
        $data= $request->except(['company_id', 'balance', 'opening_balance', 'balance_type', 'debit', 'creditor']);

        $supplier->update($data);

    }
    public function destroy($site, Supplier $supplier)
    {
        if ($supplier->company_id != $this->company()) {abort(404);}
        $bills = Bill::where('supplier_id', $supplier->id)->get();
        foreach ($bills as $bill) {
            $billItems = BillItem::where('bill_id', $bill->id)->get();
            foreach ($billItems as $billItem) {
                if ($billItem->track_quantity == 1) {
                    $productQuantity = ProductQuantity::find($billItem->product_quantity_id);
                    if ($productQuantity) {
                        $productQuantity->quantity = $productQuantity->quantity - $billItem->quantity;
                        $productQuantity->save();
                    }
                }
            }

            $purchasePayments = PurchasePayment::where('bill_id', $bill->id)->get();
            foreach ($purchasePayments as $purchasePayment) {
                $safesHistory = SafesHistory::where('purchase_payment_id', $purchasePayment->id)->first();
                if ($safesHistory) {
                    $safe = Safe::find($safesHistory->safe_id);
                    if($safe) {
                        $safe->balance = $safe->balance - $safesHistory->balance;
                        $safe->save();
                    }
                    $safesHistory->delete();
                }
            }

            $purchasePayments = PurchasePayment::where('bill_id', $bill->id)->delete();
            $billItems = BillItem::where('bill_id', $bill->id)->delete();
            $storesHistory = StoresHistory::where('bill_id', $bill->id)->delete();
            $bill->delete();
        }
        $purchaseorders = PurchaseOrder::where('supplier_id', $supplier->id)->get();
        foreach ($purchaseorders as $purchaseorder) {
            $orderItems = PurchaseOrderItem::where('purchase_order_id', $purchaseorder->id)->delete();
            $purchaseorder->delete();
        }

        $purchasePayments = PurchasePayment::where('supplier_id', $supplier->id)->get();
        foreach ($purchasePayments as $purchasePayment) {
            $safesHistory = SafesHistory::where('purchase_payment_id', $purchasePayment->id)->first();
            if ($safesHistory) {
                $safe = Safe::find($safesHistory->safe_id);
                if($safe) {
                    $safe->balance = $safe->balance - $safesHistory->balance;
                    $safe->save();
                }
                $safesHistory->delete();
            }
        }
        $purchasePayments = PurchasePayment::where('supplier_id', $supplier->id)->delete();


        $supplier->delete();
    }
    public function import(Request $request)
    {
        Excel::import(new SuppliersImport, $request->file('file')->store('temp'));
        return back();
    }
    public function export()
    {
        return Excel::download(new SuppliersExport, 'Suppliers.xlsx');
    }
}
