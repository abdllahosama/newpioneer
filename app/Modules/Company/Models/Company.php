<?php

namespace App\Modules\Company\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = ['site_id', 'image', 'name', 'tax_number', 'commercial_register', 'show_time', 'currency', 'telephone', 'fax', 'mobile', 'email', 'website', 'linkedin', 'facebook', 'twitter', 'google_plus', 'address1', 'address2', 'entity', 'city', 'zip', 'country',

    'invoice_footer', 'invoice_css', 'print_type', 'theme_type', 'tax_invoice', 'show_old_balance', 'show_signature', 'balance_type', 'show_discount_table', 'show_tax_table', 'count_allow',

    'quotations_customer_data', 'quotations_index_data',
    'invoices_customer_data', 'invoices_index_data',
    'bills_customer_data', 'bills_index_data',
    'sale_returns_customer_data', 'sale_returns_index_data',
    'purchase_returns_customer_data', 'purchase_returns_index_data',
    'purchase_orders_customer_data', 'purchase_orders_index_data',
    'sales_payments_customer_data', 'sales_payments_index_data',
    'purchase_payments_customer_data', 'purchase_payments_index_data',
    'sales_cheques_customer_data', 'sales_cheques_index_data',
    'purchase_cheques_customer_data', 'purchase_cheques_index_data',
    'sales_installments_customer_data', 'sales_installments_index_data',
    'purchase_installments_customer_data', 'purchase_installments_index_data',
    'sales_discounts_customer_data', 'sales_discounts_index_data',
    'purchase_discounts_customer_data', 'purchase_discounts_index_data',

    /*-----------------------*/

    'quotation_invoice_group_id', 'quotations_signatures_data',
    'invoice_invoice_group_id', 'invoices_signatures_data',
    'bill_invoice_group_id', 'bills_signatures_data',
    'Sale_return_invoice_group_id', 'sale_returns_signatures_data',

    'Purchase_return_invoice_group_id', 'purchase_returns_signatures_data',
    'order_invoice_group_id', 'orders_signatures_data',
    'purchase_order_invoice_group_id', 'purchase_orders_signatures_data',


    'sales_payment_invoice_group_id', 'sales_payments_signatures_data',
    'purchase_payment_invoice_group_id', 'purchase_payments_signatures_data',
    'sales_cheque_invoice_group_id', 'sales_cheque_signatures_data',
    'purchase_cheque_invoice_group_id', 'purchase_cheques_signatures_data',

    'sales_discount_invoice_group_id', 'sale_discounts_signatures_data',
    'purchase_discount_invoice_group_id', 'purchase_discounts_signatures_data',

    'expense_invoice_group_id', 'expense_signatures_data',

    'safes_deposit_invoice_group_id', 'safes_deposits_signatures_data',
    'safes_withdrawal_invoice_group_id', 'safes_withdrawals_signatures_data',
    'safes_transfer_invoice_group_id', 'safes_transfers_signatures_data',

    'stores_deposit_invoice_group_id', 'stores_deposits_signatures_data',
    'stores_withdrawal_invoice_group_id', 'stores_withdrawals_signatures_data',
    'stores_transfer_invoice_group_id', 'stores_transfers_signatures_data',

    'partners_deposits_invoice_group_id', 'partners_deposits_signatures_data',
    'partners_withdrawals_invoice_group_id', 'partners_withdrawals_signatures_data',
    'partners_settlements_invoice_group_id', 'partners_settlements_signatures_data',
    'partners_profit_invoice_group_id', 'partners_profits_signatures_data',

    'accounts_deposits_invoice_group_id', 'accounts_deposits_signatures_data',
    'accounts_withdrawals_invoice_group_id', 'accounts_withdrawals_signatures_data',

    'projects_deposits_invoice_group_id', 'projects_deposits_signatures_data',
    'projects_withdrawals_invoice_group_id', 'projects_withdrawals_signatures_data',
    'projects_transfers_invoice_group_id', 'projects_transfers_signatures_data',

    'manufacturing_operations_invoice_group_id', 'manufacturing_operations_signatures_data',
    'manufacturing_deposits_invoice_group_id', 'manufacturing_deposits_signatures_data',
    'manufacturing_withdrawals_invoice_group_id', 'manufacturing_withdrawals_signatures_data',

    'employees_salaries_invoice_group_id', 'employees_salaries_signatures_data',
    'employees_incentives_invoice_group_id', 'employees_incentives_signatures_data',
    'employees_deductions_invoice_group_id', 'employees_deductions_signatures_data',
    'employees_withdrawals_invoice_group_id', 'employees_withdrawals_signatures_data',


    "accounts_transfers_invoice_group_id", "accounts_transfers_signatures_data",

    'reports_signatures_data',

    /*----------------*/

    'barcode_price_show', 'barcode_company_show', 'barcode_height', 'barcode_width', 'barcode_height_padding', 'barcode_width_padding', 'barcode_type',

    'item_number', 'print_after',

    'quotations_required_data', 'invoices_required_data', 'bills_required_data', 'sale_returns_required_data', 'purchase_returns_required_data', 'orders_required_data', 'purchase_orders_required_data',



    'salary_expense_section_id', 'allow_minus_quantity', 'main_reports', 'main_elements', 'main_actions'];

    public function site () {
        return $this->belongsTo('App\Models\Site');
    }
}
