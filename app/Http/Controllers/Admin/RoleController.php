<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use Inertia\Inertia;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:role_list', ['only' => ['index', 'show']]);
        $this->middleware('can:role_create', ['only' => ['create', 'store']]);
        $this->middleware('can:role_edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:role_delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $query = Role::where('id', '<>', 3);
        //return Inertia::render('Admin/RoleIndexView', compact('roles'));

        $globalSearch = AllowedFilter::callback('global', function ($query, $value) {
            $query->where(function ($query) use ($value) {
                Collection::wrap($value)->each(function ($value) use ($query) {
                    $query
                        ->orWhere('name', 'LIKE', "%{$value}%");
                });
            });
        });
        $perPage = request()->query('perPage') ?? 10;
        $roles = QueryBuilder::for($query)
            ->defaultSort('name')
            ->allowedFilters(['name', $globalSearch])
            ->paginate($perPage)
            ->withQueryString();

        $resourceNeo = ['resourceName' => 'role'];

        if (\Auth::user()->can('role_export')) {
            $resourceNeo['bulkActions']['csvExport'] = [];
        }

        return Inertia::render('Admin/RoleIndexView', ['roles' => $roles, 'resourceNeo' => $resourceNeo])->table(function (InertiaTable $table) {
            $table->withGlobalSearch()
                ->column('name', 'Name', searchable: false, sortable: true,)
                ->column(label: 'Actions')
                ->perPageOptions([10, 15, 30, 50, 100]);
        });
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $allperm_t = Permission::orderBy('name')->get();
        $prev = '';
        $allpermissions = [];
        $allmodules = config('app.modules');
        foreach ($allperm_t as $key => $perm) {
            $perm_a = explode('_', $perm->name);
            if ($perm_a[0] != 'permission') {
                if ($perm_a[0] != $prev) {
                    $prev = $perm_a[0];
                    $allpermissions[$prev] = ['sts' => false, 'child' => [], 'name' => $allmodules[$prev]];
                }
                $allpermissions[$prev]['child'][] = [$perm->id, $perm_a[1], false, $perm->perm_label];
            }
        }
        $allpermissions = $this->sortPermission($allpermissions);
        return Inertia::render('Admin/RoleAddEditView', compact('allpermissions'));
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
        ]);

        $role = Role::create(request(['name']));

        foreach ($request->permission as $grouppermssion) {
            foreach ($grouppermssion['child'] as $permdata) {
                if ($permdata[2]) {
                    $permission = Permission::findById($permdata[0]);
                    $role->givePermissionTo($permission);
                }
            }
        }
        $uname = $request->input('name');
        \ActivityLog::add(['action' => 'created', 'module' => 'role', 'data_key' => $uname]);

        return redirect()->route('role.index')->with(['message' => 'Role Created Successfully', 'msg_type' => 'success']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $formdata = Role::where('id', $id)->with('permissions:id')->get()->first();

        $allgivenpermission = $formdata->permissions->pluck('id')->toArray();

        $allperm_t = Permission::all();
        $prev = '';
        $allpermissions = [];
        $allmodules = config('app.modules');
        foreach ($allperm_t as $key => $perm) {
            $perm_a = explode('_', $perm->name);
            if ($perm_a[0] != 'permission') {
                if ($perm_a[0] != $prev) {
                    $prev = $perm_a[0];
                    $allpermissions[$prev] = ['sts' => false, 'child' => [], 'name' => $allmodules[$prev]];
                }
                $allpermissions[$prev]['child'][] = [$perm->id, ucwords($perm_a[1]), in_array($perm->id, $allgivenpermission), $perm->perm_label];
                $allpermissions[$prev]['sts'] = in_array($perm->id, $allgivenpermission) ? true : $allpermissions[$prev]['sts'];
            }
        }
        $allpermissions = $this->sortPermission($allpermissions);
        return Inertia::render('Admin/RoleAddEditView', compact('formdata', 'allpermissions'));
    }


    private function sortPermission($allpermissions)
    {
        $allPermissionKeys = array_keys($allpermissions);
        $allPermissionOrderArray = config('app.modules_order');
        $requiredKeys = array_intersect($allPermissionOrderArray, $allPermissionKeys);

        $allpermissions = array_slice(((array_replace(array_flip($requiredKeys), $allpermissions))), 0, count($allpermissions));

        // foreach($allpermissions as $key =>  $permission)
        // {
        //    $activeModIndex =  array_search('Activate Mod', array_column($allpermissions[$key]['child'], '3'));
        //    if($activeModIndex !== false)
        //    {
        //         array_push($allpermissions[$key]['child'],$allpermissions[$key]['child'][$activeModIndex]);
        //         unset($allpermissions[$key]['child'][$activeModIndex]);
        //    }

        // //    search for edit own profile
        //    $editProfileIndex = array_search('edit own profile', array_column($allpermissions[$key]['child'], '3'));
        //    if($editProfileIndex !== false)
        //    {
        //         array_unshift($allpermissions[$key]['child'], $allpermissions[$key]['child'][$editProfileIndex]);
        //         unset($allpermissions[$key]['child'][$editProfileIndex +1]);
        //    }
        // }
        // dd($allpermissions);
        return $allpermissions;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $isDirty = false;

        $role->name = $request->name;
        $role->guard_name = 'web';
        if ($role->isDirty()) {
            $role->save();
            $isDirty = true;
        }

        $formdata = Role::where('id', $role->id)->with('permissions:id')->get()->first();
        $allgivenpermission = [];
        foreach ($formdata->permissions as $key => $value) {
            $allgivenpermission[] = $value->id;
        }
        $allnewpermission = [];

        $role->permissions()->detach();
        foreach ($request->permission as $grouppermssion) {
            foreach ($grouppermssion['child'] as $permdata) {
                if ($permdata[2]) {
                    $permission = Permission::findById($permdata[0]);
                    $allnewpermission[] = $permdata[0];
                    $role->givePermissionTo($permission);
                }
            }
        }
        sort($allnewpermission);
        sort($allgivenpermission);

        if ($allnewpermission != $allgivenpermission) {
            $isDirty = true;
        }
        if ($isDirty) {
            $uname = $role->name;
            \ActivityLog::add(['action' => 'updated', 'module' => 'role', 'data_key' => $uname]);
            $res = ['message' => 'Role Updated Successfully.', 'msg_type' => 'success'];
        } else {
            $res = ['message' => 'No Value  Updated in Role .', 'msg_type' => 'warning'];
        }
        return redirect()->route('role.index')->with($res);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        $users = $role->users()->get();

        if (count($users) > 0) {
            return redirect()->route('role.index')->with(['message' => 'Can\'t delete role that has users associated, first dissociate user with this role !!', 'msg_type' => 'danger']);
        }
        $uname = $role->name;
        $role->delete();
        \ActivityLog::add(['action' => 'deleted', 'module' => 'role', 'data_key' => $uname]);
        return redirect()->route('role.index')->with('message', 'Role Deleted !!');
    }
}
