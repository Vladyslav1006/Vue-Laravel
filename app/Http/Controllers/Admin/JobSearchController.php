<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Inertia\Inertia;
use App\Helpers\Helper;

use App\Models\Setting;
use App\Models\JobSearch;
use App\Models\OtherPhone;
use App\Models\OtherEmail;
use App\Models\OtherEty;
use App\Models\FamilyProof;
use Illuminate\Support\Str;

use Illuminate\Http\Request;
use App\Models\OtherAddrress;
use Illuminate\Support\Carbon;
use App\Events\MedideNotification;

use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use App\Http\Class\StringLengthSort;
use App\Services\GoogleApiPeopleService;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;
use App\Models\Publicholiday;


class JobSearchController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:jobsearch_list', ['only' => ['index', 'show']]);
        $this->middleware('can:jobsearch_delete', ['only' => ['destroy','bulkDestroy']]);
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
                    ->orWhere('job_searches.full_name', 'LIKE', "%{$value}%")
                    ->orWhere('job_searches.job_email', 'LIKE', "%{$value}%")
                    ->orWhere('job_searches.int_remark', 'LIKE', "%{$value}%")
                    ->orWhere('job_searches.comments', 'LIKE', "%{$value}%")

                    ->orWhere('job_searches.salut', 'LIKE', "{$value}%")
                    ->orWhere('job_searches.job_addr', 'LIKE', "{$value}%")
                    ->orWhere('job_searches.job_addr_unit', 'LIKE', "{$value}%")
                    ->orWhere('job_searches.payment_date', 'LIKE', "{$value}%")
                    ->orWhere('job_searches.reg_bank_detail', 'LIKE', "{$value}%")
                    ->orWhere('job_searches.fee_balance', 'LIKE', "{$value}%")
                    ->orWhere('job_searches.ety', 'LIKE', "{$value}%")
                    ->orWhere('job_searches.lcn', 'LIKE', "{$value}%")

                    ->orWhere('job_searches.ref_crit', 'LIKE', "{$value}%")
                    ->orWhere('job_searches.ref_amt', 'LIKE', "{$value}%")
                    ->orWhere('job_searches.s1', 'LIKE', "{$value}%")
                    ->orWhere('job_searches.s2', 'LIKE', "{$value}%")
                    ->orWhere('job_searches.s3', 'LIKE', "{$value}%")
                    ->orWhere('job_searches.s4', 'LIKE', "{$value}%")
                    ->orWhere('job_searches.s5', 'LIKE', "{$value}%")
                    ->orWhere('job_searches.ref_due_date', 'LIKE', "{$value}%")
                    ->orWhere('job_searches.start_date', 'LIKE', "{$value}%")

                    ->orWhere('job_searches.qoute_ate', 'LIKE', "{$value}%")
                    ->orWhere('job_searches.extra_charge', 'LIKE', "{$value}%")
                    ->orWhere('job_searches.duration', 'LIKE', "{$value}%")
                    ->orWhere('job_searches.daysreq', 'LIKE', "{$value}%")
                    ->orWhere('job_searches.freqreq', 'LIKE', "{$value}%")
                    ->orWhere('job_searches.job_loc', 'LIKE', "{$value}%")
                    ->orWhere('job_searches.bbplcradius', 'LIKE', "{$value}%")
                    ->orWhere('job_searches.bbplcregion', 'LIKE', "{$value}%")
                    ->orWhere('job_searches.bbplcequip', 'LIKE', "{$value}%")
                    ->orWhere('job_searches.bbplcequipd', 'LIKE', "{$value}%")
                    ->orWhere('job_searches.bbplcequipd', 'LIKE', "{$value}%")
                    ->orWhere('job_searches.ykid', 'LIKE', "{$value}%")
                    ->orWhere('job_searches.okid', 'LIKE', "{$value}%")
                    ->orWhere('job_searches.job_whoelse', 'LIKE', "{$value}%")
                    ->orWhere('job_searches.pet_muzzle', 'LIKE', "{$value}%")
                    ->orWhere('job_searches.pet_description', 'LIKE', "{$value}%")
                    ->orWhere('job_searches.crit_restriction', 'LIKE', "{$value}%")
                    ->orWhere('job_searches.mf', 'LIKE', "{$value}%")
                    ->orWhere('job_searches.CleanReq', 'LIKE', "{$value}%")
                    ->orWhere('job_searches.CookReq', 'LIKE', "{$value}%")
                    ->orWhere('job_searches.CookReqDe', 'LIKE', "{$value}%")
                    ->orWhere('job_searches.kidshealth', 'LIKE', "{$value}%")
                    ->orWhere('job_searches.familyworkcon', 'LIKE', "{$value}%")
                    ->orWhere('job_searches.budget', 'LIKE', "{$value}%")
                    ->orWhere('job_searches.pay_schdl', 'LIKE', "{$value}%")
                    ->orWhere('job_searches.phone_convo', 'LIKE', "{$value}%")

                    ->orWhere('albb.rank', 'LIKE', "{$value}%")
                    ->orWhere('bbapl.BUniqueID', 'LIKE', "{$value}%")
                    ->orWhere('albb.remark', 'LIKE', "{$value}%")

                    ->orWhere('job_searches.jobMBJ', 'LIKE', "{$value}%")
                    ->orWhere('job_searches.cn', $value)
                    ->orWhere('job_searches.job_phn', $value)
                    ->orWhere('job_searches.fee', $value)
                    ->orWhere('job_searches.feepaid', $value)
                    ->orWhere('job_searches.owner_paid', $value)
                    ->orWhere('job_searches.no_of_kids', $value)
                    ->orWhere('job_searches.job_alone', $value)
                    ->orWhere('job_searches.pets', $value)
                    ->orWhere('job_searches.invc', $value)
                    ->orWhere('job_searches.last_edited', $value);
                });
            });
        });
        $perPage = request()->query('perPage') ?? 10;
        if(\Auth::user()->can('jobsearch_listAll')) {
            $Query = JobSearch::select('job_searches.*', 'u1.name AS crdr1_name', 'u2.name AS crdr2_name', 'u3.name AS last_edit_name')
            ->selectRaw('GROUP_CONCAT(DISTINCT CONCAT("(",albb.rank,")(", bbapl.BUniqueID,")(",if(albb.remark!="",albb.remark,""),")" )  ORDER BY albb.rank SEPARATOR " /// ") as allctbbs')
            ->selectRaw('GROUP_CONCAT(bbapl.BUniqueID  ORDER BY albb.rank SEPARATOR "::") as bbsuniqids')
            ->selectRaw('GROUP_CONCAT(bbapl.whts_no  ORDER BY albb.rank SEPARATOR "::") as bbscontacts')
            ->selectRaw('GROUP_CONCAT(bbapl.full_name  ORDER BY albb.rank SEPARATOR "::") as fullnames')
            ->selectRaw('GROUP_CONCAT(
                        DISTINCT CONCAT(
                                IF(oemail.email IS NOT NULL, CONCAT("(", oemail.email, ")"), "-"),
                                IF(oemail.label IS NOT NULL, CONCAT("(", oemail.label, ")"), "-"),
                                IF(oemail.is_primary = 1, "(Primary)", "-")
                            )
                            ORDER BY oemail.is_primary DESC, oemail.id ASC SEPARATOR " /// "
                        ) AS allemails')
            ->selectRaw('GROUP_CONCAT(
                            DISTINCT CONCAT(
                                IF(oety.ety IS NOT NULL, CONCAT("(", oety.ety, ")"), "-"),
                                IF(oety.label IS NOT NULL, CONCAT("(", oety.label, ")"), "-"),
                                IF(oety.is_primary = 1, "(Primary)", "-")
                            )
                            ORDER BY oety.is_primary DESC, oety.id ASC SEPARATOR " /// "
                        ) AS alletys')
            ->selectRaw('GROUP_CONCAT(
                            DISTINCT CONCAT(
                                IF(pother.phone IS NOT NULL, CONCAT("(", pother.phone, ")"), "-"),
                                IF(pother.label IS NOT NULL, CONCAT("(", pother.label, ")"), "-"),
                                IF(pother.is_primary = 1, "(Primary)", "-")
                            )
                            ORDER BY pother.is_primary DESC, pother.id ASC SEPARATOR " /// "
                        ) AS allphones')
            ->selectRaw(' GROUP_CONCAT(
                            DISTINCT CONCAT(
                                IF(oadd.address IS NOT NULL, CONCAT("(", oadd.address, ")"), "-"),
                                IF(oadd.label IS NOT NULL, CONCAT("(", oadd.label, ")"), "-"),
                                IF(oadd.prime = 1, "(Primary)", "-"),
                                IF(oadd.aunit IS NOT NULL, CONCAT("(", oadd.aunit, ")"), "-")
                            )
                            ORDER BY oadd.prime DESC, oadd.id ASC SEPARATOR " /// "
                        ) AS alladdress')
            ->leftJoin('other_emails AS oemail', 'oemail.jobId', 'job_searches.id', 'left')->groupBy("job_searches.id")
            ->leftJoin('other_etys AS oety', 'oety.jobId', 'job_searches.id', 'left')
            ->leftJoin('other_phones AS pother', 'pother.jobId', 'job_searches.id', 'left')
            ->leftJoin('other_addrresses AS oadd', 'oadd.jobId', 'job_searches.id', 'left')
            ->leftJoin('users AS u1', 'u1.id', 'job_searches.crdr1', 'left')
            ->leftJoin('users  AS u2', 'u2.id', 'job_searches.crdr2', 'left')
            ->leftJoin('users  AS u3', 'u3.id', 'job_searches.last_edited', 'left')
            ->leftJoin('allocated_b_b_s  AS albb', 'albb.jobId', 'job_searches.id', 'left')
            ->leftJoin('bbapplicants  AS bbapl', 'bbapl.id', 'albb.baysitterId', 'left')
            ->groupBy("job_searches.id");
        } else {
            $Query = JobSearch::select('job_searches.*', 'u1.name AS crdr1_name', 'u2.name AS crdr2_name', 'u3.name AS last_edit_name')
            ->selectRaw('GROUP_CONCAT(DISTINCT CONCAT("(",albb.rank,")(", bbapl.BUniqueID,")(",if(albb.remark!="",albb.remark,""),")" )  ORDER BY albb.rank SEPARATOR " /// ") as allctbbs')
            ->selectRaw('GROUP_CONCAT(bbapl.BUniqueID  ORDER BY albb.rank SEPARATOR "::") as bbsuniqids')
            ->selectRaw('GROUP_CONCAT(bbapl.whts_no  ORDER BY albb.rank SEPARATOR "::") as bbscontacts')
            ->selectRaw('GROUP_CONCAT(bbapl.full_name  ORDER BY albb.rank SEPARATOR "::") as fullnames')
            ->selectRaw('GROUP_CONCAT(
                CONCAT(
                    IF(oemail.email IS NOT NULL, CONCAT("(", oemail.email, ")"), "-"),
                    IF(oemail.label IS NOT NULL, CONCAT("(", oemail.label, ")"), "-"),
                    IF(oemail.is_primary = 1, "(Primary)", "-")
                )
                ORDER BY oemail.is_primary DESC, oemail.id ASC SEPARATOR " /// "
            ) AS allemails')
            ->selectRaw('GROUP_CONCAT(
                CONCAT(
                    IF(oety.ety IS NOT NULL, CONCAT("(", oety.ety, ")"), "-"),
                    IF(oety.label IS NOT NULL, CONCAT("(", oety.label, ")"), "-"),
                    IF(oety.is_primary = 1, "(Primary)", "-")
                )
                ORDER BY oety.is_primary DESC, oety.id ASC SEPARATOR " /// "
            ) AS alletys')
            ->selectRaw('GROUP_CONCAT(
                CONCAT(
                    IF(pother.phone IS NOT NULL, CONCAT("(", pother.phone, ")"), "-"),
                    IF(pother.label IS NOT NULL, CONCAT("(", pother.label, ")"), "-"),
                    IF(pother.is_primary = 1, "(Primary)", "-")
                )
                ORDER BY pother.is_primary DESC, pother.id ASC SEPARATOR " /// "
            ) AS allphones')
            ->selectRaw(' GROUP_CONCAT(
                CONCAT(
                    IF(oadd.address IS NOT NULL, CONCAT("(", oadd.address, ")"), "-"),
                    IF(oadd.label IS NOT NULL, CONCAT("(", oadd.label, ")"), "-"),
                    IF(oadd.prime = 1, "(Primary)", ""),
                    IF(oadd.aunit IS NOT NULL, CONCAT("(", oadd.aunit, ")"), "-")
                )
                ORDER BY oadd.prime DESC, oadd.id ASC SEPARATOR " /// "
            ) AS alladdress')
            ->leftJoin('other_emails AS oemail', 'oemail.jobId', 'job_searches.id', 'left')
            ->leftJoin('other_etys AS oety', 'oety.jobId', 'job_searches.id', 'left')
            ->leftJoin('other_phones AS pother', 'pother.jobId', 'job_searches.id', 'left')
            ->leftJoin('other_addrresses AS oadd', 'oadd.jobId', 'job_searches.id', 'left')
            ->leftJoin('users AS u1', 'u1.id', 'job_searches.crdr1', 'left')
            ->leftJoin('users  AS u2', 'u2.id', 'job_searches.crdr2', 'left')
            ->leftJoin('users  AS u3', 'u3.id', 'job_searches.last_edited', 'left')
            ->leftJoin('allocated_b_b_s  AS albb', 'albb.jobId', 'job_searches.id', 'left')
            ->leftJoin('bbapplicants  AS bbapl', 'bbapl.id', 'albb.baysitterId', 'left')
            ->where(function ($query) {$query->where('CRDR1', null)->orWhere('CRDR1', Auth::id())
            ->orWhere('CRDR2', Auth::id());})->groupBy("job_searches.id");
        }
        $chained = QueryBuilder::for($Query)
        ->defaultSort('-jobMBJ')
        ->allowedSorts([AllowedSort::custom('jobMBJ', new StringLengthSort(), 'jobMBJ'),  'salut', 'full_name', 'ety', 'lcn', 'cn', 'job_email', 'job_addr', 'job_addr_unit', 'job_phn', 'fee',  'feepaid', 'owner_paid','payment_date', 'reg_bank_detail', 'fee_balance', 'int_remark', 'ref_crit', 'ref_amt', 's1', 's2','s3','s4','s5', 'ref_due_date', 'start_date', 'comments','qoute_ate','extra_charge','duration', 'daysreq','freqreq','job_loc', 'bbplcradius', 'bbplcregion', 'bbplcequip', 'bbplcequipd', 'no_of_kids', 'ykid', 'okid', 'job_alone',  'job_whoelse', 'pets', 'pet_muzzle', 'pet_description', 'crit_restriction', 'mf', 'CleanReq', 'CleanReqDe', 'CookReq','CookReqDe', 'kidshealth', 'familyworkcon','budget', 'phone_convo', 'invc',  AllowedSort::field('crdr1_name', 'u1.name'), 'CRDR1_reason',AllowedSort::field('crdr2_name', 'u2.name'),  AllowedSort::field('last_edited', 'u3.name'), AllowedSort::field('a-l-l-o-c-a-t-e-d-b-b', 'allctbbs') ])

        ->allowedFilters([AllowedFilter::exact('jobMBJ'),'salut', 'full_name', 'ety', 'lcn', AllowedFilter::exact('cn'),'job_email', 'job_addr', 'job_addr_unit', AllowedFilter::exact('job_phn'), AllowedFilter::exact('crdr1_name', 'crdr1'),  AllowedFilter::exact('crdr2_name', 'crdr2'), AllowedFilter::exact('fee'), AllowedFilter::exact('feepaid'), AllowedFilter::exact('owner_paid'),    AllowedFilter::scope('payment_date_start'), AllowedFilter::scope('payment_date_end'),  'reg_bank_detail', 'fee_balance', 'int_remark', 'ref_crit', 'ref_amt', 's1', 's2','s3', 's4', 's5', AllowedFilter::scope('ref_due_date_start'), AllowedFilter::scope('ref_due_date_end'),  AllowedFilter::scope('start_date_start'), AllowedFilter::scope('start_date_end'), 'comments', 'qoute_ate', 'extra_charge', 'duration', 'daysreq', 'freqreq', 'job_loc','bbplcradius', 'bbplcregion', 'bbplcequip','bbplcequipd','bbplcequipd', AllowedFilter::exact('no_of_kids'),  AllowedFilter::scope('ykid_start'), AllowedFilter::scope('ykid_end'),AllowedFilter::scope('okid_start'), AllowedFilter::scope('okid_end'), AllowedFilter::exact('job_alone'), 'job_whoelse',  AllowedFilter::exact('pets'), 'pet_muzzle', 'pet_description', 'crit_restriction', 'mf','CleanReq','CleanReqDe','CookReq', 'CookReqDe', 'kidshealth', 'familyworkcon', 'budget','pay_schdl', 'phone_convo' , AllowedFilter::exact('invc'), AllowedFilter::exact('last_edited'),AllowedFilter::exact('id'), $globalSearch,AllowedFilter::scope('a-l-l-o-c-a-t-e-d-b-b', 'AllocatedBBLike')]);

        if($perPage != 10000) {
            $jobsearchs = $chained
            ->paginate($perPage)
            ->withQueryString();
        } else {
            $jobsearchs = $chained
            ->get();
        }
        $resourceNeo = ['resourceName' => 'jobsearch'];

        $maxCheckboxSelect = Setting::where('slug', 'max-checkbox-select')->firstOrFail();
        $resourceNeo['max-checkbox-select'] = $maxCheckboxSelect->value;

        $coordinatorName =  config('app.coordinator.name', 'Coordinator');
        $users = User::role($coordinatorName)->get();
        $allcoordinator = [];
        foreach ($users as $key => $coorr) {
            if(!(\Auth::user()->can('jobsearch_changeCRDR')) && $coorr->id == \Auth::user()->id) {
                continue;
            }
            $allcoordinator[] = ['id' => $coorr->id,'label' => $coorr->name];
        }

        $resourceNeo['allcordinator'] = $allcoordinator;
        $resourceNeo['cellDetail'] = true;
        $resourceNeo['bulkActions'] = [];
        $resourceNeo['servExp'] = true;

        if(!(\Auth::user()->can('all')) && \Auth::user()->can('jobsearch_DisCheckboxes')) {
            $resourceNeo['DisCheckboxes'] = true;
        }


        if(\Auth::user()->can('jobsearch_delete')) {
            $resourceNeo['bulkActions']['bulk_delete'] = [];
        }
        if(\Auth::user()->can('jobsearch_export')) {
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

        return Inertia::render('Admin/JobSearchIndexView', ['public_holidays' => $public_holidays , 'moduledatas' => $jobsearchs, 'resourceNeo' => $resourceNeo])->table(function (InertiaTable $table) use ($allcoordinatorAll, $allUserAll) {
            $crdr1 = $crdr2 = $allUserAll;
            $crdr1[0]['label'] = 'Select CRDR1';
            $crdr2[0]['label'] = 'Select CRDR2';
            $NextColKey = '';
            $editperm = false;
            if((\Auth::user()->can('all')) || \Auth::user()->can('jobsearch_edit')) {
                $editperm = true;
            }

            $table->withGlobalSearch()
            ->column('jobMBJ', 'MBJN', searchable: true, sortable: true, extra:['shoinfoonedit' => true,'info' => 'MBJ Number', 'editable' => (Auth::user()->role_name == 'super-admin'),'colKey' => ($NextColKey = Helper::NextColKey($NextColKey)),'width' => '60px', 'bg' => 'bg-blue-300', 'showhide' => ['gist_auto','job-type','job-number'],])
            ->column('gist_auto', 'Gist', hidden:true, extra:['info' => 'auto-generated summary', 'dispval' => 'dataval', 'type' => 'textarea', 'viewable' => true, 'hidden' => true,'colKey' => ($NextColKey = Helper::NextColKey($NextColKey)), 'width' => '60px', 'bg' => 'bg-blue-400',])
            ->column(label: 'Job Type', hidden:true, extra:['hidden' => true,'colKey' => ($NextColKey = Helper::NextColKey($NextColKey)), 'width' => '60px', 'bg' => 'bg-blue-400'])
            ->column(label: 'Job Number', hidden:true, extra:['hidden' => true,'colKey' => ($NextColKey = Helper::NextColKey($NextColKey)), 'width' => '60px', 'bg' => 'bg-blue-400'])

            ->column('s_full_name', 'Customer Name', extra:['width' => '100px','bg' => 'bg-slate-400', 'showhide' => ['salut','full_name', 'ety', 'all-ety'],'colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
            ->column('salut', 'Salutation', searchable: true, sortable: true, hidden:true, extra:['shoinfoonedit'=> true, 'width' => '65px','info' => 'Salutation','hidden' => true,'colKey' => ($NextColKey = Helper::NextColKey($NextColKey)), 'editable' => $editperm, 'freetext' => 'input', 'type' => 'select', 'options' => [['id' => '','label' => 'Salutation'],['id' => 'Ms.','label' => 'Ms.'],['id' => 'Mr.','label' => 'Mr.']]  ])
            ->column('full_name', 'FullName', hidden:true, searchable: true, sortable: true, extra:['width' => '105px','hidden' => true, 'editable' => $editperm,'colKey' => ($NextColKey = Helper::NextColKey($NextColKey)) ])

            ->column('ety', 'Pri Ety', hidden:true, searchable: true, sortable: true, extra:['shoinfoonedit' => true, 'info'=>'Primary Entity / Organization / Company Associated with Client', 'width' => '105px','hidden' => true, 'colKey' => ($NextColKey = Helper::NextColKey($NextColKey)) ])
            ->column(label:'All Ety', hidden:true, extra:['shoinfoonedit' => true ,'width' => '360px','hidden' => true ,'colKey' => ($NextColKey = Helper::NextColKey($NextColKey)), 'clEventTrOn' => 'oaddrbtn_'  ])

            ->column('lcn', 'LCN', extra:['width' => '100px','bg' => 'bg-slate-400', 'showhide' => ['cn'],'colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
            ->column('cn', 'CN', searchable: true, sortable: true, hidden:true, extra:['width' => '60px', 'bg' => 'bg-blue-300', 'info' => 'Customer Number', 'editable' => (Auth::user()->role_name == 'super-admin'),'colKey' => ($NextColKey = Helper::NextColKey($NextColKey)) ])

            ->column('job_email', 'Pri Email', searchable: true, sortable: true, extra:['width' => '75px','bg' => 'bg-slate-400', 'info' => 'click to see email', 'showhide' => ['all-emails'], 'colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
            ->column(label:'All Emails', hidden:true, extra:['width' => '360px', 'info' => 'Click to view other emails and descriptions.  The top one is the primary/default one.', 'hidden' => true, 'colKey' => ($NextColKey = Helper::NextColKey($NextColKey)),'clEventTrOn' => 'ophnbtn_' ])

            ->column('job_addr', 'Pri Address', searchable: true, sortable: true, extra:['width' => '100px','bg' => 'bg-slate-400','shoinfoonedit'=> true, 'showhide' => ['job_addr_unit','all-addresses'],'colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
            ->column('job_addr_unit', 'Pri #', searchable: true, sortable: true, hidden:true, extra:['width' => '65px','info' => 'Unit Number','hidden' => true,'colKey' => ($NextColKey = Helper::NextColKey($NextColKey)), 'editable' => $editperm, 'shoinfoonedit'=> true])
            ->column(label:'All Addresses', hidden:true, extra:['width' => '360px','hidden' => true, 'colKey' => ($NextColKey = Helper::NextColKey($NextColKey)),'clEventTrOn' => 'oaddrbtn_' ])

            ->column('job_phn', 'Pri Phone', searchable: true, sortable: true, extra:['width' => '100px','bg' => 'bg-slate-400', 'info' => 'For WhatsApp Mainly', 'showhide' => ['all-phone-numbers'],'colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
            ->column(label:'All Phone Numbers', hidden:true, extra:['width' => '360px', 'info' => 'All numbers and descriptions.  The top one is the primary/default one.', 'hidden' => true, 'colKey' => ($NextColKey = Helper::NextColKey($NextColKey)),'clEventTrOn' => 'ophnbtn_' ])

            ->column('crdr1_name', 'CRDR1', searchable: true, sortable: true, extra:[ 'type' => 'select','options' => $crdr1, 'bg' => 'bg-customOrange','info' => 'Coordinator in charge','colKey' => ($NextColKey = Helper::NextColKey($NextColKey)),'width' => '110px', 'clEventTrOn' => 'crdr1asn_'])

            ->column('crdr2_name', 'CRDR2', searchable: true, sortable: true, extra:['type' => 'select','options' => $crdr2, 'bg' => 'bg-customOrange','info' => 'Coordinator 2IC','colKey' => ($NextColKey = Helper::NextColKey($NextColKey)),'width' => '110px','clEventTrOn' => 'crdr2asn_'])

            ->column('fee', 'Fee', searchable: true, sortable: true, extra:['width' => '60px', 'bg' => 'bg-green-200','info' => 'Platform Fee Charged in SGD$', 'editable' => $editperm,'colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
            ->column('feepaid', 'Paid', searchable: true, sortable: true, extra:['width' => '60px', 'bg' => 'bg-green-200','info' => 'Platform Fee Paid in SGD$', 'editable' => $editperm,'colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
            ->column('owner_paid', 'P?', searchable: true, sortable: true, extra:['width' => '60px', 'bg' => 'bg-green-200','info' => 'Owner paid? Y for Yes, N for No, C for confirmed but not paid.', 'editable' => $editperm, 'freetext' => 'input', 'type' => 'select', 'options' => [['id' => '','label' => 'P?'],['id' => 'Y','label' => 'Y'],['id' => 'N','label' => 'N'],['id' => 'C','label' => 'C']], 'colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])

            ->column('payment_date', 'PD', searchable: true, sortable: true, extra:['width' => '60px', 'bg' => 'bg-green-200','info' => 'Payment received date (previously d-m-yy format in excel)', 'type' => 'datePicker','editable' => $editperm, 'freetext' => 'input', 'colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
            ->column('reg_bank_detail', 'Reg-Bank-Details', searchable: true, sortable: true, extra:['width' => '60px', 'bg' => 'bg-green-200','info' => '(Registered Name to check payment validity)', 'editable' => $editperm,'colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
            ->column('fee_balance', 'Fee Balance', searchable: true, sortable: true, extra:['width' => '60px', 'bg' => 'bg-green-200','info' => 'Balance Due Still for this MBJ', 'editable' => $editperm,'colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
            ->column('int_remark', 'Int-Remarks', searchable: true, sortable: true, extra:['width' => '60px', 'bg' => 'bg-cyan-200','info' => 'Internal Remarks on THIS MBJ (Note this is DIFFERENT from \'Int-Comments-BB\' in the \'New BB Job Req\' Module)', 'editable' => $editperm, 'shoinfoonedit' => true, 'type' => 'textarea', 'colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
            ->column('ref_crit', 'REFCriteria', searchable: true, sortable: true, extra:['width' => '60px', 'bg' => 'bg-cyan-200','info' => 'SELECT 1 OPTION ONLY for the Refund Criteria:
                A_Job-completed-fully
                B_Job-not-completed-5-done
                C_Job-not-completed-1234-done
                D_Job-nulled', 'editable' => $editperm, 'freetext' => 'input', 'type' => 'select', 'options' => [['id' => '','label' => 'REFCriteria'],['id' => 'A_Job-completed-fully','label' => 'A_Job-completed-fully'],['id' => 'B_Job-not-completed-5-done','label' => 'B_Job-not-completed-5-done'],['id' => 'C_Job-not-completed-1234-done','label' => 'C_Job-not-completed-1234-done'],['id' => 'D_Job-nulled','label' => 'D_Job-nulled']],'colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
            ->column('ref_amt', 'RefAmt', searchable: true, sortable: true, extra:['width' => '60px', 'bg' => 'bg-cyan-200','info' => 'Refund Amount in SGD$', 'editable' => $editperm,'colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
            ->column('s1', 'S1', searchable: true, sortable: true, extra:['width' => '60px', 'onlyNumber' => true, 'freetext' => 'input', 'bg' => 'bg-cyan-200','info' => '(arranging bbsitter)', 'editable' => $editperm, 'shoinfoonedit' => true,'colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
            ->column('s2', 'S2', searchable: true, sortable: true, extra:['width' => '60px', 'onlyNumber' => true, 'bg' => 'bg-cyan-200','info' => '(interviewing and choosing bbsitter)', 'editable' => $editperm,'shoinfoonedit' => true, 'colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
            ->column('s3', 'S3', searchable: true, sortable: true, extra:['width' => '60px', 'onlyNumber' => true, 'bg' => 'bg-cyan-200','info' => '(finalizing & signing contract, pay deposit)', 'editable' => $editperm,'shoinfoonedit' => true, 'colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
            ->column('s4', 'S4', searchable: true, sortable: true, extra:['width' => '60px', 'onlyNumber' => true, 'bg' => 'bg-cyan-200','info' => '(deposit received but refer to 6 MEIDE BB for full $ details)', 'editable' => $editperm,'shoinfoonedit' => true, 'colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
            ->column('s5', 'S5', searchable: true, sortable: true, extra:['width' => '60px', 'onlyNumber' => true, 'bg' => 'bg-cyan-200','info' => '(4=ongoing job; 5=deposit to be refunded; 6=refunded already; 7=deposit forfeited, 8=deposit/refund/admin on hold due to queries, 9=admin fee only, 10=coordinator input status 5 but problem with refund process)', 'editable' => $editperm,'shoinfoonedit' => true, 'colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
            ->column('ref_due_date', 'RefDueDate', searchable: true, sortable: true, extra:['width' => '60px', 'bg' => 'bg-cyan-200','info' => 'Due Date of Refund', 'editable' => $editperm,'type' => 'datePicker', 'freetext' => 'input', 'colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])

            ->column('start_date', 'Sdate', searchable: true, sortable: true, extra:['type' => 'datePicker','showhide' => ['start_time'],'info' => 'Start Date of BB Required', 'editable' => $editperm, 'freetext' => 'input', 'colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
            
            ->column('start_time', 'STime', hidden:true, extra:['hidden' => true,'info' => 'Start Time of BB Required, Use 24 hour Time format eg. 2359 and not 1159PM','shoinfoonedit' => true ,'bg' => 'bg-green-300', 'editable' => $editperm,'colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])

            ->column(label:'ALLOCATED BB', searchable: true, sortable: true, extra:['width' => '360px', 'bg' => 'bg-customOrange', 'dblClEventTrOn' => 'altbl_', 'info' => 'Click to See List of Allocated Babysitters and their details', 'colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])

            ->column('comments', 'Comments 细节', searchable: true, sortable: true, extra:['info' => ' \'Int-Comments-BB\' from the \'New BB Job Req\' Module)','editable' => $editperm, 'type' => 'textarea', 'shoinfoonedit' => true, 'colKey' => ($NextColKey = Helper::NextColKey($NextColKey)), 'width' => '270px'])

            ->column('qoute_ate', 'QuotedRate', searchable: true, sortable: true, extra:['info' => 'S$ per hour / per day / per week / per month', 'editable' => $editperm,'shoinfoonedit' => true, 'colKey' => ($NextColKey = Helper::NextColKey($NextColKey)),'width' => '110px'])

            ->column('extra_charge', 'Additional Charges', searchable: true, sortable: true, extra:['info' => 'S$ free text if any', 'editable' => $editperm,'shoinfoonedit' => true, 'colKey' => ($NextColKey = Helper::NextColKey($NextColKey)),'width' => '110px'])

            ->column(label:'More Details on MBJ', extra:['width' => '75px','colKey' => ($NextColKey = Helper::NextColKey($NextColKey)),'showhide' => ['duration','job_loc','no_of_kids', 'ykid', 'okid' ,'job_alone','pets','crit_restriction', 'mf','CleanReq','CookReq', 'kidshealth', 'familyworkcon', 'familyprof','budget', 'phone_convo'],])

            ->column('duration', 'Duration', hidden:true, searchable: true, sortable: true, extra:['width' => '67px','hidden' => true, 'info' => 'duration of sitting required - from mbb1. Expand to see more details.', 'editable' => $editperm, 'colKey' => ($NextColKey = Helper::NextColKey($NextColKey)), 'showhide2' => ['daysreq','freqreq'] ])

            ->column('daysreq', 'Daysreq', hidden:true, searchable: true, sortable: true, extra:['width' => '67px','hidden' => true, 'bg' => 'bg-blue-300', 'info' => 'Mon-Sun and PH - checkbox selection of days required for babysitting', 'freetext' => 'input', 'type' => 'checkbox', 'options' => [['id' => 'Monday','label' => 'Monday'],['id' => 'Tuesday','label' => 'Tuesday'],['id' => 'Wednesday','label' => 'Wednesday'],['id' => 'Thursday','label' => 'Thursday'],['id' => 'Friday','label' => 'Friday'],['id' => 'Saturday','label' => 'Saturday'],['id' => 'Sunday','label' => 'Sunday'],['id' => 'Public Hol','label' => 'Public Hol']], 'editable' => $editperm, 'colKey' => ($NextColKey = Helper::NextColKey($NextColKey)) ])
            ->column('freqreq', 'Freqreq', hidden:true, searchable: true, sortable: true, extra:['width' => '67px','hidden' => true, 'bg' => 'bg-blue-300', 'info' => 'frequency of babysitting required: ~D/W/F/M/Yx or O (~ = a number, x= a number, O=once only, D/W/F/M/Y = day, week, fortnight, month, year)(eg. 2W5 = 2 weeks, five times; or 5 sessions in 2 weeks)', 'shoinfoonedit' => true, 'editable' => $editperm, 'colKey' => ($NextColKey = Helper::NextColKey($NextColKey)) ])

            ->column('job_loc', 'Plc', hidden:true, searchable: true, sortable: true, extra:['width' => '67px','hidden' => true, 'info' => 'Place for BB (bbh = At Babysitter House Only; oh = At My House Only; travelh = Outdoors/Hotel/Travel Nanny; ?h for all other entries.)', 'freetext' => 'textarea', 'type' => 'select', 'options' => [['id' => '','label' => 'Plc'],['id' => 'bbh','label' => 'bbh'],['id' => 'oh','label' => 'oh'],['id' => 'travelh','label' => 'travelh'],['id' => '?h','label' => '?h']], 'editable' => $editperm, 'colKey' => ($NextColKey = Helper::NextColKey($NextColKey)),'shoinfoonedit' => true, 'showhide2' => ['bbplcradius','bbplcregion','bbplcequip','bbplcequipd'] ])

            ->column('bbplcradius', 'Bbplcradius', hidden:true, searchable: true, sortable: true, extra:['width' => '67px','hidden' => true, 'bg' => 'bg-blue-300', 'info' => 'Distance to nanny house, in Allowable Radius (in kilometers), from stated client address', 'shoinfoonedit' => true, 'editable' => $editperm, 'colKey' => ($NextColKey = Helper::NextColKey($NextColKey)) ])
            ->column('bbplcregion', 'Bbplcregion', hidden:true, searchable: true, sortable: true, extra:['width' => '67px','hidden' => true, 'bg' => 'bg-blue-300', 'info' => 'List of Allowable Regions in Singapore for client to send baby to nanny (eg. Orchard, Boon Lay)', 'shoinfoonedit' => true, 'editable' => $editperm, 'colKey' => ($NextColKey = Helper::NextColKey($NextColKey)) ])
            ->column('bbplcequip', 'Bbplcequip', hidden:true, searchable: true, sortable: true, extra:['width' => '67px','hidden' => true, 'bg' => 'bg-blue-300', 'info' => 'Require equipment provided at nanny house? (Yes / No)', 'shoinfoonedit' => true, 'type' => 'select','options' => [['id' => '','label' => 'Bbplcequip'],['id' => 'Yes','label' => 'Yes'],['id' => 'No','label' => 'No']], 'editable' => $editperm, 'freetext' => 'input', 'editable' => $editperm, 'colKey' => ($NextColKey = Helper::NextColKey($NextColKey)) ])
            ->column('bbplcequipd', 'Bbplcequipd', hidden:true, searchable: true, sortable: true, extra:['width' => '67px','hidden' => true, 'bg' => 'bg-blue-300', 'info' => 'Details of equipment needed', 'shoinfoonedit' => true, 'editable' => $editperm, 'colKey' => ($NextColKey = Helper::NextColKey($NextColKey)) ])



            ->column('no_of_kids', 'Kids', searchable: true, sortable: true, hidden:true, extra:['hidden' => true,'width' => '65px','editable' => $editperm,'info' => 'how many kids requiring BB','colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
            ->column('ykid', 'Ykid', searchable: true, sortable: true, hidden:true, extra:['hidden' => true,'width' => '95px','info' => 'bday of youngest (or only) kid','type' => 'datePicker', 'editable' => $editperm, 'freetext' => 'input', 'colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
            ->column('okid', 'Okid', searchable: true, sortable: true, hidden:true, extra:['hidden' => true,'width' => '95px','info' => 'bday of youngest (or only) kid','type' => 'datePicker', 'editable' => $editperm, 'freetext' => 'input', 'colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])

            ->column('job_alone', 'Alone', searchable: true, sortable: true, hidden:true, extra:['hidden' => true,'width' => '65px','showhide2' => ['job_whoelse'], 'type' => 'select','options' => [['id' => '','label' => 'Job Alone'],['id' => 'Yes','label' => 'Yes'],['id' => 'No','label' => 'No']], 'editable' => $editperm, 'freetext' => 'input', 'info' => 'from mbb1 - do you require bb to bb alone? (Y/N)','colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
            ->column('job_whoelse', 'Whoelse', searchable: true, sortable: true, hidden:true, extra:['hidden' => true, 'bg' => 'bg-blue-300', 'editable' => $editperm,'info' => 'Who else may be at the residence with the babysitter?','colKey' => ($NextColKey = Helper::NextColKey($NextColKey)),'width' => '115px'])


            ->column('pets', 'Pets', searchable: true, sortable: true, hidden:true, extra:['hidden' => true,'width' => '55px','showhide2' => ['pet_muzzle','pet_description'], 'type' => 'select','options' => [['id' => '','label' => 'Pets'],['id' => 'Yes','label' => 'Yes'],['id' => 'No','label' => 'No']], 'editable' => $editperm, 'freetext' => 'input', 'info' => 'Do you have pet(s) at home?YesNo','colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
            ->column('pet_muzzle', 'Muzzle', searchable: true, sortable: true, hidden:true, extra:['width' => '95px','hidden' => true, 'bg' => 'bg-blue-300', 'editable' => $editperm, 'freetext' => 'textarea', 'type' => 'checkbox', 'options' => [['id' => 'No, strictly free roaming.','label' => 'No, strictly free roaming.'],['id' => 'Can be isolated or kenneled.','label' => 'Can be isolated or kenneled.'],['id' => 'Can be muzzled.','label' => 'Can be muzzled.']], 'info' => 'Can your pet(s) be isolated, kenneled or muzzled?','colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
            ->column('pet_description', 'Petspec', searchable: true, sortable: true, hidden:true, extra:['hidden' => true, 'bg' => 'bg-blue-300', 'editable' => $editperm,'info' => 'Specify your pet details','colKey' => ($NextColKey = Helper::NextColKey($NextColKey)),'width' => '115px'])


            ->column('crit_restriction', 'Crit+Restrictions', searchable: true, sortable: true, hidden:true, extra:['hidden' => true, 'width' => '50px', 'editable' => $editperm, 'info' => 'free text for further elaboration of restriction details + transferred comments from MBB1', 'colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])

            ->column('mf', 'M/F', searchable: true, sortable: true, hidden:true, extra:['hidden' => true, 'width' => '50px', 'editable' => $editperm, 'info' => 'Choose 1 combination of: M=male, F=female, A=Any + P=preferred, O=Only/Strictly', 'shoinfoonedit' => true, 'colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])

             ->column('CleanReq', 'CleanReq', searchable: true, sortable: true, hidden:true, extra:['hidden' => true, 'width' => '55px','showhide2' => ['CleanReqDe'], 'type' => 'select','options' => [['id' => '','label' => 'CleanReq'],['id' => 'Yes','label' => 'Yes'],['id' => 'No','label' => 'No']], 'editable' => $editperm, 'shoinfoonedit' => true, 'freetext' => 'input', 'info' => 'is cleaning required during the bb?','colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])

             ->column('CleanReqDe', 'CleanReqDe', searchable: true, sortable: true, hidden:true, extra:['hidden' => true,'bg' => 'bg-blue-300', 'width' => '50px', 'editable' => $editperm, 'info' => 'details on cleaning required', 'shoinfoonedit' => true, 'colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])

             ->column('CookReq', 'CookReq', searchable: true, sortable: true, hidden:true, extra:['hidden' => true,'width' => '55px','showhide2' => ['CookReqDe'], 'type' => 'select','options' => [['id' => '','label' => 'CookReq'],['id' => 'Yes','label' => 'Yes'],['id' => 'No','label' => 'No']], 'editable' => $editperm, 'shoinfoonedit' => true, 'freetext' => 'input', 'info' => 'is cooking required during the bb?','colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])

             ->column('CookReqDe', 'CookReqDe', searchable: true, sortable: true, hidden:true, extra:['hidden' => true, 'bg' => 'bg-blue-300', 'width' => '50px', 'editable' => $editperm, 'info' => 'details on cooking required', 'shoinfoonedit' => true, 'colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])

             ->column('kidshealth', 'Kidshealth', searchable: true, sortable: true, hidden:true, extra:['hidden' => true, 'width' => '50px', 'editable' => $editperm, 'info' => 'health conditions of kid(s) if any', 'shoinfoonedit' => true, 'colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
             ->column('familyworkcon', 'Familyworkcon', searchable: true, sortable: true, hidden:true, extra:['hidden' => true, 'width' => '50px', 'editable' => $editperm, 'info' => 'Family work conditions/setup: Main caregiver, Who stays in the house, WFH/go office and working hours, etc (Option of "Declined to Reveal Understanding Higher Mismatch Risk")', 'shoinfoonedit' => true, 'colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])

             ->column(label:'familyprof', hidden:true, extra:['hidden' => true, 'width' => '50px',  'info' => 'Family Profile: Nationalities Present, Occupations, for each household member (Option of "Declined to Reveal Understanding Higher Mismatch Risk")',  'colKey' => ($NextColKey = Helper::NextColKey($NextColKey)),'clEventTrOn' => 'fpbtn_'])

             ->column('budget', 'Budget', searchable: true, sortable: true, hidden:true, extra:['hidden' => true, 'width' => '50px', 'editable' => $editperm, 'info' => 'Please advise us about your budget (per month/week/day/hour):', 'colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])

             ->column('phone_convo', 'phone-convo', searchable: true, sortable: true, hidden:true, extra:['hidden' => true, 'type' => 'textarea', 'info' => 'Records of Phone Convo with Customer (Private and Confidential)','editable' => $editperm, 'dispval' => 'dataval', 'colKey' => ($NextColKey = Helper::NextColKey($NextColKey)), 'width' => '80px'])

             ->column('invc', 'Inv', searchable: true, sortable: true, extra:['info' => 'Invoice Number (Store Invoices at https://drive.google.com/drive/u/1/folders/1Xhm4sFiTD9YY5KW6Fwqc5NhDQnBeN9Hc)', 'colKey' => ($NextColKey = Helper::NextColKey($NextColKey)), 'width' => '80px','bg' => 'bg-green-300'])

             ->column(label:'Auto-txt', extra:['showhide' => ['sitting_contract','jc1','jc2','inv_detail','receipt_no'], 'width' => '50px', 'bg' => 'bg-fuchsia-300',  'colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
             ->column('sitting_contract', 'Sitting-contract', hidden:true, extra:['hidden' => true, 'viewable' => true, 'width' => '50px', 'bg' => 'bg-customOrange','type' => 'textarea', 'dispval' => 'dataval',  'info' => 'auto generated paragraph for contract intro',  'colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
             ->column('jc1', 'JC1', hidden:true, extra:['hidden' => true, 'viewable' => true, 'width' => '50px', 'bg' => 'bg-customOrange', 'type' => 'textarea', 'dispval' => 'dataval',  'info' => 'auto generated paragraph for contract intro',  'colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
             ->column('jc2', 'JC2', hidden:true, extra:['hidden' => true, 'viewable' => true, 'width' => '50px', 'bg' => 'bg-customOrange','type' => 'textarea', 'dispval' => 'dataval',  'info' => 'auto generated job confirmation message 2',  'colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
             ->column('inv_detail', 'Invdetail', hidden:true, extra:['hidden' => true, 'viewable' => true, 'width' => '50px', 'bg' => 'bg-customOrange', 'type' => 'textarea', 'dispval' => 'dataval', 'info' => 'auto generated invoice details',  'colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
             ->column('receipt_no', 'Receipt no.', hidden:true, extra:['hidden' => true, 'viewable' => true, 'width' => '50px', 'bg' => 'bg-customOrange','type' => 'textarea',  'dispval' => 'dataval', 'info' => 'auto generated  receipt details',  'colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])

            ->column('last_edited', 'LastEdit', searchable: true, sortable: true, extra:['type' => 'select', 'options' => $allUserAll,'colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])


            ->dateFilter(key: 'payment_date_start', label: 'Entry Date From')
            ->dateFilter(key: 'payment_date_end', label: 'Entry Date To')
            ->dateFilter(key: 'ref_due_date_start', label: 'Entry Date From')
            ->dateFilter(key: 'ref_due_date_end', label: 'Entry Date To')
            ->dateFilter(key: 'start_date_start', label: 'Entry Date From')
            ->dateFilter(key: 'start_date_end', label: 'Entry Date To')
            ->dateFilter(key: 'ykid_start', label: 'Entry Date From')
            ->dateFilter(key: 'ykid_end', label: 'Entry Date To')
            ->dateFilter(key: 'okid_start', label: 'Entry Date From')
            ->dateFilter(key: 'okid_end', label: 'Entry Date To')
            ;

            if(!(\Auth::user()->can('all')) && \Auth::user()->can('jobsearch_DisViewAllRow')) {
                $table->perPageOptions([10,15,30,50,100]);
            } else {
                $table->perPageOptions([10,15,30,50,100, 10000]);
            }

            $table->column('id', 'Row Ref', searchable: true, hidden:true, extra:['info' => 'Row Ref number', 'colKey' => ($NextColKey = Helper::NextColKey($NextColKey))]);
            $table->column(label: 'Actions', extra:['colKey' => ($NextColKey = Helper::NextColKey($NextColKey))]);
        });

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JobSearch $jobsearch)
    {
        $uname = $jobsearch->id;
        $jobsearch->delete();
        \ActivityLog::add(['action' => 'deleted','module' => 'jobsearch','data_key' => $uname, 'data_ref' => $jobsearch->id,]);
        return redirect()->back()->with(['message' => 'Jobsearch Deleted !!','msg_type' => 'warning']);
    }

    public function fieldUpdate(Request $request)
    {
        $notAllowedBlank = ['job_email','job_phn'];
        $saved_value = $request->value;
        if(in_array($request->key, $notAllowedBlank) &&  $saved_value == '') {
            return redirect()->back()->with(['message' => 'Value can not be blank !!','msg_type' => 'danger']);
        } elseif($request->key == 'job_email') {
            if (!filter_var($saved_value, FILTER_VALIDATE_EMAIL)) {
                return redirect()->back()->with(['message' => 'Email format not valid !!','msg_type' => 'danger']);
            }
        } elseif(in_array($request->key, ['fee','feepaid','fee_balance','ref_amt'])) {
            if($request->value == '') {
                $saved_value = null;
            } elseif(!preg_match('/^[0-9 .-]+$/', $saved_value)) {
                return redirect()->back()->with(['message' => $request->key . ' not valid !!','msg_type' => 'danger']);
            }
        } elseif($request->key == 'no_of_kids') {
            if($request->value == '') {
                $request->value = null;
            } elseif(!preg_match('/^[0-9]+$/', $saved_value)) {
                return redirect()->back()->with(['message' => 'Numbers of kid value not valid !!','msg_type' => 'danger']);
            } elseif($saved_value > 9999) {
                return redirect()->back()->with(['message' => 'Numbers of kid value not valid !!','msg_type' => 'danger']);
            }
        } elseif($request->key == 'start_time') {
            if($request->value == '') {
                $request->value = null;
            } elseif(!preg_match("/^(?:2[0-3]|[01][0-9])[0-5][0-9]$/", $saved_value)) {
                return redirect()->back()->with(['message' => 'Time format invalide use HHMM !!','msg_type' => 'danger']);
            }
        } elseif(in_array($request->key, ['daysreq','pay_pref','pet_muzzle'])) {
            $saved_value = is_array($request->value) ? implode(PHP_EOL, $request->value) : $request->value;
        } elseif(in_array($request->key, ['payment_date','start_date','ref_due_date','ykid','okid'])) {
            if($request->value == '') {
                $request->value = null;
            } elseif (!(preg_match("/^[0-9]{4}-(0?[1-9]|1[0-2])-(0?[1-9]|[1-2][0-9]|3[0-1])$/", $request->value))) {
                return redirect()->back()->with(['message' => 'Wrong Format! Please key in format YYYY-MM-DD','msg_type' => 'danger']);
            }
        }



        $jobsearch = Jobsearch::find($request->id);
        $prev_value = $jobsearch->{$request->key};
        $jobsearch->{$request->key} = $saved_value;
        $coordinatorName =  config('app.coordinator.name', 'Coordinator');
        if($request->key == 'BStatus' && empty($jobsearch->CRDR1) && Auth::user()->role_name == $coordinatorName) {
            $jobsearch->CRDR1 = Auth::user()->id;
        }
        $isDirty = false;
        if($jobsearch->isDirty()) {
            $jobsearch->last_edited = Auth::user()->id;
            $jobsearch->save();
            $isDirty = true;
        }
        if($request->key == 'cn') {
            $lcn=Jobsearch::updateLCN($request->id);
            $jobsearch->lcn =$lcn;
            $jobsearch->save();
        }

        if($isDirty) {
            $uname = $request->key;
            \ActivityLog::add(['action' => 'updated','module' => 'jobsearch','data_key' => $uname, 'data_id' => $request->id, 'data_ref' => $request->ref, 'data_val' => $prev_value]);
            //broadcast(new MedideNotification($request->ref . ' Updated By ' . Auth::user()->name, 'jobsearch'))->toOthers();
            $res = ['message' => 'Updated Successfully.','msg_type' => 'info'];
        } else {
            $res = ['message' => 'No Value  Updated in Job Request .','msg_type' => 'warning'];
        }
        return redirect()->back()->with($res);
    }

    public function assignCRDR(Request $request)
    {
        $jobsearch = JobSearch::find($request->id);


        $prev_value = $jobsearch->{$request->key};
        $jobsearch->{$request->key} = $request->value;

        if($jobsearch->isDirty()) {
            $jobsearch->last_edited = Auth::user()->id;
            $jobsearch->save();

            $uname = $request->key;
            \ActivityLog::add(['action' => 'updated','module' => 'jobsearch','data_key' => $uname,'data_id' => $request->id,'data_val' => $prev_value, 'data_ref' => $request->ref, ]);
            $res = ['message' => $request->key . ' Changed !!','msg_type' => 'info'];
        } else {
            $res = ['message' => 'No CRDR  Updated.','msg_type' => 'warning'];
        }
        return redirect()->back()->with($res);
    }


    /**
     * bulk delete.
     */
    public function bulkDestroy()
    {
        JobSearch::whereIn('id', request('ids'))->delete();
        $uname = (count(request('ids')) > 10) ? 'Many' : $uname = implode(',', request('ids'));
        \ActivityLog::add(['action' => 'deleted','module' => 'jobsearch','data_key' => $uname, 'data_ref' => $uname,]);
        return redirect()->back()->with(['message' => 'Selected Jobsearch Deleted !!','msg_type' => 'warning']);
    }
    public function geneInv(Request $request)
    {
        $maxInv = Setting::where('slug', 'maxInv')->firstOrFail();
        $lastmaxInv = $maxInv->value;
        $nextInv = \Helper::NextInv($lastmaxInv);
        $jobsearch = Jobsearch::find($request->id);
        $jobsearch->invc = $nextInv;


        if($jobsearch->isDirty()) {
            $jobsearch->last_edited = Auth::user()->id;
            $jobsearch->save();
            Setting::where('slug', 'maxInv')->update(['value' => $nextInv]);

            \ActivityLog::add(['action' => 'updated', 'module' => 'jobsearch','data_key' => 'invc', 'data_id' => $request->id, 'data_ref' => $request->cellref, 'data_val' => $nextInv]);

            $res = ['message' => 'Invoice generated','msg_type' => 'info'];
        } else {
            $res = ['message' => 'Invoice  Updated.','msg_type' => 'warning'];
        }
        return redirect()->back()->with($res);

    }
    /**
    * Bulk replicate.
    */
    public function bulkDuplicate()
    {
        $allDuplicate = [];
        $maxMbj = Setting::where('slug', 'maxMbj')->firstOrFail();
        $lastMBJNo = $maxMbj->value;

        foreach (request('ids') as  $id) {
            $jobsearch = Jobsearch::find($id);
            for($i = 0; $i < request('nx');$i++) {
                $newJobsearch = $jobsearch->replicate();
                $newJobsearch->created_at = Carbon::now();

                $lastMBJNo  = \Helper::NextMBJ($lastMBJNo);
                Setting::where('slug', 'maxMbj')->update(['value' => $lastMBJNo]);
                $newJobsearch->jobMBJ = $lastMBJNo;
                $newJobsearch->last_edited = null;

                $newJobsearch->CRDR1 = null;
                $newJobsearch->CRDR2 = null;
                $newJobsearch->fee = null;
                $newJobsearch->feepaid = null;
                $newJobsearch->owner_paid = null;
                $newJobsearch->payment_date = null;
                $newJobsearch->fee_balance = null;
                $newJobsearch->int_remark = null;
                $newJobsearch->ref_crit = null;
                $newJobsearch->ref_amt = null;
                $newJobsearch->s1 = null;
                $newJobsearch->s2 = null;
                $newJobsearch->s3 = null;
                $newJobsearch->s4 = null;
                $newJobsearch->s5 = null;
                $newJobsearch->ref_due_date = null;
                $newJobsearch->start_date = null;
                $newJobsearch->comments = null;
                $newJobsearch->qoute_ate = null;
                $newJobsearch->extra_charge = null;
                $newJobsearch->duration = null;
                $newJobsearch->daysreq = null;
                $newJobsearch->freqreq = null;
                $newJobsearch->job_loc = null;
                $newJobsearch->bbplcradius = null;
                $newJobsearch->bbplcregion = null;
                $newJobsearch->bbplcequip = null;
                $newJobsearch->bbplcequipd = null;
                $newJobsearch->bbplcequipd = null;
                $newJobsearch->job_alone = null;
                $newJobsearch->job_whoelse = null;
                $newJobsearch->budget = null;
                $newJobsearch->save();
                $allDuplicate[] = $id;

                $familyProofs = FamilyProof::where('jobId', $jobsearch->id)->orderBy('id', 'ASC')->get();
                foreach ($familyProofs as $familyProof) {
                    FamilyProof::create(['jobId' => $newJobsearch->id, 'member' => $familyProof->member, 'nati' => $familyProof->nati, 'occup' => $familyProof->occup]);
                }
                $otherAddrress = OtherAddrress::where('jobId', $jobsearch->id)->orderBy('id', 'ASC')->get();
                foreach ($otherAddrress as $OtherAddrres) {
                    OtherAddrress::create(['jobId' => $newJobsearch->id, 'address' => $OtherAddrres->address, 'aunit' => $OtherAddrres->aunit, 'rank' => $OtherAddrres->rank, 'prime' => $OtherAddrres->prime, 'label' => $OtherAddrres->label]);
                }
                $otherPhones = OtherPhone::where('jobId', $jobsearch->id)->orderBy('id', 'ASC')->get();
                foreach ($otherPhones as $OtherPhone) {
                    OtherPhone::create(['jobId' => $newJobsearch->id, 'phone' => $OtherPhone->phone, 'label' => $OtherPhone->label, 'is_primary' => $OtherPhone->is_primary]);
                }
                $otherEmails = OtherEmail::where('jobId', $jobsearch->id)->orderBy('id', 'ASC')->get();
                foreach ($otherEmails as $email) {
                    OtherEmail::create(['jobId' => $newJobsearch->id, 'email' => $email->email, 'label' => $email->label, 'is_primary' => $email->is_primary]);
                }
                $otherEty = OtherEty::where('jobId', $jobsearch->id)->orderBy('id', 'ASC')->get();
                foreach ($otherEty as $ety) {
                    OtherEty::create(['jobId' => $newJobsearch->id, 'ety' => $ety->ety, 'label' => $ety->label, 'is_primary' => $ety->is_primary]);
                }


            }
        }
        $uname = (count($allDuplicate) > 10) ? 'Many' : $uname = implode(',', $allDuplicate);
        \ActivityLog::add(['action' => 'duplicated', 'module' => 'jobsearch', 'data_key' => $uname, 'data_ref' => $uname,]);
        return redirect()->back()->with(['message' => 'Selected MBJ Search(s) Duplicated Successfully! Check the top of the table for it!','msg_type' => 'info']);
    }
    public function export()
    {

        if(\Auth::user()->can('jobsearch_listAll')) {
            $rows = JobSearch::select('job_searches.*', 'u1.name AS crdr1_name', 'u2.name AS crdr2_name', 'u3.name AS last_edit_name')->selectRaw('GROUP_CONCAT(CONCAT("(",albb.rank,")(", bbapl.BUniqueID,")(",if(albb.remark!="",albb.remark,""),")" )  ORDER BY albb.rank SEPARATOR " /// ") as allctbbs')->selectRaw('GROUP_CONCAT(bbapl.BUniqueID  ORDER BY albb.rank SEPARATOR "::") as bbsuniqids')->selectRaw('GROUP_CONCAT(bbapl.whts_no  ORDER BY albb.rank SEPARATOR "::") as bbscontacts')
            ->selectRaw('GROUP_CONCAT(bbapl.full_name  ORDER BY albb.rank SEPARATOR "::") as fullnames')->leftJoin('users AS u1', 'u1.id', 'job_searches.crdr1', 'left')->leftJoin('users  AS u2', 'u2.id', 'job_searches.crdr2', 'left')->leftJoin('users  AS u3', 'u3.id', 'job_searches.last_edited', 'left')->leftJoin('allocated_b_b_s  AS albb', 'albb.jobId', 'job_searches.id', 'left')->leftJoin('bbapplicants  AS bbapl', 'bbapl.id', 'albb.baysitterId', 'left')->groupBy("job_searches.id")->orderBy('jobMBJ', 'DESC')->get();
        } else {
            $rows = JobSearch::select('job_searches.*', 'u1.name AS crdr1_name', 'u2.name AS crdr2_name', 'u3.name AS last_edit_name')->selectRaw('GROUP_CONCAT(CONCAT("(",albb.rank,")(", bbapl.BUniqueID,")(",if(albb.remark!="",albb.remark,""),")" )  ORDER BY albb.rank SEPARATOR " /// ") as allctbbs')->selectRaw('GROUP_CONCAT(bbapl.BUniqueID  ORDER BY albb.rank SEPARATOR "::") as bbsuniqids')->selectRaw('GROUP_CONCAT(bbapl.whts_no  ORDER BY albb.rank SEPARATOR "::") as bbscontacts')
            ->selectRaw('GROUP_CONCAT(bbapl.full_name  ORDER BY albb.rank SEPARATOR "::") as fullnames')->leftJoin('users AS u1', 'u1.id', 'job_searches.crdr1', 'left')->leftJoin('users  AS u2', 'u2.id', 'job_searches.crdr2', 'left')->leftJoin('users  AS u3', 'u3.id', 'job_searches.last_edited', 'left')->leftJoin('allocated_b_b_s  AS albb', 'albb.jobId', 'job_searches.id', 'left')->leftJoin('bbapplicants  AS bbapl', 'bbapl.id', 'albb.baysitterId', 'left')->where(function ($query) {$query->where('CRDR1', null)->orWhere('CRDR1', Auth::id())->orWhere('CRDR2', Auth::id());})->groupBy("job_searches.id")->orderBy('jobMBJ', 'DESC')->get();
        }

        $fileName = 'SearchMBJ.csv';

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = ['MBJN','Gist','Job Type','Job Number','C. Name','Sal.','FullName','CN','Email','Address','#','Other Addresses','Phone number','Other Phone Numbers','CRDR1','CRDR2','Fee','Paid','P?','PD','Reg-Bank-Details','Fee Balance','Int-Remarks','REFCriteria','RefAmt','S1','S2','S3','S4','S5','RefDueDate','Sdate','STime','ALLOCATED BB','Comments 细节','QuotedRate','Additional Charges','More Details on MBJ','Duration','Daysreq','Freqreq','Plc','Bbplcradius','Bbplcregion','Bbplcequip','Bbplcequipd','Kids','Ykid','Okid','Alone','Whoelse','Pets','Muzzle','Petspec','Crit+Restrictions','M/F','CleanReq','CleanReqDe','CookReq','CookReqDe','Kidshealth','Familyworkcon','familyprof','Budget','phone-convo','Inv','Auto-txt','Sitting-contract','JC1','JC2','Invdetail','Receipt no.','LastEdit','Row Ref'
    ];

        $callback = function () use ($rows, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($rows as $row) {
                fputcsv($file, [$row->jobMBJ, $row->gist_auto,substr($row->jobMBJ, 0, 3), substr($row->jobMBJ, 3), $row->s_full_name, $row->salut, $row->full_name, $row->cn, $row->job_email, $row->job_addr, $row->job_addr_unit, '', $row->job_phn, '', $row->crdr1_name, $row->crdr2_name, $row->fee, $row->feepaid, $row->owner_paid, $row->payment_date, $row->reg_bank_detail, $row->fee_balance, $row->int_remark, $row->ref_crit, $row->ref_amt, $row->s1, $row->s2, $row->s3, $row->s4, $row->s5,  $row->ref_due_date, $row->start_date, $row->start_time, $row->allctbbs, $row->comments, $row->qoute_ate, $row->extra_charge, '', $row->duration, $row->daysreq, $row->freqreq, $row->job_loc, $row->bbplcradius,
                $row->bbplcregion,
                $row->bbplcequip,
                $row->bbplcequipd,
                $row->no_of_kids,
                $row->ykid,
                $row->okid,
                $row->job_alone,
                $row->job_whoelse,
                $row->pets,
                $row->pet_muzzle,
                $row->pet_description,
                $row->crit_restriction,
                $row->mf,
                $row->CleanReq,
                $row->CleanReqDe,
                $row->CookReq,
                $row->CookReqDe,
                $row->kidshealth,
                $row->familyworkcon,  '',
                $row->budget,
                $row->phone_convo,
                $row->invc, ((!$row->allctbbs || !$row->invc) ?
                "Unable to auto-gen. Pls fill in first : EMAIL; ALLOCATED BB; INVOICE NUMBER; etc." : ''),
                $row->sitting_contract,
                $row->jc1,  $row->jc2,  $row->inv_detail,  $row->receipt_no,  $row->last_edit_name,$row->id]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);

    }

    public function updateLcn(){
        $jobs=JobSearch::all();
        foreach($jobs as $job){
            $job->lcn=JobSearch::updateLCN($job->id);
            $job->save();
        }
        return redirect()->back()->with(['message' => 'LCN Updated Successfully','msg_type' => 'success']);
    }

    public function fieldData(Request $request){
        $fieldName = $request->fieldName ?? '';

        if(!$fieldName){
            return response()->json(['message' => 'At least one field is required !!', 'data' => []], 400);
        }

        $data = JobSearch::distinct()->pluck($fieldName)->toArray();
        $data = array_values(array_filter($data)); // filter data
        $result = array_map(function($element) { return ['id'=> $element, 'label' => $element];}, $data);
        return response()->json(['message' => 'Field Data shown successfully !!', 'data' => $result]);
    }
}

