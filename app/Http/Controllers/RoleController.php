<?php

namespace App\Http\Controllers;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\ModelHasRoles;

use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::orderBy('name')->get(['id','name','created_at','updated_at'])->toArray();
        array_walk($roles, [$this,'getCount']);
        return response()->json($roles);
    }
    public function none_admin()
    {
      return response()->json(Role::where('name','NOT LIKE', '%admin%' )->orderBy('name')->get(['id','name','created_at','updated_at'])
            ->toArray());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {


        try {
            $role = Role::create(['name' => $request->name]);
            $permissions = Permission::whereIn('id', $request->selectedPermissions)->pluck('id','id')->all();
            $role->syncPermissions($permissions);
            return response()->json(['success' => 1]);
        } catch(\Exception $e){
            return response()->json(['error' => $e->getMessage(), 'success' => 0]);
        }



    }

    /**
     * Display the specified resource.
     *
     * @param  Spatie\Permission\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        $permissions = $role->permissions()->getResults()->toArray();
        $row = $role->toArray();
        $row['permissions'] = $permissions;

        return response()->json($row);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Spatie\Permission\Models\Role $role
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $role)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Spatie\Permission\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Role $role)
    {
        $role->syncPermissions($request->selectedPermissions);
        $role->name  = $request->name;
        return response()->json(['success' => $role->save()]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Spatie\Permission\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        try {
            $role->delete();
            return response()->json(['success' => 1]);
        } catch(\Exception $e){
            return response()->json(['error' => $e->getMessage(), 'success' => 0]);
        }
    }

    private function getCount(&$value, $key)
    {
        $id = $value['id'];
        $model = ModelHasRoles::where(['role_id' => $id])
            ->groupby('model_id')
            ->get([\DB::raw('count(*) as ct')])
            ->first();

        $count = $model != null ? $model->ct :0;
        $value['count'] = $count;

    }
}
