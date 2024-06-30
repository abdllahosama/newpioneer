<?php

namespace App\Modules\Roles\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Roles\Models\Role;
use App\Modules\Auth\Models\User;
use App\Modules\Roles\Requests\StoreRequest;
use App\Modules\Roles\Requests\UpdateRequest;

class RolesController extends Controller
{
    public function index(Request $request)
    {
        $orderBy   = $request->orderBy;
        $orderType = $request->orderType;
        $search    = $request->search;

        //return $request with data;
        $roles = Role::select('id', 'name')->where('site_id', $this->site()->id);

        //if search data
        if ($search != '') {
            $roles = $roles->where('name', 'like', '%' . $search . '%');
        }

        //if order by
        if ($orderBy != '') {
            $roles = $roles->orderBy($orderBy, $orderType);
        } else {
            $roles = $roles->orderBy('id', 'desc');
        }

        //paginaton
        $roles = $roles->paginate(15);

        foreach ($roles as $role) {
            $role->users_count = User::where('role_id', $role->id)->count();
        }

        return $roles;
    }
    public function items()
    {
        $roles = Role::orderBy('id', 'desc')->where('site_id', $this->site()->id)->get();
        return $roles;
    }
    public function store(StoreRequest $request)
    {
        $data = $request->all();
        $data['main_reports']   = isset($data['main_reports']) ? json_encode($data['main_reports']) : '[]';
        $data['available_reports']   = isset($data['available_reports']) ? json_encode($data['available_reports']) : '[]';
        $data['main_elements']   = isset($data['main_elements']) ? json_encode($data['main_elements']) : '[]';
        $data['main_actions']   = isset($data['main_actions']) ? json_encode($data['main_actions']) : '[]';
        $data['orders_allow_status']   = isset($data['orders_allow_status']) ? json_encode($data['orders_allow_status']) : '[]';
        $data['site_id'] = $this->site()->id;
        if (isset($data['reports_show']) && $data['reports_show'] != 1) {
            $data['main_reports'] = '[]';
        }
        $role = Role::create($data);
    }
    public function show($site, Role $role)
    {
        $role->main_reports   = json_decode($role->main_reports);
        $role->available_reports   = json_decode($role->available_reports);
        $role->main_elements  = json_decode($role->main_elements);
        $role->main_actions   = json_decode($role->main_actions);
        $role->orders_allow_status  = json_decode($role->orders_allow_status);
        return $role;
    }
    public function update(UpdateRequest $request, $site, Role $role)
    {

        $data = $request->all();
        $data['main_reports']   = json_encode($data['main_reports']);
        $data['available_reports']   = json_encode($data['available_reports']);
        $data['main_elements']   = json_encode($data['main_elements']);
        $data['main_actions']    = json_encode($data['main_actions']);
        if ($data['reports_show'] != 1) {
            $data['main_reports'] = '';
        }
        $role->update($data);
    }
    public function destroy($site, Role $role)
    {
        $role->delete();
    }
}
