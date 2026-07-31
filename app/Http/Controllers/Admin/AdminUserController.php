<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\AdminActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function index()
    {
        $admins = Admin::all();
        return view('admin.admins.index', compact('admins'));
    }

    public function editProfile()
    {
        $admin = auth('admin')->user();
        return view('admin.admins.edit', compact('admin'));
    }

    public function updateProfile(Request $request)
    {
        $admin = auth('admin')->user();
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $fail(__('messages.username_cannot_be_email'));
                    }
                },
            ],
            'email' => [
                'required',
                'email',
                'unique:admins,email,'.$admin->id,
            ],
            'password' => [
                'nullable',
                'string',
                'confirmed',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/',
            ],
            'avatar' => 'nullable|image|max:2048',
        ], [
            'password.regex' => __('messages.password_requirements'),
            'password.min' => __('messages.password_min'),
            'password.confirmed' => __('messages.password_confirmation_required'),
        ]);
        $admin->name = $validated['name'];
        $admin->email = $validated['email'];
        if (! empty($validated['password'])) {
            $admin->password = Hash::make($validated['password']);
        }
        if (
            $request->boolean('remove_avatar') &&
            $admin->profile_image
        ) {

            if (
                file_exists(public_path($admin->profile_image))
            ) {
                unlink(public_path($admin->profile_image));
            }

            $admin->profile_image = null;
        }
        if ($request->hasFile('avatar')) {
            $oldImage = $admin->profile_image;
            $file = $request->file('avatar');
            $filename = time().'_'.$file->getClientOriginalName();
            $file->move(
                public_path('admins/avatars'),
                $filename
            );
            $admin->profile_image = 'admins/avatars/'.$filename;
            if (
                $oldImage &&
                file_exists(public_path($oldImage))
            ) {
                unlink(public_path($oldImage));
            }
        }
        $admin->save();
        AdminActivityLogger::log(
            'update_admin',
            $admin,
            $admin
        );
        return redirect()
            ->route('admin.admins.edit')
            ->with('success', __('messages.updated'));
    }

    public function destroy(Admin $admin)
    {
        $this->ensureSuperAdmin();
        if (
            $admin->profile_image &&
            file_exists(public_path($admin->profile_image))
        ) {
            unlink(public_path($admin->profile_image));
        }
        AdminActivityLogger::deleted(
            auth('admin')->user(),
            $admin
        );
        $admin->delete();
        return redirect()
            ->route('admin.admins.index')
            ->with('success', __('messages.deleted'));
    }

    public function toggleSuspend(Admin $admin)
    {
        $current = auth('admin')->user();
        if (! $current->is_super_admin || $current->id === $admin->id) {
            abort(403, __('messages.super_admin_only'));
        }
        $admin->is_suspended = ! $admin->is_suspended;
        $admin->save();
        AdminActivityLogger::log(
            $admin->is_suspended
                ? 'suspend_admin'
                : 'activate_admin',
            $current,
            $admin
        );
        return redirect()
            ->route('admin.admins.index')
            ->with('success', __('messages.admin_updated'));
    }

    public function usersIndex(Request $request)
    {
        $query = User::query()
            ->with('socialAccounts');
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($query) use ($search) {
                $query->where('id', $search)
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }
        if ($request->filled('status')) {
            if ($request->input('status') === 'active') {
                $query->where('is_suspended', false);
            }
            if ($request->input('status') === 'suspended') {
                $query->where('is_suspended', true);
            }
        }
        if ($request->filled('login_method')) {
            switch ($request->input('login_method')) {
                case 'email_password':
                    $query->whereNotNull('password')
                        ->whereDoesntHave('socialAccounts', function ($query) {
                            $query->where('provider', 'google');
                        });
                    break;
                case 'google':
                    $query->whereNull('password')
                        ->whereHas('socialAccounts', function ($query) {
                            $query->where('provider', 'google');
                        });
                    break;
                case 'google_email_password':
                    $query->whereNotNull('password')
                        ->whereHas('socialAccounts', function ($query) {
                            $query->where('provider', 'google');
                        });
                    break;
            }
        }
        $users = $query
            ->orderBy('id', 'desc')
            ->paginate(25)
            ->withQueryString();
        return view('admin.users.index', compact('users'));
    }

    public function toggleUserSuspend(User $user)
    {
        $user->is_suspended = ! $user->is_suspended;
        $user->save();
        if ($user->is_suspended) {
            $sessionsTable = config('session.table', 'sessions');

            DB::table($sessionsTable)
                ->where('user_id', $user->id)
                ->delete();
        }
        AdminActivityLogger::log(
            $user->is_suspended
                ? 'suspend_user'
                : 'activate_user',
            auth('admin')->user(),
            $user
        );
        return redirect()
            ->route('admin.users.index')
            ->with('success', __('messages.updated'));
    }

    public function destroyUser(User $user)
    {
        $sessionsTable = config('session.table', 'sessions');
        DB::table($sessionsTable)
            ->where('user_id', $user->id)
            ->delete();
        AdminActivityLogger::deleted(
            auth('admin')->user(),
            $user
        );
        $user->delete();
        return redirect()
            ->route('admin.users.index')
            ->with('success', __('messages.deleted'));
    }

    private function ensureSuperAdmin(): void
    {
        if (! auth('admin')->user()->is_super_admin) {
            abort(403, __('messages.super_admin_only'));
        }
    }
}
