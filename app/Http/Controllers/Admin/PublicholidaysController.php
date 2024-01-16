<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Publicholiday;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Inertia\Inertia;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Support\Facades\Validator;
use App\Helpers\Helper;


class PublicholidaysController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:publicholiday_list', ['only' => ['index', 'show']]);
        $this->middleware('can:publicholiday_create', ['only' => ['create', 'store']]);
        $this->middleware('can:publicholiday_edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:publicholiday_delete', ['only' => ['destroy']]);
        //$this->middleware('can:publicholiday_delete', ['only' => ['destroy','bulkDestroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $perPage = request()->query('perPage') ?? 10;
        $public_holidays = QueryBuilder::for(Publicholiday::class)
            ->defaultSort('holiday_name')
            ->allowedSorts(['holiday_name', 'holiday_date'])
            ->allowedFilters([
                'holiday_name',
                'holiday_date',
                AllowedFilter::callback('global', function ($query, $value) {
                    $query->where(function ($query) use ($value) {
                        Collection::wrap($value)->each(function ($value) use ($query) {
                            $query
                                ->orWhere('holiday_name', 'LIKE', "%{$value}%")
                                ->orWhere('holiday_date', 'LIKE', "%{$value}%");
                        });
                    });
                }),
            ])
            ->paginate($perPage)
            ->withQueryString();

        $resourceNeo = ['resourceName' => 'public-holidays'];

        if (\Auth::user()->can('publicholiday_export')) {
            $resourceNeo['bulkActions']['csvExport'] = [];
        }

        if (\Auth::user()->can('publicholiday_delete')) {
            $resourceNeo['bulkActions']['bulk_delete'] = [];
        }

        $resourceNeo['cellDetail'] = true;
        $resourceNeo['cellGoToHistoryDisabled'] = true;

        return Inertia::render('Admin/PublicHoliday/PublicholidaysIndexView', ['public_holidays' => $public_holidays, 'resourceNeo' => $resourceNeo])->table(function (InertiaTable $table) {
            $NextColKey = '';
            $table->withGlobalSearch()
            ->column('holiday_name', 'Holiday Name', searchable: true, sortable: true, extra:['info' => 'Holiday Name', 'colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])->column('holiday_date', 'Holiday Date', searchable: true, sortable: true, extra:['info' => 'Holiday Date (previously d-m-yy format in excel)', 'type' => 'datePicker', 'freetext' => 'input', 'colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
            
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
        return Inertia::render('Admin/PublicHoliday/PublicholidaysAddEditView');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'holiday_name' => 'required|string|max:255',
            'holiday_date' => 'required|date'
        ]);
        $model = new Publicholiday();
        $model->holiday_name = request('holiday_name', null);
        $model->holiday_date = request('holiday_date', null);
        $model->save();
        \ActivityLog::add(['action' => 'created', 'module' => 'public-holiday', 'data_key' => $request->input('name'), 'data_ref' => $model->id]);

        return redirect()->route('public-holidays.index')->with(['message' => 'Public Holiday Created Successfully', 'msg_type' => 'success']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $formdata = Publicholiday::where('id', $id)->get()->first();
        //dd($formdata);
        return Inertia::render('Admin/PublicHoliday/PublicholidaysAddEditView', compact('formdata'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, $id)
    {
        $request->validate([
            'holiday_name' => 'required|string|max:255',
            'holiday_date' => 'required|date'
        ]);
        $ph = Publicholiday::find($id);
        $ph->holiday_name = $request->holiday_name;
        $ph->holiday_date = $request->holiday_date;

        if ($ph->isDirty()) {
            $ph->save();
            $isDirty = true;
        }

        if ($isDirty) {
            \ActivityLog::add(['action' => 'updated', 'module' => 'public-holiday', 'data_key' => $request->holiday_name, 'data_ref' => $id]);
            $res = ['message' => 'Public Holiday Updated Successfully.', 'msg_type' => 'success'];
        } else {
            $res = ['message' => 'No Value  Updated in public holiday .', 'msg_type' => 'warning'];
        }
        return redirect()->route('public-holidays.index')->with($res);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $ph = Publicholiday::find($id);
        $ph->delete();
        \ActivityLog::add(['action' => 'deleted', 'module' => 'public-holiday', 'data_key' => $ph->holiday_name, 'data_ref' => $id]);
        return redirect()->route('public-holidays.index')->with(['message' => 'Public Holiday Deleted !!']);
    }

    /**
     * Bulk replicate.
     */
    public function bulkDuplicate()
    {
        $allDuplicate = [];
        foreach (request('ids') as $id) {
            $public_holiday = Publicholiday::find($id);
            //dd($public_holiday);
            for ($i = 0; $i < request('nx'); $i++) {
                $newJobRequest = $public_holiday->replicate();
                $newJobRequest->save();
                $allDuplicate[] = $id;
            }
        }
        $uname = (count($allDuplicate) > 10) ? 'Many' : $uname = implode(',', $allDuplicate);
        \ActivityLog::add(['action' => 'duplicated', 'module' => 'public-holiday', 'data_key' => $uname, 'data_ref' => $uname,]);
        return redirect()->back()->with(['message' => 'Selected Public Holiday duplicated Successfully! ', 'msg_type' => 'info']);
    }

    /**
     * bulk delete.
     */
    public function bulkDestroy()
    {
        Publicholiday::whereIn('id', request('ids'))->delete();
        $uname = (count(request('ids')) > 10) ? 'Many' : $uname = implode(',', request('ids'));
        \ActivityLog::add(['action' => 'deleted', 'module' => 'public-holiday', 'data_key' => $uname, 'data_ref' => $uname,]);
        return redirect()->back()->with(['message' => 'Selected Public Holiday Deleted !!', 'msg_type' => 'warning']);
    }

    /**
     * save export activity log
     */
    public function saveExportActivityLog()
    {
        $uname = (count(request('ids')) > 10) ? 'Many' : $uname = implode(',', request('ids'));
        \ActivityLog::add(['action' => 'exportfile', 'module' => 'public-holiday', 'data_key' => $uname, 'data_ref' => $uname,]);
        return redirect()->back()->with(['message' => 'Selected Public Holidays Exported Successfully!', 'msg_type' => 'success']);
    }

    /**
     * import File
     */
    public function importFile()
    {
        return Inertia::render('Admin/PublicHoliday/PublicHolidayImportFile');
    }

    public function importFileSubmit(Request $request)
    {
        Validator::extend('csv_file', function ($attribute, $value, $parameters, $validator) {
            return pathinfo($value->getClientOriginalName(), PATHINFO_EXTENSION) === 'csv';
        });

        $this->validate($request, [
            'import_file' => 'required|file|max:2048|csv_file',
        ], [
            'import_file.csv_file' => 'The import file must have a .csv extension.',
        ]);

        $import_file = $request->file('import_file');
        if ($import_file) {
            $handle = fopen($import_file->path(), 'r');

            // Validate CSV format
            $header = fgetcsv($handle, 1000, ',');
            $expectedHeader = ['holiday_name', 'holiday_date'];
            $expectedColumnCount = count($expectedHeader);
            $rowNumber = 0;

            if (!$this->validateHeader($header, $expectedHeader)) {
                fclose($handle);
                return redirect()->route('public-holidays.index')->withErrors([
                    'import_file' => 'Please check CSV format. Use the latest exported CSV format and edit from there.',
                ]);
            }

            $records = [];

            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                $rowNumber++;
                $columnCount = count($data);
                if ($columnCount !== $expectedColumnCount) {
                    fclose($handle);
                    return redirect()->route('public-holidays.index')->withErrors([
                        'import_file' => "Invalid number of columns in row $rowNumber. Please check CSV format.",
                    ]);
                }

                if ($data[0] != "" && $data[1] != "") {
                    $records[] = [
                        'holiday_name' => $data[0],
                        'holiday_date' => date('Y-m-d', strtotime($data[1])),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            fclose($handle);

            if (!empty($records)) {
                $recordIDs = [];
                foreach ($records as $record) {
                    $recordIDs[] = Publicholiday::insertGetId($record);
                }

                $data_ref = (count($recordIDs) > 10) ? 'Many' : $uname = implode(',', $recordIDs);

                \ActivityLog::add([
                    'action' => 'importfile',
                    'module' => 'public-holiday',
                    'data_key' => 'import-file',
                    'data_ref' => $data_ref
                ]);

                return redirect()->route('public-holidays.index')->with([
                    'message' => 'Public Holiday file imported Successfully',
                    'msg_type' => 'success',
                ]);
            }

            return redirect()->route('public-holidays.index')->with([
                'message' => 'You uploaded file(s) with empty record(s). Nothing imported.',
                'msg_type' => 'warning',
            ]);
        }
    }
    private function validateHeader($header, $expectedHeader)
    {
        return count($header) === count($expectedHeader) && empty(array_diff($expectedHeader, $header));
    }
}
