<?php

namespace App\Http\Controllers;

use App\Models\AclRulesRole;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;


class AclRulesController extends Controller
{
    private $acl_rules_role;
    private $role;

    public function __construct(AclRulesRole $acl_rules_role, Role $role)
    {
        $this->acl_rules_role = $acl_rules_role;
        $this->role = $role;
    }

    /**
     * @param $path
     */
    public function getRole()
    {
        $path = \request()->get('path');

        $rules =  $this->acl_rules_role->where(['path'=> $this->sanitize($path)])
           // ->where('role_id', '<>', 1)
            ->get()->toArray();
        array_walk($rules, [$this, 'parseRule']);
        return response()->json($rules);
    }

    public function postRules(Request $request)
    {
        $this->acl_rules_role->where('path', '=', $this->sanitize($request->path))
            ->delete();
        foreach($request->selectedRoles as $role_id)
        {
            AclRulesRole::create(['role_id' => $role_id, 'path' => $this->sanitize($request->path),
                'access' => 1, 'disk' => 'storage']);
        }
        return response()->json(['success' => 1]);
    }

    public function parseRule(&$value, $key)
    {
        $role_id = $value['role_id'];
        $value['role'] =  Role::findById($role_id);
        unset( $value['role_id']);

    }
    private function sanitize(string $path): string
    {
        $path = preg_replace('/\s/', '_', $path);
        $path = preg_replace('/\(|\)/', "", $path);
        return strtolower($path);
    }
}
