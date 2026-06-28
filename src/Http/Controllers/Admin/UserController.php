<?php

namespace Ssh521\LaravelAdmin\Http\Controllers\Admin;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;
use Ssh521\LaravelAdmin\Models\User;
use Ssh521\LaravelAdmin\Services\UserService;
use Ssh521\LaravelAdmin\Support\ResolvesUserModel;

class UserController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private readonly UserService $userService,
        private readonly ResolvesUserModel $users,
    ) {}

    public function selectUser()
    {
        $this->authorize('viewAny', User::class);

        return view('laravel-admin::admin.users.select-user');
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $data = $this->userService->getUserList([
            'search' => $request->search,
            'sortField' => 'created_at',
            'sortDirection' => 'desc',
            'perPage' => 15,
        ]);

        return view('laravel-admin::admin.users.index', compact('data'));
    }

    public function list()
    {
        $this->authorize('viewAny', User::class);

        return redirect()->route('admin.users.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', User::class);

        return view('laravel-admin::admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', User::class);
        $table = $this->users->table();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique($table, 'email')],
            'password' => 'required|string|min:8|same:confirm-password',
            'confirm-password' => 'required|string|min:8',
        ]);

        $user = $this->userService->createUser([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ]);

        // 이메일 인증 상태 처리
        $user->email_verified_at = $request->has('email_verified') ? now() : null;
        $user->save();

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(int $user)
    {
        $user = $this->users->findOrFail($user);
        $this->authorize('view', $user);

        return view('laravel-admin::admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $user)
    {
        $user = $this->users->findOrFail($user);
        $this->authorize('update', $user);

        return view('laravel-admin::admin.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $user)
    {
        $user = $this->users->findOrFail($user);
        $this->authorize('update', $user);
        $table = $this->users->table();

        $request->validate([
            'name' => 'required',
            'email' => ['required', 'email', Rule::unique($table, 'email')->ignore($user->id)],
            'password' => 'same:confirm-password',
        ]);

        $updateData = [
            'userId' => $user->id,
            'name' => $request->name,
            'email' => $request->email,
        ];

        if (! empty($request->password)) {
            $updateData['password'] = $request->password;
        }

        $user = $this->userService->updateUser($updateData);

        // 이메일 인증 상태 처리
        $user->email_verified_at = $request->has('email_verified') ? now() : null;
        $user->save();

        return redirect()->route('admin.users.index')
            ->with('success', $user->name.' 정보가 수정되었습니다.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $user)
    {
        $user = $this->users->findOrFail($user);
        $this->authorize('delete', $user);

        $this->userService->deleteUser([
            'userId' => $user->id,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully');
    }

}
