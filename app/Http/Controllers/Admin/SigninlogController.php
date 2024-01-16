<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SigninLog;
use Inertia\Inertia;

use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

class SigninlogController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:signinlog_list', ['only' => ['index', 'show']]);
        $this->middleware('can:signinlog_delete', ['only' => ['destroy','bulkDestroy']]);
        $this->middleware('can:signinlog_view', ['only' => ['show']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //  $signinlogs = SigninLog::orderBy('created_at', 'desc')->get();
        //  return Inertia::render('Admin/SigninLogIndexView', compact('signinlogs'));

        $globalSearch = AllowedFilter::callback('global', function ($query, $value) {
            $query->where(function ($query) use ($value) {
                Collection::wrap($value)->each(function ($value) use ($query) {
                    $query
                        ->orWhere('email', 'LIKE', "%{$value}%")
                        ->orWhere('ip', 'LIKE', "%{$value}%");
                });
            });
        });
        $perPage = request()->query('perPage') ?? 10;
        $chained = QueryBuilder::for(SigninLog::class)
        ->defaultSort('-created_at')
        ->allowedSorts(['created_at', 'email'])
        ->allowedFilters(['email','msg', AllowedFilter::scope('signedin_start_date'), AllowedFilter::scope('signedin_end_date'), $globalSearch]);
        if($perPage != 10000) {
            $signinlogs = $chained
            ->paginate($perPage)
            ->withQueryString();
        } else {
            $signinlogs = $chained
            ->get();
        }

        $resourceNeo = ['resourceName' => 'signinlog'];

        if(\Auth::user()->can('signinlog_delete')) {
            $resourceNeo['bulkActions'] = ['bulk_delete' => []];
        }
        if(\Auth::user()->can('signinlog_export')) {
            $resourceNeo['bulkActions']['csvExport'] = [];
        }

        return Inertia::render('Admin/SigninLogIndexView', ['signinlogs' => $signinlogs, 'resourceNeo' => $resourceNeo])->table(function (InertiaTable $table) {
            $table->withGlobalSearch()
            ->column('created_at', 'Date', searchable: false, sortable: true, )
            ->column('email', 'Email', searchable: true, sortable: true)
            ->column('ip', 'IP', searchable: false, sortable: false)
            ->column('msg', 'Log', searchable: false, sortable: false)
            ->column('userAgent', 'Device', searchable: false, sortable: false)
            ->column(label: 'Actions')
            ->perPageOptions([10,15,30,50,100, 10000])
            ->selectFilter(key: 'msg', label: 'Log', options: [
                'Login Success' => 'Login Success',
                'Login Failed' => 'Login Failed',
                'Otp Success' => 'Otp Success',
                'Otp Failed' => 'Otp Failed',
            ])
            ->dateFilter(key: 'signedin_start_date', label: 'Date From')
            ->dateFilter(key: 'signedin_end_date', label: 'Date To');
        });

    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SigninLog $signinlog)
    {
        $uname = $signinlog->id;
        $signinlog->delete();
        \ActivityLog::add(['action' => 'deleted','module' => 'signinlog','data_key' => $uname]);
        return redirect()->back()->with('message', 'Logs Deleted !!');
    }


    /**
     * bulk delete.
     */
    public function bulkDestroy()
    {
        SigninLog::whereIn('id', request('ids'))->delete();
        $uname = (count(request('ids')) > 50) ? 'Many' : $uname = implode(',', request('ids'));
        \ActivityLog::add(['action' => 'deleted','module' => 'signinlog','data_key' => $uname]);
        return redirect()->back()->with('message', 'Selected Logs Deleted !!');
    }
}
