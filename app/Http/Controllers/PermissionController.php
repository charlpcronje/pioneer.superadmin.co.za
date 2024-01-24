<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return response()->json(Permission::all()->toArray());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            Permission::create(['name' => $request->name]);
            $role = Role::findByName('Admin');
            $permissions = Permission::pluck('id','id')->all();
            $role->syncPermissions($permissions);
            return response()->json(['success' => 1]);
        } catch(\Exception $e){
            return response()->json(['error' => $e->getMessage(), 'success' => 0]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  Spatie\Permission\Models\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function show(Permission $permission)
    {
        return response()->json($permission->toArray());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Spatie\Permission\Models\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function edit(Permission $permission)
    {
        return response()->json($permission->toArray());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Spatie\Permission\Models\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Permission $permission)
    {
        $permission->name  = $request->name;
        return response()->json(['success' => $permission->save()]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Spatie\Permission\Models\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function destroy(Permission $permission)
    {
        try {
            $permission->delete();
            return response()->json(['success' => 1]);
        } catch(\Exception $e){
            return response()->json(['error' => $e->getMessage(), 'success' => 0]);
        }
    }
}
