<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PermissionService;
use App\Repositories\Interfaces\PermissionRepositoryInterface;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    protected $permissionService;
    protected $permissionRepository;

    public function __construct(PermissionService $permissionService, PermissionRepositoryInterface $permissionRepository)
    {
        $this->permissionService = $permissionService;
        $this->permissionRepository = $permissionRepository;
    }

    public function index(Request $request)
    {
        $data = $this->permissionService->searchPermissions($request);
        return view('admin.permissions.index', $data);
    }

    public function create()
    {
        return view('admin.permissions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
        ]);

        try {
            $permission = $this->permissionService->createPermission($request->all());
            return redirect()->route('admin.permissions.index')
                           ->with('success', 'تم إنشاء الصلاحية بنجاح');
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', $e->getMessage());
        }
    }

    public function edit(Permission $permission)
    {
        return view('admin.permissions.edit', compact('permission'));
    }

    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $permission->id,
        ]);

        try {
            $updated = $this->permissionService->updatePermission($permission, $request->all());
            
            if ($updated) {
                return redirect()->route('admin.permissions.index')
                               ->with('success', 'تم تحديث الصلاحية بنجاح');
            }
            
            return redirect()->back()
                           ->with('error', 'فشل في تحديث الصلاحية');
                           
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', $e->getMessage());
        }
    }

    public function destroy(Permission $permission)
    {
        $result = $this->permissionService->deletePermission($permission);

        return redirect()->route('admin.permissions.index')
                        ->with($result['success'] ? 'success' : 'error', $result['message']);
    }
}
