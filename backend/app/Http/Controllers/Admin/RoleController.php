<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\RoleService;
use App\Repositories\Interfaces\RoleRepositoryInterface;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    protected $roleService;
    protected $roleRepository;

    public function __construct(RoleService $roleService, RoleRepositoryInterface $roleRepository)
    {
        $this->roleService = $roleService;
        $this->roleRepository = $roleRepository;
    }

    public function index(Request $request)
    {
        $data = $this->roleService->searchRoles($request);
        return view('admin.roles.index', $data);
    }

    public function create()
    {
        $permissions = $this->roleService->getPermissionsGrouped();
        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'array',
        ]);

        $role = $this->roleService->createRole($request->all());

        return redirect()->route('admin.roles.index')
                        ->with('success', 'تم إنشاء الدور بنجاح');
    }

    public function show(Role $role)
    {
        $data = $this->roleService->getRoleInsights($role);
        
        return view('admin.roles.show', [
            'role' => $role,
            'permissions' => $data['permissions'], // Collection مباشرة
            'users' => $data['users'], // Collection مباشرة
            'insights' => $data['insights'] ?? []
        ]);
    }

    public function edit(Role $role)
    {
        $permissions = $this->roleService->getPermissionsGrouped();
        $rolePermissions = $this->roleService->getRolePermissions($role);
        
        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'array',
        ]);

        $updated = $this->roleService->updateRole($role, $request->all());

        if ($updated) {
            return redirect()->route('admin.roles.index')
                           ->with('success', 'تم تحديث الدور بنجاح');
        }

        return redirect()->back()
                        ->with('error', 'فشل في تحديث الدور');
    }

    public function destroy(Role $role)
    {
        $result = $this->roleService->deleteRole($role);

        return redirect()->route('admin.roles.index')
                        ->with($result['success'] ? 'success' : 'error', $result['message']);
    }
}
