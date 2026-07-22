<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Helpers\AdminActivityLogger;

class AdminUserController extends Controller
{
    public function index()
    {
        $admins = Admin::all();
        return view('admin.admins.index', compact('admins'));
    }

    public function create()
    {
        $this->ensureSuperAdmin();
        return view('admin.admins.create');
    }

    public function store(Request $request)
    {
        $this->ensureSuperAdmin();

        $request->validate([
            'name' => [
                'required','string','max:255',
                function ($attribute,$value,$fail){
                    if(filter_var($value,FILTER_VALIDATE_EMAIL)){
                        $fail(__('messages.username_cannot_be_email'));
                    }
                },
            ],
            'email' => 'required|email|unique:admins,email',
            'password' => [
                'required','string','confirmed','min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/'
            ],
            'avatar' => 'nullable|image|max:2048',
        ],[
            'password.regex'=>__('messages.password_requirements'),
            'password.min'=>__('messages.password_min'),
            'password.confirmed'=>__('messages.password_confirmation_required'),
        ]);

        $admin = new Admin();
        $admin->name=$request->name;
        $admin->email=$request->email;
        $admin->password=Hash::make($request->password);

        if($request->hasFile('avatar')){
            $file = $request->file('avatar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(
                public_path('admins/avatars'),
                $filename
            );
            $admin->profile_image = 'admins/avatars/' . $filename;
        }

        $admin->save();

        AdminActivityLogger::log('create_admin',auth('admin')->user(),$admin);

        return redirect()->route('admin.admins.index')
            ->with('success',__('messages.saved'));
    }

    public function edit(Admin $admin)
    {
        $current=auth('admin')->user();

        if(!$current->is_super_admin && $current->id!==$admin->id){
            abort(403,__('messages.super_admin_only'));
        }

        return view('admin.admins.edit',compact('admin'));
    }

    public function update(Request $request,Admin $admin)
    {
        $current=auth('admin')->user();

        if(!$current->is_super_admin && $current->id!==$admin->id){
            abort(403,__('messages.super_admin_only'));
        }

        $rules=[
            'name'=>[
                'required','string','max:255',
                function ($attribute,$value,$fail){
                    if(filter_var($value,FILTER_VALIDATE_EMAIL)){
                        $fail(__('messages.username_cannot_be_email'));
                    }
                },
            ],
            'password'=>[
                'nullable','string','confirmed','min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/'
            ],
            'avatar'=>'nullable|image|max:2048',
        ];

        if($current->is_super_admin){
            $rules['email']='required|email|unique:admins,email,'.$admin->id;
        }

        $validated=$request->validate($rules,[
            'password.regex'=>__('messages.password_requirements'),
            'password.min'=>__('messages.password_min'),
            'password.confirmed'=>__('messages.password_confirmation_required'),
        ]);

        $admin->name=$validated['name'];

        if($current->is_super_admin){
            $admin->email=$validated['email'];
        }

        if(!empty($validated['password'])){
            $admin->password=Hash::make($validated['password']);
        }

        if($request->hasFile('avatar')){
            if(
                $admin->profile_image &&
                file_exists(public_path($admin->profile_image))
            ){
                unlink(
                    public_path($admin->profile_image)
                );
            }
            $file = $request->file('avatar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(
                public_path('admins/avatars'),
                $filename
            );
            $admin->profile_image = 'admins/avatars/' . $filename;
        }

        $admin->save();

        AdminActivityLogger::log('update_admin',$current,$admin);

        return redirect()->route('admin.admins.edit',$admin)
            ->with('success',__('messages.updated'));
    }

    public function destroy(Admin $admin)
    {
        $this->ensureSuperAdmin();

        if(
            $admin->profile_image &&
            file_exists(public_path($admin->profile_image))
        ){
            unlink(
                public_path($admin->profile_image)
            );
        }

        AdminActivityLogger::deleted(auth('admin')->user(),$admin);

        $admin->delete();

        return redirect()->route('admin.admins.index')
            ->with('success',__('messages.deleted'));
    }

    public function toggleSuspend(Admin $admin)
    {
        $current=auth('admin')->user();

        if(!$current->is_super_admin || $current->id===$admin->id){
            abort(403,__('messages.super_admin_only'));
        }

        $admin->is_suspended=!$admin->is_suspended;
        $admin->save();

        AdminActivityLogger::log(
            $admin->is_suspended ? 'suspend_admin' : 'activate_admin',
            $current,
            $admin
        );

        return redirect()->route('admin.admins.index')
            ->with('success',__('messages.admin_updated'));
    }

    private function ensureSuperAdmin():void
    {
        if(!auth('admin')->user()->is_super_admin){
            abort(403,__('messages.super_admin_only'));
        }
    }

    public function usersIndex()
    {
        $users=User::all();
        return view('admin.users.index',compact('users'));
    }

    public function editUser(User $user)
    {
        if (!$user->canBeEditedByAdmin()) {
            abort(403, __('messages.user_oauth_cannot_be_edited'));
        }

        return view(
            'admin.users.edit',
            compact('user')
        );
    }

    public function updateUser(Request $request,User $user)
    {
        if (!$user->canBeEditedByAdmin()) {
            abort(403, __('messages.user_oauth_cannot_be_edited'));
        }
        $request->validate([
            'first_name'=>[
                'required','string','max:50',
                function($attribute,$value,$fail){
                    if(filter_var($value,FILTER_VALIDATE_EMAIL)){
                        $fail(__('messages.username_cannot_be_email'));
                    }
                },
            ],
            'last_name'=>[
                'required','string','max:50',
                function($attribute,$value,$fail){
                    if(filter_var($value,FILTER_VALIDATE_EMAIL)){
                        $fail(__('messages.username_cannot_be_email'));
                    }
                },
            ],
            'email'=>'required|email|unique:users,email,'.$user->id,
            'password'=>[
                'nullable','string','confirmed','min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/'
            ],
        ],[
            'password.regex'=>__('messages.password_requirements'),
            'password.min'=>__('messages.password_min'),
            'password.confirmed'=>__('messages.password_confirmation_required'),
        ]);

        $user->first_name=$request->first_name;
        $user->last_name=$request->last_name;
        $user->email=$request->email;

        if($request->filled('password')){
            $user->password=Hash::make($request->password);
        }

        $user->save();

        AdminActivityLogger::log('update_user',auth('admin')->user(),$user);

        return redirect()->route('admin.users.edit',$user)
            ->with('success',__('messages.updated'));
    }

    public function toggleUserSuspend(User $user)
    {
        $user->is_suspended=!$user->is_suspended;
        $user->save();

        if($user->is_suspended){

            $sessionsTable=config('session.table','sessions');

            DB::table($sessionsTable)
                ->where('user_id',$user->id)
                ->delete();
        }

        AdminActivityLogger::log(
            $user->is_suspended ? 'suspend_user' : 'activate_user',
            auth('admin')->user(),
            $user
        );

        return redirect()->route('admin.users.index')
            ->with('success',__('messages.updated'));
    }

    public function destroyUser(User $user)
    {
        $sessionsTable=config('session.table','sessions');

        DB::table($sessionsTable)
            ->where('user_id',$user->id)
            ->delete();

        AdminActivityLogger::deleted(auth('admin')->user(),$user);

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success',__('messages.deleted'));
    }
}