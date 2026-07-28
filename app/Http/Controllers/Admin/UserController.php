<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Models\ProjectSite;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:users.manage');
    }

    public function index(): Response
    {
        return Inertia::render('Admin/Users/Index', [
            'users' => User::with(['roles', 'projectSites'])->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Users/Form', [
            'user' => null,
            'roles' => Role::orderBy('name')->get(['id', 'name']),
            'sites' => ProjectSite::orderBy('sort_order')->get(['id', 'code', 'name']),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $role = $data['role'];
        $siteIds = $data['project_site_ids'] ?? [];
        unset($data['role'], $data['project_site_ids']);

        $user = User::create([
            ...$data,
            'password' => Hash::make($data['password']),
            'email_verified_at' => now(),
        ]);

        $user->syncRoles([$role]);
        $user->projectSites()->sync($siteIds);

        return redirect()->route('admin.users.index')
            ->with('success', 'User created.');
    }

    public function edit(User $user): Response
    {
        return Inertia::render('Admin/Users/Form', [
            'user' => $user->load(['roles', 'projectSites']),
            'roles' => Role::orderBy('name')->get(['id', 'name']),
            'sites' => ProjectSite::orderBy('sort_order')->get(['id', 'code', 'name']),
        ]);
    }

    public function update(StoreUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();
        $role = $data['role'];
        $siteIds = $data['project_site_ids'] ?? [];
        unset($data['role'], $data['project_site_ids']);

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);
        $user->syncRoles([$role]);
        $user->projectSites()->sync($siteIds);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted.');
    }

    public function assignSites(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'project_site_ids' => ['array'],
            'project_site_ids.*' => ['exists:project_sites,id'],
        ]);

        $user->projectSites()->sync($validated['project_site_ids'] ?? []);

        return back()->with('success', 'Site assignments updated.');
    }
}
