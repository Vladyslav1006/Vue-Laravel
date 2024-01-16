<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Rules\NotUsedPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

use Illuminate\Support\Facades\Session;

use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;
use Illuminate\Support\Collection;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:user_list', ['only' => ['index', 'show']]);
        $this->middleware('can:user_create', ['only' => ['create', 'store']]);
        $this->middleware('can:user_edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:user_delete', ['only' => ['destroy']]);
        // $this->middleware('can:user_editOwnProfile',['only'=>['profile','updateProfile']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $globalSearch = AllowedFilter::callback('global', function ($query, $value) {
            $query->where(function ($query) use ($value) {
                Collection::wrap($value)->each(function ($value) use ($query) {
                    $query
                        ->orWhere('name', 'LIKE', "%{$value}%")
                        ->orWhere('email', 'LIKE', "%{$value}%")
                        ->orWhere('phn', 'LIKE', "{$value}%");
                });
            });
        });
        $perPage = request()->query('perPage') ?? 10;
        $users = QueryBuilder::for(User::class)
        ->defaultSort('name')
        ->allowedSorts(['name', 'email','phn'])
        ->allowedFilters(['name', 'email','phn', $globalSearch])
        ->paginate($perPage)
        ->withQueryString();

        $resourceNeo = ['resourceName' => 'user'];

        if(\Auth::user()->can('user_export')) {
            $resourceNeo['bulkActions']['csvExport'] = [];
        }

        return Inertia::render('Admin/UserIndexView', ['users' => $users, 'resourceNeo' => $resourceNeo])->table(function (InertiaTable $table) {
            $table->withGlobalSearch()
            ->column('name', 'Name', searchable: true, sortable: true)
            ->column('email', 'Email', searchable: true, sortable: true)
            ->column('phn', 'Phn', searchable: true, sortable: true)
            ->column('role_name', 'Role', searchable: false, sortable: false)
            ->column(label: 'Actions')
            ->perPageOptions([10,15,30,50,100]);
        });

    }

    public function profile()
    {
        $formdata = auth()->user();
        return Inertia::render('Admin/ProfileView', compact('formdata'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::select('id', 'name as label')->where('id', '<>', 3)->get();
        return Inertia::render('Admin/UserAddEditView', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required',
        ]);

        $role = Role::findById($request->role['id']);
        $user = User::create(request(['name', 'email', 'phn', 'password','twofa']));
        $user->assignRole($role);

        $uname = $request->input('name') . '-' . $role->name;
        \ActivityLog::add(['action' => 'created','module' => 'user','data_key' => $uname]);

        User::neUserMail(
            [
                'password' => $request->input('password'),
                'ip' => $request->ip(),
                'user' => $user,
                'userAgent' => $request->userAgent(),
            ]
        );


        return redirect()->route('user.index')->with(['message' => 'User Created Successfully','msg_type' => 'success']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $formdata = User::where('id', $id)->with('roles')->get()->first();
        if($formdata->role_name == 'super-admin') {
            return redirect()->route('user.index')->with(['message' => 'You don\'t have permission to update Super Admin','msg_type' => 'danger']);
        }


        //dd($formdata);
        $formdata->role = count($formdata->roles) > 0 ? Role::select('id', 'name as label')->where('id', $formdata->roles[0]->id)->get() : 0;
        $roles = array_merge([['id' => 0, 'label' => 'Select']], Role::select('id', 'name as label')->where('id', '<>', 3)->get()->toArray());

        //dd($roles);
        return Inertia::render('Admin/UserAddEditView', compact('roles', 'formdata'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, user $user)
    {
        // dd($request);
        if($user->role_name == 'super-admin') {
            return redirect()->route('user.index')->with(['message' => 'You don\'t have permission to update Super Admin','msg_type' => 'danger']);
        }
        $isDirty = false;
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email,' . $user->id,
            'role' => 'required',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phn = $request->phn;
        $user->twofa = $request->twofa ? 1 : 0;
        if (!empty($request->password)) {
            $user->password = $request->password;
        }
        if($user->isDirty()) {
            $user->save();
            $isDirty = true;
        }



        if($request->role['id'] > 0) {
            $prevrole = $user->roles[0]->id;
            $role = Role::findById($request->role['id']);
            $user->syncRoles($role);
            if($prevrole != $request->role['id']) {
                $isDirty = true;
            }
        } else {
            $user->roles()->detach();
        }
        if($isDirty) {
            $uname = $user->name . '-' . $user->role_name;
            \ActivityLog::add(['action' => 'updated','module' => 'user','data_key' => $uname]);
            $res = ['message' => 'User Updated Successfully.','msg_type' => 'success'];
        } else {
            $res = ['message' => 'No Value  Updated in user .','msg_type' => 'warning'];
        }
        return redirect()->route('user.index')->with($res);
    }

    public function updateProfile(Request $request)
    {
        $user = User::find(Auth::user()->id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email,' . $user->id,
            'password' => [new NotUsedPassword()],
            'current_password' => 'current_password',
        ]);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phn = $request->phn;
        $user->twofa = $request->twofa;
        if($request->twofa) {
            Session::put('user_2fa', auth()->user()->id);
        } else {
            Session::remove('user_2fa');
        }

        if (!empty($request->password)) {
            $user->password = $request->password;
        }
        $user->save();
        $uname = $user->name . '-' . $user->role_name;
        \ActivityLog::add(['action' => 'updated','module' => 'profile','data_key' => $uname]);
        return redirect()->route('profile.profile')->with(['message' => 'Profile Updated Successfully','msg_type' => 'success']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        if($user->role_name == 'super-admin') {
            return redirect()->route('user.index')->with(['message' => 'You don\'t have permission to delete Super Admin','msg_type' => 'danger']);
        }
        if(auth()->user()->id == $user->id) {
            return redirect()->route('user.index')->with(['message' => 'You Can\'t delete yourself.Request Super admin to do it.','msg_type' => 'danger']);
        }

        $uname = $user->name . '-' . $user->role_name;
        $user->delete();
        \ActivityLog::add(['action' => 'deleted','module' => 'user','data_key' => $uname]);
        return redirect()->route('user.index')->with(['message' => 'User Deleted !!']);
    }

    public function authDestroy()
    {
        if((Hash::check(request('password'), Auth::user()->password))) {
            $user = User::find(request('id'));
            $this->destroy($user);

        } else {
            return redirect()->route('user.index')->with(['message' => 'Athentication Failed!!','msg_type' => 'danger']);
        }

    }

}
