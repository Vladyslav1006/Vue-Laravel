<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Inertia\Inertia;
use App\Helpers\Helper;
use App\Models\Setting;
use App\Models\JobSearch;
use App\Models\JobRequest;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\OtherAddrress;
use App\Models\Publicholiday;
use Illuminate\Support\Carbon;
use App\Events\MedideNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\OtherPhone;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use App\Http\Class\StringLengthSort;
use App\Services\GoogleApiPeopleService;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;

class JobRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:jobrequest_list', ['only' => ['index', 'show']]);
        $this->middleware('can:jobrequest_delete', ['only' => ['destroy','bulkDestroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $public_holidays = Publicholiday::all();
        $globalSearch = AllowedFilter::callback('global', function ($query, $value) {
            $query->where(function ($query) use ($value) {
                Collection::wrap($value)->each(function ($value) use ($query) {
                    $query
                    ->orWhereFullText(['job_requests.full_name',  'job_requests.job_loc', 'job_requests.job_loc_addr',  'job_requests.job_loc_addr2',   'job_requests.pet_description',   'job_requests.job_schdl',  'job_requests.job_crit', 'job_requests.pay_pref', 'job_requests.expc_budget', 'job_requests.budget_flex', 'job_requests.other_info', 'job_requests.CRDR1_reason', 'job_requests.CRDR2_reason',  'job_requests.rejectReason', 'job_requests.int_c_bb', 'job_requests.remark' ], $value)

                    ->orWhere('job_requests.pet_muzzle', 'LIKE', "%{$value}%")

                    ->orWhere('job_requests.MBJNo', 'LIKE', "{$value}%")
                    ->orWhere('job_requests.salut', 'LIKE', "{$value}%")
                    ->orWhere('job_requests.salut', 'LIKE', "{$value}%")
                    ->orWhere('job_requests.date_flex', 'LIKE', "{$value}%")
                    ->orWhere('job_requests.job_loc_addr_unit', 'LIKE', "{$value}%")
                    ->orWhere('job_requests.job_loc_addr_unit2', 'LIKE', "{$value}%")
                    ->orWhere('job_requests.pets', 'LIKE', "{$value}%")
                    ->orWhere('job_requests.job_alone', 'LIKE', "{$value}%")
                    ->orWhere('job_requests.job_restriction', 'LIKE', "{$value}%")
                    ->orWhere('job_requests.BStatus', 'LIKE', "{$value}%")
                    ->orWhere('job_requests.duration', 'LIKE', "{$value}%")
                    ->orWhere('job_requests.status_s2c', 'LIKE', "{$value}%")
                    ->orWhere('job_requests.created_at', 'LIKE', "{$value}%")
                    ->orWhere('job_requests.start_date', 'LIKE', "{$value}%")
                    ->orWhere('job_requests.dob_kid', 'LIKE', "{$value}%")
                    ->orWhere('job_requests.dob_young_kid', 'LIKE', "{$value}%")
                    ->orWhere('job_requests.dob_old_kid', 'LIKE', "{$value}%")

                    ->orWhere('job_requests.email', $value)
                    ->orWhere('job_requests.whts_no', $value)
                    ->orWhere('job_requests.no_of_kids', $value)
                    ;
                });
            });
        });
        $perPage = request()->query('perPage') ?? 10;
        if(\Auth::user()->can('jobrequest_listAll')) {
            $Query = JobRequest::select('job_requests.*', 'u1.name AS crdr1_name', 'u2.name AS crdr2_name', 'u3.name AS last_edit_name')->leftJoin('users AS u1', 'u1.id', 'job_requests.crdr1', 'left')->leftJoin('users  AS u2', 'u2.id', 'job_requests.crdr2', 'left')->leftJoin('users  AS u3', 'u3.id', 'job_requests.last_edited', 'left');
        } else {
            $Query = JobRequest::select('job_requests.*', 'u1.name AS crdr1_name', 'u2.name AS crdr2_name', 'u3.name AS last_edit_name')->leftJoin('users AS u1', 'u1.id', 'job_requests.crdr1', 'left')->leftJoin('users  AS u2', 'u2.id', 'job_requests.crdr2', 'left')->leftJoin('users  AS u3', 'u3.id', 'job_requests.last_edited', 'left')->where(function ($query) {$query->where('CRDR1', null)->orWhere('CRDR1', Auth::id())->orWhere('CRDR2', Auth::id());});
        }
        $chained = QueryBuilder::for($Query)
        ->defaultSort('-created_at')
        ->allowedSorts(['created_at', 'salut', 'full_name', 'email', 'whts_no', 'start_date', 'date_flex', 'no_of_kids', 'int_c_bb',  'remark', 'status_s2c','dob_kid', 'dob_young_kid', 'dob_old_kid', 'job_loc', 'job_loc_addr', 'job_loc_addr_unit', 'job_loc_addr2', 'job_loc_addr_unit2', 'pets', 'pet_muzzle', 'pet_description', 'job_alone', 'job_whoelse', 'job_schdl', 'job_restriction', 'job_crit','pay_pref', 'expc_budget', 'budget_flex','other_info', AllowedSort::field('crdr1_name', 'u1.name'), 'CRDR1_reason',AllowedSort::field('crdr2_name', 'u2.name'), 'CRDR2_reason',  AllowedSort::field('last_edited', 'u3.name'), 'BStatus', 'rejectReason', AllowedSort::field('s_full_name', 'full_name'), AllowedSort::custom('MBJNo', new StringLengthSort(), 'MBJNo')])

        ->allowedFilters([AllowedFilter::exact('id'), AllowedFilter::scope('created_at', 'created_at_search'), AllowedFilter::scope('created_at_start'), AllowedFilter::scope('created_at_end'),  AllowedFilter::scope('start_date', 'start_date_search'), AllowedFilter::scope('start_date_start'), AllowedFilter::scope('start_date_end'),  AllowedFilter::scope('dob_kid', 'dob_kid_search'), AllowedFilter::scope('dob_kid_start'), AllowedFilter::scope('dob_kid_end'), AllowedFilter::scope('dob_young_kid', 'dob_young_kid_search'), AllowedFilter::scope('dob_young_kid_start'), AllowedFilter::scope('dob_young_kid_end'),  AllowedFilter::scope('dob_old_kid', 'dob_old_kid_search'), AllowedFilter::scope('dob_old_kid_start'), AllowedFilter::scope('dob_old_kid_end'),  'salut', 'full_name','email', AllowedFilter::exact('whts_no'), 'int_c_bb', AllowedFilter::exact('crdr1_name', 'crdr1'), 'CRDR1_reason', AllowedFilter::exact('crdr2_name', 'crdr2') ,'CRDR2_reason', 'BStatus', 'rejectReason', AllowedFilter::exact('date_flex'), AllowedFilter::exact('no_of_kids'), 'other_info', 'job_loc', 'job_loc_addr', AllowedFilter::exact('job_loc_addr_unit'), 'job_loc_addr2', AllowedFilter::exact('job_loc_addr_unit2'), AllowedFilter::exact('pets'), 'pet_muzzle', 'pet_description', AllowedFilter::exact('job_alone'),'job_whoelse', 'job_schdl', AllowedFilter::exact('job_restriction'),'job_crit', 'expc_budget','pay_pref', 'budget_flex', AllowedFilter::exact('last_edited'), 'remark', 'status_s2c',$globalSearch, AllowedFilter::exact('MBJNo')]);
        if($perPage != 10000) {
            $jobrequests = $chained
            ->paginate($perPage)
            ->withQueryString();
        } else {
            $jobrequests = $chained
            ->get();
        }
        $resourceNeo = ['resourceName' => 'jobrequest'];

        $maxCheckboxSelect = Setting::where('slug', 'max-checkbox-select')->firstOrFail();
        $resourceNeo['max-checkbox-select'] = $maxCheckboxSelect->value;

        $coordinatorName =  config('app.coordinator.name', 'Coordinator');
        $users = User::role($coordinatorName)->get();
        $allcoordinator = [];
        foreach ($users as $key => $coorr) {
            if(!(\Auth::user()->can('jobrequest_changeCRDR')) && $coorr->id == \Auth::user()->id) {
                continue;
            }
            $allcoordinator[] = ['id' => $coorr->id,'label' => $coorr->name];
        }

        $resourceNeo['allcordinator'] = $allcoordinator;
        $resourceNeo['cellDetail'] = true;
        $resourceNeo['bulkActions'] = [];
        $resourceNeo['servExp'] = true;

        if(!(\Auth::user()->can('all')) && \Auth::user()->can('jobrequest_DisCheckboxes')) {
            $resourceNeo['DisCheckboxes'] = true;
        }


        if(\Auth::user()->can('jobrequest_delete')) {
            $resourceNeo['bulkActions']['bulk_delete'] = [];
        }
        if(\Auth::user()->can('jobrequest_export')) {
            $resourceNeo['bulkActions']['csvExport'] = [];
        }

        $allUsers = User::with('roles')->get();
        $allcoordinatorAll = [['id' => '','label' => 'Select CRDR']];
        $allUserAll = [['id' => '','label' => 'Last Edited']];
        foreach ($allUsers as $key => $usr) {
            if($usr->role_name == $coordinatorName) {
                $allcoordinatorAll[] = ['id' => $usr->id,'label' => $usr->name];
            }
            $allUserAll[] = ['id' => $usr->id, 'label' => $usr->name];
        }
        $resourceNeo['allcordinator'] = $allUserAll;
        $resourceNeo['allcordinator'][0]['label'] = 'No CRDR';

        return Inertia::render('Admin/JobRequestIndexView', ['public_holidays' => $public_holidays,'moduledatas' => $jobrequests, 'resourceNeo' => $resourceNeo])->table(function (InertiaTable $table) use ($allcoordinatorAll, $allUserAll) {
            $crdr1 = $crdr2 = $allUserAll;
            $crdr1[0]['label'] = 'Select CRDR1';
            $crdr2[0]['label'] = 'Select CRDR2';
            $NextColKey = '';
            $editperm = false;
            if((\Auth::user()->can('all')) || \Auth::user()->can('jobrequest_edit')) {
                $editperm = true;
            }

            $table->withGlobalSearch()
            ->column(label: 'SN', extra:['info' => 'serial number order in excel (historic)','colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])

            ->column('created_at', 'Timestamp', searchable: true, sortable: true, extra:['type' => 'datePicker','showhide' => ['date','time'],'bg' => 'bg-customSlate','colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
            ->column(label: 'Date', hidden:true, extra:['hidden' => true,'colKey' => ($NextColKey = Helper::NextColKey($NextColKey)), 'width' => '60px'])
            ->column(label: 'Time', hidden:true, extra:['hidden' => true,'colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
            ->column('s_full_name', 'S.Fullname', sortable: true, extra:['width' => '100px', 'showhide' => ['salut','full_name'],'bg' => 'bg-customGrey','info' => 'Combined Salutation and FullName','colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
            ->column('salut', 'Sal.', searchable: true, sortable: true, hidden:true, extra:['width' => '65px','info' => 'Salutation','hidden' => true,'colKey' => ($NextColKey = Helper::NextColKey($NextColKey)), 'editable' => $editperm, 'freetext' => 'input', 'type' => 'select', 'options' => [['id' => '','label' => 'Salutation'],['id' => 'Ms.','label' => 'Ms.'],['id' => 'Mr.','label' => 'Mr.']]  ])
            ->column('full_name', 'FullName', hidden:true, searchable: true, sortable: true, extra:['width' => '105px','hidden' => true, 'editable' => $editperm,'colKey' => ($NextColKey = Helper::NextColKey($NextColKey)) ])
            ->column('email', 'Email', searchable: true, sortable: true, extra:['width' => '75px', 'editable' => $editperm,'info' => 'click to see email','colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
            ->column('whts_no', 'WA', searchable: true, sortable: true, extra:['width' => '120px','info' => 'Whatsup Number', 'editable' => $editperm,'info' => 'WhatsApp number','colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
            ->column('int_c_bb', 'Int C_BB', searchable: true, sortable: true, extra:['info' => 'Internal Comments from MEIDE BB Staff','editable' => $editperm, 'type' => 'textarea','bg' => 'bg-customYellow','colKey' => ($NextColKey = Helper::NextColKey($NextColKey)), 'width' => '270px'])
            ->column('crdr1_name', 'CRDR1', searchable: true, sortable: true, extra:['showhide' => ['CRDR1_reason'], 'type' => 'select','options' => $crdr1, 'bg' => 'bg-customOrange','info' => 'Coordinator in charge','colKey' => ($NextColKey = Helper::NextColKey($NextColKey)),'width' => '110px','clEventTrOn' => 'crdr1asn_'])
            ->column('CRDR1_reason', 'CRDR1 Reason', searchable: true, sortable: true, hidden:true, extra:['hidden' => true, 'editable' => $editperm, 'bg' => 'bg-customOrange','colKey' => ($NextColKey = Helper::NextColKey($NextColKey)),'width' => '110px'])
            ->column('crdr2_name', 'CRDR2', searchable: true, sortable: true, extra:['showhide' => ['CRDR2_reason'],'type' => 'select','options' => $crdr2, 'bg' => 'bg-customOrange','info' => 'Coordinator 2IC','colKey' => ($NextColKey = Helper::NextColKey($NextColKey)),'width' => '110px','clEventTrOn' => 'crdr2asn_'])
            ->column('CRDR2_reason', 'CRDR2 Reason', searchable: true, sortable: true, hidden:true, extra:['hidden' => true, 'editable' => $editperm, 'bg' => 'bg-customOrange','colKey' => ($NextColKey = Helper::NextColKey($NextColKey)),'width' => '110px'])
            ->column('BStatus', 'BStatus', searchable: true, sortable: true, extra:['info' => 'BStatus: LATEST on the left EG. Latest is 0 so 0/3/2 (9=cannot connect x1, 99=cannot connect x2, 999=cannot connectx3, 0=rejected/cancelled, 3= called and await 98$, 1=sent messages, 2=called but need clarifications <eg re-call, discussions>, 4= $98 paid) // dup=duplicate entry, str=sitter and not client wront entry, tst=for testing only, err=other errors.', 'shoinfoonedit' => true, 'showhide' => ['rejectReason','MBJNo'],'editable' => $editperm, 'bg' => 'bg-customYellow','colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
            ->column('MBJNo', 'AOR', searchable: true, sortable: true, hidden:true, extra:['hidden' => true, 'info' => 'Accepted Or Rejected', 'editable' => (Auth::user()->role_name == 'super-admin'),'colKey' => ($NextColKey = Helper::NextColKey($NextColKey)),'width' => '110px'])
            ->column('rejectReason', 'Reject Reason', searchable: true, sortable: true, hidden:true, extra:['hidden' => true, 'editable' => $editperm,'colKey' => ($NextColKey = Helper::NextColKey($NextColKey)),'width' => '110px'])
            ->column('start_date', 'Sdate', searchable: true, sortable: true, extra:['width' => '95px','info' => 'The Start Date of the entire babysitting required','type' => 'datePicker', 'editable' => $editperm, 'freetext' => 'input', 'colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
            ->column('date_flex', 'Schedule Flex', searchable: true, sortable: true, extra:['width' => '105px','type' => 'select', 'info' => 'Is your start date flexible? Yes, my start date is flexible and can be adjusted earlier or later.
            ', 'editable' => $editperm, 'freetext' => 'input', 'colKey' => ($NextColKey = Helper::NextColKey($NextColKey)),'options' => [['id' => '','label' => 'Schedule Flex'],['id' => 'Yes','label' => 'Yes'],['id' => 'No','label' => 'No']]])
            ->column('job_schdl', 'Duration', searchable: true, sortable: true, extra:[ 'editable' => $editperm,'info' => 'Duration requested for BB','type' => 'textarea','colKey' => ($NextColKey = Helper::NextColKey($NextColKey)),'width' => '110px'])
            ->column('no_of_kids', 'Kids', searchable: true, sortable: true, extra:['width' => '65px','editable' => $editperm,'info' => 'how many kids requiring BB','colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
            ->column('dob_kid', 'BBday', searchable: true, sortable: true, extra:['width' => '95px','info' => 'bday of the only kid requiring BB','type' => 'datePicker', 'editable' => $editperm, 'freetext' => 'input', 'colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
            ->column('dob_young_kid', 'BBday Young', searchable: true, sortable: true, extra:['width' => '95px','info' => 'multiple kids requiring BB (youngest kid)','type' => 'datePicker', 'editable' => $editperm, 'freetext' => 'input', 'colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
            ->column('dob_old_kid', 'BBday Old', searchable: true, sortable: true, extra:['width' => '95px','info' => 'multiple kids requiring BB (eldest kid)','type' => 'datePicker', 'editable' => $editperm, 'freetext' => 'input', 'colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
            ->column('other_info', 'Job Details', searchable: true, sortable: true, extra:['type' => 'textarea', 'editable' => $editperm,'info' => 'If there are further details not specified above, let us know here:','colKey' => ($NextColKey = Helper::NextColKey($NextColKey)),'width' => '150px'])
            ->column('job_loc', 'LOC', searchable: true, sortable: true, extra:['width' => '65px','info' => 'Location Requested for BB', 'editable' => $editperm, 'freetext' => 'textarea', 'type' => 'select', 'options' => [['id' => '','label' => 'Location'],['id' => 'At Babysitter House Only','label' => 'At Babysitter House Only'],['id' => 'At My House Only','label' => 'At My House Only'],['id' => 'Considering Both','label' => 'Considering Both'],['id' => 'Outdoors/Hotel','label' => 'Outdoors/Hotel']],'colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
            ->column('job_loc_addr', 'Address1', searchable: true, sortable: true, extra:['width' => '110px','editable' => $editperm,'info' => 'Address for OWN house','colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
            ->column('job_loc_addr_unit', 'Unit1', searchable: true, sortable: true, extra:['width' => '110px','editable' => $editperm,'info' => 'Unit Number for OWN house','colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
            ->column('job_loc_addr2', 'Address2', searchable: true, sortable: true, extra:['width' => '110px','editable' => $editperm,'info' => 'Address for OWN house (But OK at BB House)','colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
            ->column('job_loc_addr_unit2', 'Unit2 ', searchable: true, sortable: true, extra:['width' => '110px','editable' => $editperm,'info' => 'Unit Number for OWN house (But OK at BB House)','colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
            ->column('pets', 'Pets', searchable: true, sortable: true, extra:['width' => '55px','showhide' => ['pet_muzzle','pet_description'], 'type' => 'select','options' => [['id' => '','label' => 'Pets'],['id' => 'Yes','label' => 'Yes'],['id' => 'No','label' => 'No']], 'editable' => $editperm, 'freetext' => 'input', 'info' => 'Do you have pet(s) at home?YesNo','colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
            ->column('pet_muzzle', 'Muzzle', searchable: true, sortable: true, hidden:true, extra:['width' => '95px','hidden' => true, 'editable' => $editperm, 'freetext' => 'textarea', 'type' => 'checkbox', 'options' => [['id' => 'No, strictly free roaming.','label' => 'No, strictly free roaming.'],['id' => 'Can be isolated or kenneled.','label' => 'Can be isolated or kenneled.'],['id' => 'Can be muzzled.','label' => 'Can be muzzled.']], 'info' => 'Can your pet(s) be isolated, kenneled or muzzled?','colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
            ->column('pet_description', 'Petspec', searchable: true, sortable: true, hidden:true, extra:['hidden' => true, 'editable' => $editperm,'info' => 'Specify your pet details','colKey' => ($NextColKey = Helper::NextColKey($NextColKey)),'width' => '115px'])
            ->column('job_alone', 'Alone', searchable: true, sortable: true, extra:['width' => '65px','showhide' => ['job_whoelse'], 'type' => 'select','options' => [['id' => '','label' => 'Job Alone'],['id' => 'Yes','label' => 'Yes'],['id' => 'No','label' => 'No']], 'editable' => $editperm, 'freetext' => 'input', 'info' => 'Do you require babysitter to babysit alone at the residence?YesNo','colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
            ->column('job_whoelse', 'Whoelse', searchable: true, sortable: true, hidden:true, extra:['hidden' => true, 'editable' => $editperm,'info' => 'Who else may be at the residence with the babysitter?','colKey' => ($NextColKey = Helper::NextColKey($NextColKey)),'width' => '115px'])
            ->column('job_restriction', 'Crit', searchable: true, sortable: true, extra:['width' => '50px','showhide' => ['job_crit'], 'type' => 'select','options' => [['id' => '','label' => 'Restriction'],['id' => 'Yes','label' => 'Yes'],['id' => 'No','label' => 'No'], ['id' => '','label' => 'Leave Blank']], 'editable' => $editperm, 'ffreetext' => 'input', 'info' => 'Any restrictions or criteria for your babysitter? YesNo', 'colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
            ->column('job_crit', 'Restriction', searchable: true, sortable: true, hidden:true, extra:['hidden' => true, 'editable' => $editperm, 'type' => 'textarea','info' => 'Please specify your criteria or restrictions ','colKey' => ($NextColKey = Helper::NextColKey($NextColKey)),'width' => '115px'])
            ->column('expc_budget', 'Budget', searchable: true, sortable: true, extra:['showhide' => ['pay_pref'], 'editable' => $editperm,'info' => 'Please advise us about your budget (per month/week/day/hour):','colKey' => ($NextColKey = Helper::NextColKey($NextColKey)),'width' => '80px'])
            ->column('pay_pref', 'Schedules', searchable: true, sortable: true, hidden:true, extra:['width' => '110px','type' => 'checkbox', 'options' => [['id' => 'Pay Per Hour','label' => 'Pay Per Hour'],['id' => 'Pay Per Day','label' => 'Pay Per Day'],['id' => 'Pay Per Week','label' => 'Pay Per Week']], 'hidden' => true, 'editable' => $editperm, 'freetext' => 'textarea', 'info' => 'Select your preferred payment schedule:','colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
            ->column('budget_flex', 'Budgetneg', searchable: true, sortable: true, extra:[ 'editable' => $editperm,'info' => 'Is your budget flexible?','colKey' => ($NextColKey = Helper::NextColKey($NextColKey)),'width' => '110px'])
            ->column('last_edited', 'LastEdit', searchable: true, sortable: true, extra:['type' => 'select', 'options' => $allUserAll,'colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
            ->column('remark', 'Remark', searchable: true, sortable: true, extra:['type' => 'textarea','editable' => $editperm,'info' => 'Any other remarks','colKey' => ($NextColKey = Helper::NextColKey($NextColKey)),'width' => '270px'])


            ->dateFilter(key: 'created_at_start', label: 'Entry Date From')
            ->dateFilter(key: 'created_at_end', label: 'Entry Date To')
            ->dateFilter(key: 'start_date_start', label: 'Entry Date From')
            ->dateFilter(key: 'start_date_end', label: 'Entry Date To')
            ->dateFilter(key: 'dob_kid_start', label: 'Entry Date From')
            ->dateFilter(key: 'dob_kid_end', label: 'Entry Date To')
            ->dateFilter(key: 'dob_young_kid_start', label: 'Entry Date From')
            ->dateFilter(key: 'dob_young_kid_end', label: 'Entry Date To')
            ->dateFilter(key: 'dob_old_kid_start', label: 'Entry Date From')
            ->dateFilter(key: 'dob_old_kid_end', label: 'Entry Date To')
            ;

            if(!(\Auth::user()->can('all')) && \Auth::user()->can('jobrequest_DisViewAllRow')) {
                $table->perPageOptions([10,15,30,50,100]);
            } else {
                $table->perPageOptions([10,15,30,50,100, 10000]);
            }
            $coordinatorName =  config('app.coordinator.name', 'Coordinator');
            if(Auth::user()->role_name != $coordinatorName) {
                $table->column('status_s2c', 'S2C', searchable: true, sortable: true, extra:['info' => 'historic excel sheet to contacts record','width' => '60px', 'colKey' => ($NextColKey = Helper::NextColKey($NextColKey))]);
            }
            $table->column('id', 'Row Ref', searchable: true, hidden:true, extra:['info' => 'Row Ref number', 'colKey' => ($NextColKey = Helper::NextColKey($NextColKey))]);
            $table->column(label: 'Actions', extra:['colKey' => ($NextColKey = Helper::NextColKey($NextColKey))]);
        });

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JobRequest $jobrequest)
    {
        $uname = $jobrequest->id;
        $jobrequest->delete();
        \ActivityLog::add(['action' => 'deleted','module' => 'jobrequest','data_key' => $uname, 'data_ref' => $jobrequest->id,]);
        return redirect()->back()->with(['message' => 'JobRequest Deleted !!','msg_type' => 'warning']);
    }

    public function fieldUpdate(Request $request)
    {
        $notAllowedBlank = ['email','whts_no'];
        $saved_value = $request->value;
        if(in_array($request->key, $notAllowedBlank) &&  $saved_value == '') {
            return redirect()->back()->with(['message' => 'Value can not be blank !!','msg_type' => 'danger']);
        } elseif($request->key == 'BStatus') {
            $verr = false;
            if($saved_value) {
                $validvaleu = ['9','99','999','0','3','1','2','4','dup','str','tst','err'];
                $exvalue = explode('/', $saved_value);
                foreach ($exvalue as $exval) {
                    if(!in_array($exval, $validvaleu)) {
                        $verr = true;
                        break;
                    }
                }
            }
            if($verr) {
                return redirect()->back()->with(['message' => 'BB Status Value not valid !!','msg_type' => 'danger']);
            }
        } elseif($request->key == 'email') {
            if (!filter_var($saved_value, FILTER_VALIDATE_EMAIL)) {
                return redirect()->back()->with(['message' => 'Email format not valid !!','msg_type' => 'danger']);
            }
        } elseif($request->key == 'no_of_kids') {
            if($request->value == '') {
                $request->value = null;
            } elseif(!preg_match('/^[0-9]+$/', $saved_value)) {
                return redirect()->back()->with(['message' => 'Numbers of kid value not valid !!','msg_type' => 'danger']);
            } elseif($saved_value > 9999) {
                return redirect()->back()->with(['message' => 'Numbers of kid value not valid !!','msg_type' => 'danger']);
            }
        } elseif($request->key == 'pet_muzzle' || $request->key == 'pay_pref') {
            $saved_value = is_array($request->value) ? implode(PHP_EOL, $request->value) : $request->value;
        } elseif($request->key == 'start_date' || $request->key == 'dob_kid' || $request->key == 'dob_young_kid' || $request->key == 'dob_old_kid') {
            if($request->value == '') {
                $request->value = null;
            } elseif (!(preg_match("/^[0-9]{4}-(0?[1-9]|1[0-2])-(0?[1-9]|[1-2][0-9]|3[0-1])$/", $request->value))) {
                return redirect()->back()->with(['message' => 'Wrong Format! Please key in format YYYY-MM-DD','msg_type' => 'danger']);
            }
        }



        $jobrequest = JobRequest::find($request->id);
        $prev_value = $jobrequest->{$request->key};
        $jobrequest->{$request->key} = $saved_value;
        $coordinatorName =  config('app.coordinator.name', 'Coordinator');
        if($request->key == 'BStatus' && empty($jobrequest->CRDR1) && Auth::user()->role_name == $coordinatorName) {
            $jobrequest->CRDR1 = Auth::user()->id;
        }
        $isDirty = false;
        if($jobrequest->isDirty()) {
            $jobrequest->last_edited = Auth::user()->id;
            $jobrequest->save();
            $isDirty = true;
        }

        if($isDirty) {
            $uname = $request->key;
            \ActivityLog::add(['action' => 'updated','module' => 'jobrequest','data_key' => $uname, 'data_id' => $request->id, 'data_ref' => $request->ref, 'data_val' => $prev_value]);
            //broadcast(new MedideNotification($request->ref . ' Updated By ' . Auth::user()->name, 'jobrequest'))->toOthers();
            $res = ['message' => 'Updated Successfully.','msg_type' => 'info'];
        } else {
            $res = ['message' => 'No Value  Updated in Job Request .','msg_type' => 'warning'];
        }
        return redirect()->back()->with($res);
    }

    public function assignCRDR(Request $request)
    {
        $jobrequest = JobRequest::find($request->id);

        //if(!$request->value) {
        //    return redirect()->back()->with(['message' => 'No Coordinator selected !!','warning' => 'info']);
        //}

        $prev_value = $jobrequest->{$request->key};
        $jobrequest->{$request->key} = $request->value;
        if($request->reason != '') {
            $prev_value2 = $jobrequest->{$request->key . '_reason'};
            $jobrequest->{$request->key . '_reason'} = $request->reason;
        }
        if($jobrequest->isDirty()) {
            $jobrequest->last_edited = Auth::user()->id;
            $jobrequest->save();

            $uname = $request->key;
            \ActivityLog::add(['action' => 'updated','module' => 'jobrequest','data_key' => $uname,'data_id' => $request->id,'data_val' => $prev_value, 'data_ref' => $request->ref, ]);
            if($request->reason && !empty($prev_value2)) {
                \ActivityLog::add(['action' => 'updated','module' => 'jobrequest','data_key' => $request->key . '_reason','data_id' => $request->id, 'data_val' => $prev_value2]);
            }
        } else {
            $res = ['message' => 'No CRDR  Updated.','msg_type' => 'warning'];
        }
        return redirect()->back()->with(['message' => $request->key . ' sassigned !!','msg_type' => 'info']);
    }

    /**
     * replicate.
     */
    public function replicate($id)
    {
        $jobrequest = JobRequest::find($id);
        for($i = 0; $i < request('nx');$i++) {
            $newJobRequest = $jobrequest->replicate();
            $newJobRequest->created_at = Carbon::now();
            $newJobRequest->BStatus = null;
            $newJobRequest->rejectReason = null;
            $newJobRequest->MBJNo = null;
            $newJobRequest->last_edited = null;
            $newJobRequest->CRDR2 = null;
            $newJobRequest->CRDR2_reason = null;
            $newJobRequest->CRDR1 = null;
            $newJobRequest->status_s2c = null;

            $newJobRequest->save();
        }
        $uname = $newJobRequest->id;
        \ActivityLog::add(['action' => 'duplicated','module' => 'jobrequest','data_key' => $uname, 'data_ref' => $uname,]);
        return redirect()->back()->with(['message' => 'Selected JobRequest Duplicated !!','msg_type' => 'info']);
    }

    /**
    * Bulk replicate.
    */
    public function bulkDuplicate()
    {
        $allDuplicate = [];
        foreach (request('ids') as  $id) {
            $jobrequest = JobRequest::find($id);
            for($i = 0; $i < request('nx');$i++) {
                $newJobRequest = $jobrequest->replicate();
                $newJobRequest->created_at = Carbon::now();
                $newJobRequest->BStatus = null;
                $newJobRequest->rejectReason = null;
                $newJobRequest->MBJNo = null;
                $newJobRequest->last_edited = null;
                $newJobRequest->CRDR2 = null;
                $newJobRequest->CRDR2_reason = null;
                $newJobRequest->CRDR1 = null;
                $newJobRequest->CRDR1_reason = null;
                $newJobRequest->CRDR1_reason = null;
                $newJobRequest->status_s2c = null;
                $newJobRequest->save();
                $allDuplicate[] = $id;
            }
        }
        $uname = (count($allDuplicate) > 10) ? 'Many' : $uname = implode(',', $allDuplicate);
        \ActivityLog::add(['action' => 'duplicated', 'module' => 'jobrequest', 'data_key' => $uname, 'data_ref' => $uname,]);
        return redirect()->back()->with(['message' => 'Selected BB Job Request(s) Duplicated Successfully! Check the top of the table for it!.','msg_type' => 'info']);
    }

    /**
     * bulk delete.
     */
    public function bulkDestroy()
    {
        JobRequest::whereIn('id', request('ids'))->delete();
        $uname = (count(request('ids')) > 10) ? 'Many' : $uname = implode(',', request('ids'));
        \ActivityLog::add(['action' => 'deleted','module' => 'jobrequest','data_key' => $uname, 'data_ref' => $uname,]);
        return redirect()->back()->with(['message' => 'Selected JobRequest Deleted !!','msg_type' => 'warning']);
    }

    /**
     *  bulkAccept.
     */
    public function bulkAccept()
    {
        $maxMbj = Setting::where('slug', 'maxMbj')->firstOrFail();
        $lastMBJNo = $maxMbj->value;
        $allgenMBJ = [];
        $g_contacts = [];
        $coordinatorName =  config('app.coordinator.name', 'Coordinator');
        $duplifound = [];
        $alreadydone = [];
        foreach (request('ids') as  $id) {
            $jobrequest = JobRequest::find($id);
            $wht8_st = substr($jobrequest->whts_no, 0, 8);
            $wht8_en = substr($jobrequest->whts_no, -8);
            $wht5_st = substr($jobrequest->whts_no, 0, 5);
            $wht5_en = substr($jobrequest->whts_no, -5);
            $email = $jobrequest->email;
            $fnm = $jobrequest->full_name;
            $addr = $jobrequest->job_loc_addr;
            $addrunit = $jobrequest->job_loc_addr_unit;
            $exvalue = explode('/', $jobrequest->BStatus);
            if(!$jobrequest->MBJNo && $exvalue[0] != '0') {
                //check duplicacy
                $dplQuerycount = 0;
                if(request('ignore') != true) {

                    $dplQuery = DB::table('job_searches')->select("job_searches.id", "job_searches.salut", "job_searches.full_name", "job_searches.jobMBJ", "job_searches.job_email", "job_searches.job_addr", "job_searches.job_addr_unit", "job_searches.job_phn", "job_searches.cn")->leftJoin('other_phones', 'other_phones.jobId', 'job_searches.id', 'left')->where('job_email', $email)->orWhere('job_phn', 'LIKE', $wht8_st . '%')->orWhere('job_phn', 'LIKE', '%' . $wht8_en)->orWhere('other_phones.phone', 'LIKE', $wht8_st . '%')->orWhere('other_phones.phone', 'LIKE', '%' . $wht8_en);
                    if(!empty($addr)) {
                        $dplQuery = $dplQuery->orWhere(function ($query) use ($addr, $addrunit) {$query->where('job_addr', $addr)->where('job_addr_unit', $addrunit);});
                    }
                    if(!empty($fnm)) {
                        $dplQuery = $dplQuery->orWhere(function ($query) use ($fnm, $wht5_st, $wht5_en) {$query->where('full_name', $fnm)->where(function ($query) use ($wht5_st, $wht5_en) {$query->where('job_phn', 'LIKE', $wht5_st . '%')->orWhere('job_phn', 'LIKE', '%' . $wht5_en)->orWhere('other_phones.phone', 'LIKE', $wht5_st . '%')->orWhere('other_phones.phone', 'LIKE', '%' . $wht5_st);});});
                    }
                    $dplQuery = $dplQuery->groupBy("job_searches.id")->get();
                    $dplQuerycount = $dplQuery->count();
                }
                if($dplQuerycount > 0) {
                    if(count(request('ids')) == 1) {
                        return ['message' => 'Suspected duplicate existing entries', 'msg_type' => 'danger', 'rdata' => $dplQuery, 'reqid' => $id];
                    } else {
                        $duplifound[] = $fnm;
                    }

                } else {
                    $nextMBJNo = \Helper::NextMBJ($lastMBJNo);
                    $jobrequest->MBJNo = $nextMBJNo;
                    if(empty($jobrequest->CRDR1) && Auth::user()->role_name == $coordinatorName) {
                        $jobrequest->CRDR1 = Auth::user()->id;
                    }
                    $jobrequest->last_edited = Auth::user()->id;
                    $jobrequest->save();
                    Setting::where('slug', 'maxMbj')->update(['value' => $nextMBJNo]);
                    $allgenMBJ[] = $nextMBJNo;
                    $lastMBJNo = $nextMBJNo;
                    if($jobrequest->status_s2c == '') {
                        $g_contacts[] = ['id' => $jobrequest->id, 'email' => $jobrequest->email, 'name' => $jobrequest->full_name, 'phn' => $jobrequest->whts_no];
                    }
                    if(request('smbjid')) {
                        $jobsearch = Jobsearch::find(request('smbjid'));
                        $nextCN = $jobsearch->cn;
                    } else {
                        $maxCN = Setting::where('slug', 'maxCn')->firstOrFail();
                        $lastCN = $maxCN->value;
                        $nextCN = \Helper::NextCN($lastCN);
                        Setting::where('slug', 'maxCn')->update(['value' => $nextCN]);
                    }

                    $job_loc = ($jobrequest->job_loc == 'At Babysitter House Only' ? 'bbh' : ($jobrequest->job_loc == 'At My House Only' ? 'oh' : ($jobrequest->job_loc == 'Outdoors/Hotel' ? 'travelh' : '?h')));


                    $jsrch = [
                        'jobReqId' => $jobrequest->id,
                        'jobMBJ' => $jobrequest->MBJNo,
                        'salut' => $jobrequest->salut,
                        'full_name' => $jobrequest->full_name,
                        'cn' => $nextCN,
                        'job_email' => $jobrequest->email,
                        'job_addr' => empty($jobrequest->job_loc_addr2) ? $jobrequest->job_loc_addr : $jobrequest->job_loc_addr2 ,
                        'job_addr_unit' => empty($jobrequest->job_loc_addr2) ? $jobrequest->job_loc_addr_unit : $jobrequest->job_loc_addr_unit2,
                        'job_phn' => $jobrequest->whts_no,
                        'CRDR1' => $jobrequest->CRDR1,
                        'CRDR2' => $jobrequest->CRDR2,
                        'start_date' => $jobrequest->start_date,
                        'comments' => $jobrequest->int_c_bb,
                        'duration' => $jobrequest->duration,
                        'job_loc' => $job_loc,
                        'no_of_kids' => $jobrequest->no_of_kids,
                        'ykid' => $jobrequest->no_of_kids == 1 ? $jobrequest->dob_kid : $jobrequest->dob_young_kid,
                        'okid' => $jobrequest->no_of_kids == 1 ? null : $jobrequest->dob_old_kid,
                        'job_alone' => $jobrequest->job_alone,
                        'job_whoelse' => $jobrequest->job_whoelse,
                        'pets' => $jobrequest->pets,
                        'pet_muzzle' => $jobrequest->pet_muzzle,
                        'pet_description' => $jobrequest->pet_description,
                        'crit_restriction' => $jobrequest->job_restriction . ' ' . $jobrequest->job_crit ,
                        'budget' => $jobrequest->expc_budget,
                    ];
                    $job_search = JobSearch::create($jsrch);
                    if(!empty($jobrequest->job_loc_addr2)) {
                        OtherAddrress::create(['jobId' => $job_search->id, 'address' => $jobrequest->job_loc_addr, 'aunit' => $jobrequest->job_loc_addr_unit]);
                    }
                    if(request('smbjid')) {
                        $job_search->salut = $jobsearch->salut;
                        $job_search->full_name = $jobsearch->full_name;
                        $job_search->job_email = $jobsearch->job_email;
                        $job_search->reg_bank_detail = $jobsearch->reg_bank_detail;
                        $job_search->save();

                        $exist_other_phones = OtherPhone::where('jobId', $jobsearch->id)->get();
                        $temp_ar = [];
                        foreach ($exist_other_phones as $exist_other_phone) {
                            $temp_ar[] = ['created_at' => Carbon::now(),'updated_at' =>  Carbon::now(),'jobId' => $job_search->id, 'phone' => $exist_other_phone->phone,'label' => $exist_other_phone->label];
                        }
                        OtherPhone::insert($temp_ar);
                        $temp_ar = [];
                        $exist_other_address = OtherAddrress::where('jobId', $jobsearch->id)->get();
                        foreach ($exist_other_address as $exist_other_addr) {
                            if($exist_other_addr->address != $jobrequest->job_loc_addr || $exist_other_addr->aunit != $jobrequest->job_loc_addr_unit) {
                                $temp_ar[] = ['created_at' => Carbon::now(),'updated_at' =>  Carbon::now(),'jobId' => $job_search->id, 'address' => $exist_other_addr->address, 'aunit' => $exist_other_addr->aunit, 'label' => $exist_other_addr->label];
                            }
                        }
                        OtherAddrress::insert($temp_ar);

                    }
                }

            } else {
                $alreadydone[] = $fnm;
            }
        }
        if(!1 && config('app.google_api.contact_post') && count($g_contacts) > 0) {
            $googleServices = new GoogleApiPeopleService();
            $response_contacts = $googleServices->batchCreateContact($g_contacts);
            foreach ($response_contacts as $key => $g_contact) {
                $jobrequest = JobRequest::find($g_contact['id']);
                $jobrequest->status_s2c = $g_contact['rec_id'];
                $jobrequest->save();
            }
        }
        if(count($allgenMBJ) > 0) {
            $uname = (count($allgenMBJ) > 10) ? 'Many' : $uname = implode(',', $allgenMBJ);
            $uname2 = (count(request('ids')) > 10) ? 'Many' : $uname = implode(',', request('ids'));
            \ActivityLog::add(['action' => 'accepted','module' => 'jobrequest','data_key' => $uname, 'data_ref' => $uname2,]);
        }
        $mtp = 'warning';
        if(count($allgenMBJ) > 0 && count($duplifound) == 0 && count($alreadydone) == 0) {
            $mtp = 'success';
        } elseif(count($allgenMBJ) == 0 && count($duplifound) == 0 && count($alreadydone) > 0) {
            $mtp = 'danger';
        }

        return ['message' => (count($allgenMBJ) > 0 ? '<span class="text-blue-600">Selected JobRequest Accepted !! ' . implode(', ', $allgenMBJ) . '</span><br>' : '') . (count($duplifound) > 0 ? '<span class="text-red-600"> Some duplicate found: (' . implode(', ', $duplifound) . ') do it one by one' . '</span><br>' : '') . (count($alreadydone) > 0 ? ' Some JobRequest Already Accepted or Rejected !!: (' . implode(', ', $alreadydone) : ''),'msg_type' => $mtp];

    }

    public function bulkReject()
    {
        $allgenMBJ = [];
        $coordinatorName =  config('app.coordinator.name', 'Coordinator');
        foreach (request('ids') as  $id) {
            $jobrequest = JobRequest::find($id);
            if(!$jobrequest->MBJNo) {
                $exvalue = explode('/', $jobrequest->BStatus);
                if($exvalue[0] != '0') {
                    $jobrequest->BStatus = empty($jobrequest->BStatus) ? 0 : '0/' . $jobrequest->BStatus;
                    $jobrequest->rejectReason = request('rejectReason');
                    $jobrequest->MBJNo = 'REJ';
                    if(empty($jobrequest->CRDR1) && Auth::user()->role_name == $coordinatorName) {
                        $jobrequest->CRDR1 = Auth::user()->id;
                    }
                    $jobrequest->last_edited = Auth::user()->id;
                    $jobrequest->save();
                    $allgenMBJ[] = $jobrequest->id;
                }
            }
        }
        if(count($allgenMBJ) > 0) {
            $uname = (count($allgenMBJ) > 10) ? 'Many' : $uname = implode(',', $allgenMBJ);
            \ActivityLog::add(['action' => 'rejected','module' => 'jobrequest', 'data_key' => $uname, 'data_ref' => $uname,]);
            return redirect()->back()->with(['message' => 'Selected JobRequest Rejected !!','msg_type' => 'warning']);
        } else {
            return redirect()->back()->with(['message' => 'Selected JobRequest not qualified to be Rejected !!','msg_type' => 'danger']);
        }

    }

    public function export()
    {
        if(\Auth::user()->can('jobrequest_listAll')) {
            $rows = JobRequest::select('job_requests.*', 'u1.name AS crdr1_name', 'u2.name AS crdr2_name', 'u3.name AS last_edit_name')->leftJoin('users AS u1', 'u1.id', 'job_requests.crdr1', 'left')->leftJoin('users  AS u2', 'u2.id', 'job_requests.crdr2', 'left')->leftJoin('users  AS u3', 'u3.id', 'job_requests.last_edited', 'left')->orderBy('created_at', 'DESC')->get();
        } else {
            $rows = JobRequest::select('job_requests.*', 'u1.name AS crdr1_name', 'u2.name AS crdr2_name', 'u3.name AS last_edit_name')->leftJoin('users AS u1', 'u1.id', 'job_requests.crdr1', 'left')->leftJoin('users  AS u2', 'u2.id', 'job_requests.crdr2', 'left')->leftJoin('users  AS u3', 'u3.id', 'job_requests.last_edited', 'left')->where(function ($query) {$query->where('CRDR1', null)->orWhere('CRDR1', Auth::id())->orWhere('CRDR2', Auth::id());})->orderBy('created_at', 'DESC')->get();
        }

        $fileName = 'BBJobRequest.csv';

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = ['SN','Timestamp','Date','Time','S.Fullname','Sal.','FullName','Email','WA','Int C_BB','CRDR1','CRDR1 Reason','CRDR2','CRDR2 Reason','BStatus','AOR','Reject Reason','Sdate','Schedule Flex','Duration','Kids','BBday','BBday Young','BBday Old','Job Details','LOC','Address1','Unit1','Address2','Unit2','Pets','Muzzle','Petspec','Alone','Whoelse','Crit','Restriction','Budget','Schedules','Budgetneg','LastEdit','Remark','S2C','Row Ref'];

        $callback = function () use ($rows, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            $i = 1;
            foreach ($rows as $row) {
                $dtex = explode(' ', $row->created_at);
                fputcsv($file, [$i++, $row->created_at, $dtex[0], $dtex[1], $row->s_full_name, $row->salut, $row->full_name, $row->email, $row->whts_no, $row->int_c_bb, $row->crdr1_name, $row->CRDR1_reason, $row->crdr2_name, $row->CRDR2_reason, $row->BStatus, $row->MBJNo, $row->rejectReason, $row->start_date, $row->date_flex, $row->job_schdl, $row->no_of_kids, $row->dob_kid, $row->dob_young_kid, $row->dob_old_kid, $row->other_info, $row->job_loc, $row->job_loc_addr, $row->job_loc_addr_unit, $row->job_loc_addr2, $row->job_loc_addr_unit2, $row->pets, $row->pet_muzzle, $row->pet_description, $row->job_alone, $row->job_whoelse, $row->job_restriction, $row->job_crit, $row->expc_budget, $row->pay_pref, $row->budget_flex, $row->last_edit_name, $row->remark, $row->status_s2c, $row->id]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);

    }
    public function fieldData(Request $request){
        $fieldName = $request->fieldName ?? '';

        if(!$fieldName){
            return response()->json(['message' => 'At least one field is required !!', 'data' => []], 400);
        }
        
        $data = JobRequest::distinct()->pluck($fieldName)->toArray();
        $data = array_values(array_filter($data)); // filter data
        $result = array_map(function($element) { return ['id'=> $element, 'label' => $element];}, $data);
        return response()->json(['message' => 'Field Data shown successfully !!', 'data' => $result]);
        
    }

}
