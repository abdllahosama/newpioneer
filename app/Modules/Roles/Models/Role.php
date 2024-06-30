<?php

namespace App\Modules\Roles\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'site_id', 'discription',
        'companies_show' , 'companies_add' , 'companies_edit' , 'companies_delete',
        'users_show'     , 'users_add'     , 'users_edit'     , 'users_delete',
        'roles_show'     , 'roles_add'     , 'roles_edit'     , 'roles_delete',
        'customers_show' , 'customers_add' , 'customers_edit' , 'customers_delete',
        'suppliers_show' , 'suppliers_add' , 'suppliers_edit' , 'suppliers_delete',
        'projects_show'  , 'projects_show_allow','projects_add'  , 'projects_edit'  , 'projects_delete',
        'accounts_show'  , 'accounts_add'  , 'accounts_edit'  , 'accounts_delete',
        'partners_show'  , 'partners_add'  , 'partners_edit'  , 'partners_delete',
        'safes_show'     , 'safes_add'     , 'safes_edit'     , 'safes_delete',  'safes_show_allow',
        'bank_accounts_show'     , 'bank_accounts_add'     , 'bank_accounts_edit'     , 'bank_accounts_delete',  'bank_accounts_show_allow',
        'capitals_show'  , 'capitals_add'  , 'capitals_edit'  , 'capitals_delete',
        'stores_show'    , 'stores_add'    , 'stores_edit'    ,'stores_add_products', 'stores_move_products', 'stores_remove_products', 'stores_delete', 'stores_show_allow',

        'products_show'  , 'products_add'  , 'products_edit'  , 'products_delete',   'products_cost',
        'subscriptions_show', 'subscriptions_add', 'subscriptions_edit', 'subscriptions_delete',
        'employees_show' , 'employees_add' , 'employees_edit' , 'employees_delete',
        'quotations_show', 'quotations_show_allow', 'quotations_add' , 'quotations_edit_product', 'quotations_edit', 'quotations_delete', 'quotations_turn_into_invoice',
        'subscriptions_operations_show', 'subscriptions_operations_add', 'subscriptions_operations_edit', 'subscriptions_operations_delete',
        'invoices_show'  , 'invoices_show_allow',  'invoices_add'  , 'invoices_edit_product', 'invoices_edit'  , 'invoices_delete',
        'bills_show'     , 'bills_show_allow', 'bills_add'     , 'bills_edit_product', 'bills_edit'     , 'bills_delete',
        'sale_returns_show'  , 'sale_returns_show_allow', 'sale_returns_add'  , 'sale_returns_edit_product', 'sale_returns_edit'  , 'sale_returns_delete',
        'purchase_returns_show', 'purchase_returns_show_allow'  , 'purchase_returns_add'  , 'purchase_returns_edit_product', 'purchase_returns_edit'  , 'purchase_returns_delete',
        'orders_show'    , 'orders_show_allow', 'orders_add'  , 'orders_edit_product'    , 'orders_edit'    , 'orders_delete', 'orders_turn_into_invoice', 'orders_allow_status', 'orders_edit_date',

        'purchase_orders_show'   , 'purchase_orders_show_allow',    'purchase_orders_add'  , 'purchase_orders_edit_product'  ,  'purchase_orders_edit'  , 'purchase_orders_delete',
        'sales_payments_show'    , 'sales_payments_show_allow',     'sales_payments_add'   ,  'sales_payments_edit_item', 'sales_payments_edit'   , 'sales_payments_delete' ,
        'purchase_payments_show' , 'purchase_payments_show_allow',  'purchase_payments_add',  'purchase_payments_edit_item', 'purchase_payments_edit', 'purchase_payments_delete',

        'sales_cheques_show'     , 'sales_cheques_show_allow',      'sales_cheques_add'   ,   'sales_cheques_edit_item', 'sales_cheques_edit'   , 'sales_cheques_delete' ,
        'purchase_cheques_show'  , 'purchase_cheques_show_allow',   'purchase_cheques_add',   'purchase_cheques_edit_item', 'purchase_cheques_edit', 'purchase_cheques_delete',

        'sales_discounts_show'   , 'sales_discounts_show_allow',    'sales_discounts_add'   , 'sales_discounts_edit_item', 'sales_discounts_edit'   , 'sales_discounts_delete' ,
        'purchase_discounts_show', 'purchase_discounts_show_allow', 'purchase_discounts_add', 'purchase_discounts_edit_item', 'purchase_discounts_edit', 'purchase_discounts_delete',

        'expenses_show'          , 'expenses_show_allow'          , 'expenses_add'      ,     'expenses_edit'      , 'expenses_delete',


        'safes_deposits_show'     , 'safes_deposits_show_allow', 'safes_deposits_show_allow', 'safes_deposits_add', 'safes_deposits_edit_item', 'safes_deposits_edit', 'safes_deposits_delete',
        'safes_withdrawals_show'  , 'safes_withdrawals_show_allow', 'safes_withdrawals_show_allow', 'safes_withdrawals_add', 'safes_withdrawals_edit_item', 'safes_withdrawals_edit', 'safes_withdrawals_delete',
        'safes_transfers_show'    , 'safes_transfers_show_allow', 'safes_transfers_add', 'safes_transfers_edit_item', 'safes_transfers_edit', 'safes_transfers_delete',


        'stores_deposits_show'    , 'stores_deposits_show_allow', 'stores_deposits_add', 'stores_deposits_edit_item', 'stores_deposits_edit', 'stores_deposits_delete',
        'stores_withdrawals_show' , 'stores_withdrawals_show_allow', 'stores_withdrawals_add', 'stores_withdrawals_edit_item', 'stores_withdrawals_edit', 'stores_withdrawals_delete',
        'stores_transfers_show'   , 'stores_transfers_show_allow', 'stores_transfers_add', 'stores_transfers_edit_item', 'stores_transfers_edit', 'stores_transfers_delete',

        'partners_deposits_show'   , 'partners_deposits_add', 'partners_deposits_edit_item', 'partners_deposits_edit', 'partners_deposits_delete', 'partners_deposits_show_allow',
        'partners_withdrawals_show'   , 'partners_withdrawals_add', 'partners_withdrawals_edit_item', 'partners_withdrawals_edit', 'partners_withdrawals_delete', 'partners_withdrawals_show_allow',
        'partners_settlements_show'   , 'partners_settlements_add', 'partners_settlements_edit_item', 'partners_settlements_edit', 'partners_settlements_delete', 'partners_settlements_show_allow',
        'partners_profits_show'   , 'partners_profits_add', 'partners_profits_edit_item', 'partners_profits_edit', 'partners_profits_delete', 'partners_profits_show_allow',

        'accounts_deposits_show', 'accounts_deposits_add', 'accounts_deposits_edit_item', 'accounts_deposits_edit', 'accounts_deposits_delete', 'accounts_deposits_show_allow',
        'accounts_withdrawals_show', 'accounts_withdrawals_add', 'accounts_withdrawals_edit_item', 'accounts_withdrawals_edit', 'accounts_withdrawals_delete', 'accounts_withdrawals_show_allow',

        'projects_deposits_show'   , 'projects_deposits_add', 'projects_deposits_edit_item', 'projects_deposits_edit', 'projects_deposits_delete', 'projects_deposits_show_allow',
        'projects_withdrawals_show'   , 'projects_withdrawals_add', 'projects_withdrawals_edit_item', 'projects_withdrawals_edit', 'projects_withdrawals_delete', 'projects_withdrawals_show_allow',
        'projects_transfers_show'   , 'projects_transfers_add', 'projects_transfers_edit_item', 'projects_transfers_edit', 'projects_transfers_delete', 'projects_transfers_show_allow',

        'manufacturing_processes_show', 'manufacturing_processes_show_allow', 'manufacturing_processes_add', 'manufacturing_processes_edit', 'manufacturing_processes_delete',
        'manufacturing_models_show', 'manufacturing_models_add', 'manufacturing_models_edit_item', 'manufacturing_models_edit', 'manufacturing_models_delete', 'manufacturing_models_show_allow',
        'manufacturing_operations_show', 'manufacturing_operations_add', 'manufacturing_operations_edit_item', 'manufacturing_operations_edit', 'manufacturing_operations_delete', 'manufacturing_operations_show_allow',

        'employees_salaries_show', 'employees_salaries_add', 'employees_salaries_edit_item', 'employees_salaries_edit', 'employees_salaries_delete', 'employees_salaries_show_allow',
        'employees_incentives_show', 'employees_incentives_add', 'employees_incentives_edit_item', 'employees_incentives_edit', 'employees_incentives_delete', 'employees_incentives_show_allow',
        'employees_deductions_show', 'employees_deductions_add', 'employees_deductions_edit_item', 'employees_deductions_edit', 'employees_deductions_delete', 'employees_deductions_show_allow',
        'employees_withdrawals_show', 'employees_withdrawals_add', 'employees_withdrawals_edit_item', 'employees_withdrawals_edit', 'employees_withdrawals_delete', 'employees_withdrawals_show_allow',

        'accounts_transfers_show', 'accounts_transfers_add', 'accounts_transfers_edit', 'accounts_transfers_delete',

        'point_of_sales_show', 'point_of_sales_show_allow' , 'point_of_sales_show_balance', 'point_of_sales_add', 'point_of_sales_edit', 'point_of_sales_delete',
        'staff_show'         , 'staff_add'                 , 'staff_edit'                , 'staff_delete',
        'endorsements_show'  , 'endorsements_show_allow'   , 'endorsements_add'          , 'endorsements_edit'  , 'endorsements_delete',
        'deposits_show'      , 'deposits_show_allow'       ,'deposits_add'               , 'deposits_edit'      , 'deposits_delete',

        'reports_show'       ,  'accounting_show'          ,'reports_earning_show'       ,'reports_show_allow'       , 'options_show', 'buckup_allow',
        'profile_edit'       , 'main_reports'              ,'available_reports'          ,'main_elements'            , 'main_actions'
    ];

}
