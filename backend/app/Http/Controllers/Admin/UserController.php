<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserService;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $userService;
    protected $userRepository;

    public function __construct(UserService $userService, UserRepositoryInterface $userRepository)
    {
        // In Laravel 11+, middleware should be applied in routes or using different method
        $this->userService = $userService;
        $this->userRepository = $userRepository;
    }

    public function index(Request $request)
    {
        $users = $this->userRepository->getByFilters($request);
        $statistics = $this->userRepository->getStatistics();
        $roles = $this->userService->getAllRoles();

        return view('admin.users.index', compact('users', 'statistics', 'roles'));
    }

    public function create()
    {
        $roles = $this->userService->getAllRoles();
        $permissions = $this->userService->getAllPermissions();
        
        return view('admin.users.create', compact('roles', 'permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'avatar' => 'nullable|image|max:2048',
            'roles' => 'array',
            'permissions' => 'array',
        ]);

        $user = $this->userService->createUser($request->all());

        return redirect()->route('admin.users.index')
                        ->with('success', 'تم إنشاء المستخدم بنجاح');
    }

    public function show(User $user)
    {
        // تحميل المستخدم مع العلاقات
        $user = $this->userRepository->findById($user->id);
        
        // استخدام UserService بعد إضافة الحقول الجديدة
        $insights = $this->userService->getUserInsights($user);
        
        return view('admin.users.show', compact('user', 'insights'));
    }

    public function edit(User $user)
    {
        $roles = $this->userService->getAllRoles();
        $permissions = $this->userService->getAllPermissions();
        
        return view('admin.users.edit', compact('user', 'roles', 'permissions'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8',
            'avatar' => 'nullable|image|max:2048',
            'roles' => 'array',
            'permissions' => 'array',
        ]);

        $updated = $this->userService->updateUser($user, $request->all());

        return redirect()->route('admin.users.index')
                        ->with('success', 'تم تحديث المستخدم بنجاح');
    }

    public function destroy(User $user)
    {
        $result = $this->userService->deleteUser($user);

        return redirect()->route('admin.users.index')
                        ->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    public function toggleStatus(User $user)
    {
        $this->userRepository->toggleStatus($user);
        
        return response()->json([
            'success' => true,
            'message' => 'تم تغيير حالة المستخدم',
            'status' => $user->fresh()->status
        ]);
    }

    public function bulkAction(Request $request)
    {
        $result = $this->userService->bulkAction($request->action, $request->user_ids);
        
        return response()->json($result);
    }
}
