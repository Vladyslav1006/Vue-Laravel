<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Inertia\Inertia;
use App\Models\LogActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;

class LogActivityController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:activitylog_list', ['only' => ['index', 'show']]);
        $this->middleware('can:activitylog_delete', ['only' => ['destroy','bulkDestroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $globalSearch = AllowedFilter::callback('global', function ($query, $value) {
            $query->where(function ($query) use ($value) {
                Collection::wrap($value)->each(function ($value) use ($query) {
                    $query
                        ->orWhere('module', 'LIKE', "%{$value}%")
                        ->orWhere('action', 'LIKE', "%{$value}%");
                });
            });
        });
        $perPage = request()->query('perPage') ?? 10;

        $chained = QueryBuilder::for(LogActivity::class)
             ->with('user')
             ->defaultSort('-created_at')
             ->allowedSorts(['created_at', 'action', 'module', 'data_key',  AllowedSort::field('user_name', 'user_id')])
             ->allowedFilters([AllowedFilter::exact('module'),AllowedFilter::exact('action'), AllowedFilter::exact('data_ref'), AllowedFilter::exact('user_id'), AllowedFilter::scope('activity_start_date'), AllowedFilter::scope('activity_end_date'), $globalSearch]);

        if($perPage != 10000) {
            $signinlogs =  $chained
            ->paginate($perPage)
            ->withQueryString();
        } else {
            $signinlogs = $chained
            ->get();
        }

        $resourceNeo = ['resourceName' => 'activitylog'];
        if(\Auth::user()->can('activitylog_updates')) {
            //$resourceNeo['popupUpdates']=['ip'=>[]];
        }

        if(\Auth::user()->can('activitylog_delete')) {
            $resourceNeo['bulkActions']['bulk_delete'] = [];
        }
        if(\Auth::user()->can('activitylog_export')) {
            $resourceNeo['bulkActions']['csvExport'] = [];
        }
        $allmodules = config('app.modules');
        return Inertia::render('Admin/ActivityLogIndexView', ['activitylogs' => $signinlogs, 'resourceNeo' => $resourceNeo,'allmodules' => $allmodules])->table(function (InertiaTable $table) {
            $allusers = User::select('name', 'id')->get();
            $userOption = [];
            foreach($allusers as $usr) {
                $userOption[$usr->id] = $usr->name;
            }
            $table->withGlobalSearch()
            ->column('created_at', 'Date', searchable: false, sortable: true, )
            ->column('user_name', 'User', searchable: false, sortable: true)
            ->column('action', 'Action', searchable: false, sortable: true)
            ->column('data_ref', 'Cell', searchable: true, sortable: false)
            ->column('module', 'Module', searchable: false, sortable: true)

            ->column('ip', 'IP', searchable: false, sortable: false)
            ->column('user_agent', 'Device', searchable: false, sortable: false)
            ->column(label: 'Actions')
            ->perPageOptions([10,15,30,50,100, 10000])
            ->selectFilter(key: 'user_id', label: 'User', options: $userOption)
            ->selectFilter(key: 'action', label: 'Action', options: config('app.actions'))
            ->selectFilter(key: 'module', label: 'Module', options: config('app.modules'))
            ->dateFilter(key: 'activity_start_date', label: 'Date From')
            ->dateFilter(key: 'activity_end_date', label: 'Date To');
        });

    }

    public function fieldUpdate(Request $request)
    {
        $Logactivity = LogActivity::find($request->id);
        $Logactivity->{$request->key} = $request->value;
        $Logactivity->save();

        $uname = $request->key . '->' . $request->value;
        \ActivityLog::add(['action' => 'updated','module' => 'activitylog','data_key' => $uname]);
        return response()->json(['message' => 'Settings Updated Successfully.','msg_type' => 'success']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LogActivity $logactivity)
    {
        $uname = $logactivity->id;
        $logactivity->delete();
        \ActivityLog::add(['action' => 'deleted','module' => 'activitylog','data_key' => $uname]);
        return redirect()->back()->with('message', 'Logs Deleted !!');
    }

    /**
     * bulk delete.
     */
    public function bulkDestroy()
    {
        LogActivity::whereIn('id', request('ids'))->delete();
        $uname = (count(request('ids')) > 50) ? 'Many' : $uname = implode(',', request('ids'));
        \ActivityLog::add(['action' => 'deleted','module' => 'activitylog','data_key' => $uname]);
        return redirect()->back()->with('message', 'Selected Logs Deleted !!');
    }

    public function history(Request $request)
    {
        $module = $request->module;
        $did = $request->did;
        $dkey = $request->dkey;

        if(($module == 'jobrequest' || $module == 'jobsearch') && $dkey == 'crdr1_name') {
            $dkey = 'crdr1';
        } elseif(($module == 'jobrequest' || $module == 'jobsearch') && $dkey == 'crdr2_name') {
            $dkey = 'crdr2';
        }

        $logActivityHist = LogActivity::where('module', $module)->where('data_id', $did)->where('data_key', $dkey)->orderBy('id', 'DESC')->get();

        if(($module == 'jobrequest' || $module == 'jobsearch') && ($dkey == 'crdr1' || $dkey == 'crdr2')) {
            foreach ($logActivityHist as $key => $value) {

                $logActivityHist[$key]->data_val = $value->data_val ? User::find($value->data_val)->name : '';
            }
        }
        return  $logActivityHist;

    }
}
