<?php

namespace App\Http\Controllers\Admin;

use App\Models\Publicholiday;
use App\Models\User;
use Inertia\Inertia;
use App\Helpers\Helper;
use App\Models\Setting;
use App\Models\Bbapplicant;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Events\MedideNotification;
use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;

class BbapplicantController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:bbapplicant_list', ['only' => ['index', 'show']]);
        $this->middleware('can:bbapplicant_delete', ['only' => ['destroy','bulkDestroy']]);
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
                    ->orWhereFullText(['bbapplicants.full_name',  'bbapplicants.exp', 'bbapplicants.fulltime',  'bbapplicants.resid',   'bbapplicants.wheretodo',   'bbapplicants.workloc',  'bbapplicants.bonustask', 'bbapplicants.review', 'bbapplicants.comment', 'bbapplicants.BBSCR', 'bbapplicants.day_avl'], $value)

                    ->orWhere('bbapplicants.dob', 'LIKE', "{$value}%")
                    ->orWhere('bbapplicants.gender', 'LIKE', "{$value}%")
                    ->orWhere('bbapplicants.nationality', 'LIKE', "{$value}%")
                    ->orWhere('bbapplicants.ethnicity', 'LIKE', "{$value}%")
                    ->orWhere('bbapplicants.NOKname', 'LIKE', "{$value}%")
                    ->orWhere('bbapplicants.NOKrs', 'LIKE', "{$value}%")
                    ->orWhere('bbapplicants.NOKhp', 'LIKE', "{$value}%")
                    ->orWhere('bbapplicants.paynownum', 'LIKE', "{$value}%")
                    ->orWhere('bbapplicants.bankname', 'LIKE', "{$value}%")
                    ->orWhere('bbapplicants.created_at', 'LIKE', "{$value}%")
                    ->orWhere('bbapplicants.banknumb', 'LIKE', "{$value}%")
                    ->orWhere('bbapplicants.BUniqueID', 'LIKE', "{$value}%")
                    ->orWhere('bbapplicants.BUNO', 'LIKE', "{$value}%")
                    ->orWhere('bbapplicants.aceptdate', 'LIKE', "{$value}%")
                    ->orWhere('bbapplicants.cav', 'LIKE', "{$value}%")
                    ->orWhere('bbapplicants.WA-BL', 'LIKE', "{$value}%")

                    ->orWhere('bbapplicants.email', $value)
                    ->orWhere('bbapplicants.whts_no', $value)
                    ->orWhere('bbapplicants.whts_no2', $value)
                    ->orWhere('bbapplicants.telegram', $value)
                    ->orWhere('bbapplicants.whatsapp', $value)
                    ->orWhere('bbapplicants.remail', $value)
                    ;
                });
            });
        });
        $perPage = request()->query('perPage') ?? 10;

        $Query = Bbapplicant::select('bbapplicants.*', 'u3.name AS last_edit_name')->leftJoin('users  AS u3', 'u3.id', 'bbapplicants.last_edited', 'left');

        $chained = QueryBuilder::for($Query)
            ->defaultSort('-id')
            ->allowedSorts(['created_at', 'full_bio', 'full_name', 'email', 'whts_no', 'whts_no2', 'gender',
            'nationality', 'ethnicity', 'exp', 'fulltime', 'resid', 'wheretodo', 'workloc', 'bonustask', 'NOKname',
            'NOKrs', 'NOKhp', 'telegram', 'whatsapp', 'remail', 'paynownum', 'bankname','banknumb', 'BUniqueID', 'BUNO' , 'aceptdate', AllowedSort::field('last_edited', 'u3.name'), 'bal','WA-BL', 'BBSCR', 'day_avl','cav','dob', 'review', 'comment'])

            ->allowedFilters([AllowedFilter::exact('id'), AllowedFilter::scope('created_at', 'created_at_search'), AllowedFilter::scope('created_at_start'), AllowedFilter::scope('created_at_end'),  AllowedFilter::scope('dob', 'dob_search'), AllowedFilter::scope('dob_start'), AllowedFilter::scope('dob_end'), 'full_bio', 'full_name', 'email', AllowedFilter::exact('whts_no'), AllowedFilter::exact('whts_no2'),  AllowedFilter::exact('gender'), AllowedFilter::exact('nationality'), AllowedFilter::exact('ethnicity'), 'exp', AllowedFilter::exact('fulltime'), AllowedFilter::exact('resid'), AllowedFilter::exact('wheretodo'),'workloc', 'bonustask', 'NOKname', 'NOKrs', 'NOKhp', AllowedFilter::exact('telegram'), AllowedFilter::exact('whatsapp'), AllowedFilter::exact('remail'),  AllowedFilter::exact('paynownum'), AllowedFilter::exact('bankname'),  AllowedFilter::exact('banknumb'),'BUniqueID', 'BUNO', AllowedFilter::scope('aceptdate_start'), AllowedFilter::scope('aceptdate_end'), 'bal', 'review','comment', 'WA-BL', 'BBSCR', 'day_avl', 'cav',AllowedFilter::exact('last_edited'),$globalSearch,]);

        if($perPage != 10000) {
            $bbapplicants = $chained
            ->paginate($perPage)
            ->withQueryString();
        } else {
            $bbapplicants = $chained
            ->get();
        }
        $resourceNeo = ['resourceName' => 'bbapplicant'];
        $maxCheckboxSelect = Setting::where('slug', 'max-checkbox-select')->firstOrFail();
        $resourceNeo['max-checkbox-select'] = $maxCheckboxSelect->value;

        $resourceNeo['cellDetail'] = true;
        $resourceNeo['bulkActions'] = [];
        $resourceNeo['servExp'] = true;

        if(!(\Auth::user()->can('all')) && \Auth::user()->can('bbapplicant_DisCheckboxes')) {
            $resourceNeo['DisCheckboxes'] = true;
        }


        if(\Auth::user()->can('bbapplicant_delete')) {
            $resourceNeo['bulkActions']['bulk_delete'] = [];
        }
        if(\Auth::user()->can('bbapplicant_export')) {
            $resourceNeo['bulkActions']['csvExport'] = [];
        }

        $allUsers = User::with('roles')->get();

        $allUserAll = [['id' => '','label' => 'Last Edited']];
        foreach ($allUsers as $key => $usr) {

            $allUserAll[] = ['id' => $usr->id, 'label' => $usr->name];
        }


        return Inertia::render('Admin/BbapplicantIndexView', ['public_holidays' => $public_holidays,'moduledatas' => $bbapplicants, 'resourceNeo' => $resourceNeo])->table(function (InertiaTable $table) use ($allUserAll) {
            $NextColKey = '';
            $editperm = false;
            if((\Auth::user()->can('all')) || \Auth::user()->can('bbapplicant_edit')) {
                $editperm = true;
            }
            $table->withGlobalSearch()
            ->column('created_at', 'Timestamp', searchable: true, sortable: true, extra:['type' => 'datePicker','showhide' => ['date','time'],'colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
            ->column(label: 'Date', hidden:true, extra:['hidden' => true, 'colKey' => ($NextColKey = Helper::NextColKey($NextColKey)), 'width' => '60px'])
            ->column(label: 'Time', hidden:true, extra:['hidden' => true, 'colKey' => ($NextColKey = Helper::NextColKey($NextColKey))]);

            if((\Auth::user()->can('all'))) {
                $table
                ->column('BUniqueID', 'BUniqueID', searchable: true, sortable: true, extra:['width' => '90px','info' => 'BUniqueID', 'editable' => $editperm, 'colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
                ->column('BUNO', 'BUNO', searchable: true, sortable: true, extra:['width' => '60px','info' => 'BUNO','colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
                ->column('full_bio', 'Full Bio', sortable: true, searchable: true, extra:['width' => '80px', 'showhide' => ['full_name','gender','dob','ethnicity','nationality'], 'editable' => $editperm ,'info' => 'COMPILE BIODATA W NAME','colKey' => ($NextColKey = Helper::NextColKey($NextColKey))]);
            } else {
                $table
                ->column('BUniqueID', 'BUniqueID', searchable: true, sortable: true, extra:['width' => '90px','info' => 'BUniqueID',  'colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
                ->column('BUNO', 'BUNO', searchable: true, sortable: true, extra:['width' => '60px','info' => 'BUNO','colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
                ->column('full_bio', 'Full Bio', sortable: true, searchable: true, extra:['width' => '80px', 'showhide' => ['full_name','gender','dob','ethnicity','nationality'], 'info' => 'COMPILE BIODATA W NAME','colKey' => ($NextColKey = Helper::NextColKey($NextColKey))]);
            }

            $table

            ->column('full_name', 'Full Name', searchable: true, sortable: true, hidden:true, extra:['width' => '75px','info' => 'Full Name (as per identity card or passport) / 名字','hidden' => true,'colKey' => ($NextColKey = Helper::NextColKey($NextColKey)), 'editable' => $editperm,])
            ->column('gender', 'Gender', hidden:true, searchable: true, sortable: true, extra:['width' => '67px','hidden' => true, 'editable' => $editperm, 'freetext' => 'input', 'type' => 'select', 'options' => [['id' => '','label' => 'Gender'],['id' => 'Male / 男','label' => 'Male / 男'],['id' => 'Female / 女','label' => 'Female / 女']], 'info' => 'Gender / 男女', 'colKey' => ($NextColKey = Helper::NextColKey($NextColKey)) ])
            ->column('dob', 'Bday', searchable: true, sortable: true, hidden:true, extra:['width' => '40px', 'hidden' => true, 'info' => 'Birthday (16 to 75 preferred) / 年龄', 'type' => 'datePicker', 'editable' => $editperm, 'freetext' => 'input', 'colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
            ->column('ethnicity', 'Ethnicity', hidden:true, searchable: true, sortable: true, extra:['width' => '78px','hidden' => true, 'editable' => $editperm, 'freetext' => 'input', 'type' => 'select', 'options' => [['id' => '','label' => 'Ethnicity'],['id' => 'Chinese / 华人','label' => 'Chinese / 华人'],['id' => 'Malay','label' => 'Malay'],['id' => 'Indian','label' => 'Indian'],['id' => 'Filipino','label' => 'Filipino'],['id' => 'Vietnam','label' => 'Vietnam'],['id' => 'Burmese','label' => 'Burmese']], 'info' => 'Ethnicity', 'colKey' => ($NextColKey = Helper::NextColKey($NextColKey)) ])
            ->column('nationality', 'Nationality', hidden:true, searchable: true, sortable: true, extra:['width' => '92px','hidden' => true, 'editable' => $editperm, 'freetext' => 'input', 'type' => 'select', 'options' => [['id' => '','label' => 'Nationality'],['id' => 'Singapore Citizen / 新加坡人公民','label' => 'Singapore Citizen / 新加坡人公民'],['id' => 'PR / 永久住民','label' => 'PR / 永久住民'],['id' => 'Foreigner / 外劳','label' => 'Foreigner / 外劳'],['id' => 'LTVP (Long Term Visit Pass)','label' => 'LTVP (Long Term Visit Pass)']], 'info' => 'Nationality / 国家', 'colKey' => ($NextColKey = Helper::NextColKey($NextColKey)) ]);



            if((\Auth::user()->can('all'))) {
                $table
                ->column('aceptdate', 'Welcomed', searchable: true, sortable: true, extra:['width' => '89px','info' => 'COL FOR THOSE WHOM WELCOME EMAIL WAS SENT', 'type' => 'datePicker', 'editable' => $editperm, 'freetext' => 'input', 'colKey' => ($NextColKey = Helper::NextColKey($NextColKey))]);

            } else {
                $table
                ->column('aceptdate', 'Welcomed', searchable: true, sortable: true, extra:['width' => '89px','info' => 'COL FOR THOSE WHOM WELCOME EMAIL WAS SENT', 'type' => 'datePicker', 'freetext' => 'input', 'colKey' => ($NextColKey = Helper::NextColKey($NextColKey))]);
            }
            $table

            ->column('bal', 'Bal', searchable: true, sortable: true, extra:['width' => '40px','info' => 'Balance (B=blank, 0=$0, BUM=bbapplicant not validated)','colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
            ->column('review', 'Reviews', searchable: true, sortable: true, extra:['info' => 'Reviews About the Sitter','editable' => $editperm, 'dispval' => 'dataval', 'colKey' => ($NextColKey = Helper::NextColKey($NextColKey)), 'width' => '80px'])

            ->column('comment', 'Comment', searchable: true, sortable: true, extra:['info' => 'Internal Comments: This column is confidential information, free text, keyed in by coordinators regarding the bbapplicant','editable' => $editperm, 'type' => 'textarea', 'colKey' => ($NextColKey = Helper::NextColKey($NextColKey)), 'width' => '100px']);

            if((\Auth::user()->can('all'))) {
                $table
                ->column('BBSCR', 'BBSCR', searchable: true, sortable: true, extra:['width' => '75px', 'editable' => $editperm,'info' => 'Bbapplicant Score: This column is confidential information, the performance score of the bbapplicant, keyed in by coordinators regarding the bbapplicant. It should be -99.99 to 99.99 (two demical places)','colKey' => ($NextColKey = Helper::NextColKey($NextColKey))]);
            } else {
                $NextColKey = Helper::NextColKey($NextColKey);
            }
            $table
            ->column('WA-BL', 'WA-BL', searchable: true, sortable: true, extra:['width' => '60px', 'editable' => $editperm,'info' => '(input "1" for broadcast list XXX under chloe 94816597 phone, or "2" for XXXX list under ABC phone)','colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
            ->column('whts_no', 'WA', searchable: true, sortable: true, extra:['width' => '60px', 'editable' => $editperm,'info' => 'Phone number / 电话号码','colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
            ->column('whts_no2', 'WA2', searchable: true, sortable: true, extra:['width' => '60px', 'editable' => $editperm,'info' => '2nd phone number / 第二电话号码 (For WhatsApp)','colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
            ->column('email', 'Email', searchable: true, sortable: true, extra:['width' => '60px', 'editable' => $editperm,'info' => 'Bbapplicant Email Address','colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
            ->column('exp', 'Exp', searchable: true, sortable: true, extra:['width' => '150px', 'type' => 'textarea', 'editable' => $editperm,'info' => 'Where were your previous experiences taking care of baby or kids? For how long?','colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
            ->column('bonustask', 'Bonustask', searchable: true, sortable: true, extra:['info' => 'Please tick the items that you are able to help with as a bonus task (additional help):','editable' => $editperm, 'freetext' => 'textarea', 'type' => 'checkbox', 'options' => [['id' => 'Caring For Mother (Breastfeeding)','label' => 'Caring For Mother (Breastfeeding)'],['id' => 'Caring For Mother (Pregnant)','label' => 'Caring For Mother (Pregnant)'],['id' => 'Doing General Basic Housework (Sweep, Mop, Fold Clothes, Clear Trashbins, Etc)','label' => 'Doing General Basic Housework (Sweep, Mop, Fold Clothes, Clear Trashbins, Etc)'],['id' => 'Doing All Types of Cleaning (Eg. Wash Toilet, Kitchen)','label' => 'Doing All Types of Cleaning (Eg. Wash Toilet, Kitchen)'],['id' => 'Doing Basic Laundry (Wash with Machine, Hang and Fold)','label' => 'Doing Basic Laundry (Wash with Machine, Hang and Fold)'],['id' => 'Handwash Laundry (with gloves and basin, equipment all present)','label' => 'Handwash Laundry (with gloves and basin, equipment all present)'],['id' => 'Ironing','label' => 'Ironing'],['id' => 'Cooking (Basic: Fried Rice, Maggie, Egg)','label' => 'Cooking (Basic: Fried Rice, Maggie, Egg)'],['id' => 'Cooking (Regular: Meat, Vegetables, Rice)','label' => 'Cooking (Regular: Meat, Vegetables, Rice)'],],'colKey' => ($NextColKey = Helper::NextColKey($NextColKey)), 'width' => '100px'])
            ->column('workloc', 'Workloc', searchable: true, sortable: true, extra:['info' => 'Preferred Locations To Work / 工作地方','editable' => $editperm, 'freetext' => 'textarea', 'type' => 'checkbox', 'options' => [['id' => 'No Preference / 随便','label' => 'No Preference / 随便'],['id' => 'North /北','label' => 'North /北'],['id' => 'South / 南','label' => 'South / 南'],['id' => 'East / 东','label' => 'East / 东'],['id' => 'West / 西','label' => 'West / 西'],['id' => 'Central / 中央区','label' => 'Central / 中央区']],'colKey' => ($NextColKey = Helper::NextColKey($NextColKey)), 'width' => '100px'])
            ->column('wheretodo', 'WhereAbleTodo', searchable: true, sortable: true, extra:['info' => 'Where are you able to do babysitting?','editable' => $editperm, 'type' => 'select', 'freetext' => 'input', 'options' => [['id' => '','label' => 'WhereAbleTodo'],['id' => 'Both At Client House AND My House','label' => 'Both At Client House AND My House'],['id' => 'Only At Client House (See Below Location to Work)','label' => 'Only At Client House (See Below Location to Work)'],['id' => 'Only At My House (Indicate Your Location to Work Below Too!)','label' => 'Only At My House (Indicate Your Location to Work Below Too!)']], 'colKey' => ($NextColKey = Helper::NextColKey($NextColKey)), 'width' => '100px'])
            ->column('day_avl', 'Days Available', searchable: true, sortable: true, extra:['info' => 'Days Available','editable' => $editperm, 'type' => 'textarea','colKey' => ($NextColKey = Helper::NextColKey($NextColKey)), 'width' => '80px'])
            ->column('fulltime', 'Full Time', searchable: true, sortable: true, extra:['width' => '55px','type' => 'select', 'info' => 'Are you also keen to join full-time? We will send you details if you are keen.', 'editable' => $editperm, 'freetext' => 'input', 'colKey' => ($NextColKey = Helper::NextColKey($NextColKey)),'options' => [['id' => '','label' => 'Full Time'],['id' => 'Yes','label' => 'Yes'],['id' => 'No','label' => 'No'],['id' => 'Maybe','label' => 'Maybe']]])

            ->column(label: 'Notify', extra:[ 'showhide' => ['telegram','whatsapp','remail'],'info' => 'Preferences for Receiving Notifications','colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
            ->column('telegram', 'R-tele', searchable: true, sortable: true, hidden:true, extra:['width' => '60px', 'hidden' => true, 'type' => 'select','editable' => $editperm, 'freetext' => 'input', 'options' => [['id' => '','label' => 'R-tele'],['id' => 'Yes','label' => 'Yes'],['id' => 'No','label' => 'No']], 'info' => 'Do you wish to receive notification for available jobs (eg babysitting) via Telegram?','colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
            ->column('whatsapp', 'R-WA', searchable: true, sortable: true, hidden:true, extra:['width' => '60px', 'hidden' => true, 'type' => 'select','editable' => $editperm, 'freetext' => 'input', 'options' => [['id' => '','label' => 'R-WA'],['id' => 'Yes','label' => 'Yes'],['id' => 'No','label' => 'No']], 'info' => 'Do you wish to receive notification for available jobs (eg babysitting) via WhatsApp?','colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
            ->column('remail', 'R-email', searchable: true, sortable: true, hidden:true, extra:['width' => '60px', 'hidden' => true, 'type' => 'select','editable' => $editperm, 'freetext' => 'input', 'options' => [['id' => '','label' => 'R-email'],['id' => 'Yes','label' => 'Yes'],['id' => 'No','label' => 'No']],'info' => 'Do you wish to receive notification for available jobs (eg babysitting) via Email?','colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])

            ->column(label: '$info', extra:['showhide' => ['paynownum','bankname','banknumb'],'info' => 'Payment Details For Sitter','colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
            ->column('paynownum', 'PayNowN', searchable: true, sortable: true, hidden:true, extra:['width' => '85px', 'hidden' => true, 'editable' => $editperm,'info' => 'Your PayNow Number','colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
            ->column('bankname', 'BankName', searchable: true, sortable: true, hidden:true, extra:['width' => '85px','hidden' => true,  'type' => 'select','editable' => $editperm, 'freetext' => 'input', 'options' => [['id' => '','label' => 'BankName'],['id' => 'DBS POSB','label' => 'DBS POSB'],['id' => 'OCBC','label' => 'OCBC'],['id' => 'UOB','label' => 'UOB'],['id' => 'CitiBank','label' => 'CitiBank'],['id' => 'Standard Chartered','label' => 'Standard Chartered'],['id' => 'MayBank','label' => 'MayBank'],],'info' => 'Your Bank Name','colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
            ->column('banknumb', 'BankNumb', searchable: true, sortable: true, hidden:true, extra:['width' => '85px', 'hidden' => true, 'editable' => $editperm,'info' => 'Your Full Bank Account Number','colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])

            ->column(label: 'NOK', extra:['showhide' => ['NOKrs','NOKname','NOKhp'],'info' => 'Next of Kin Details','colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
            ->column('NOKrs', 'NOKrs', searchable: true, sortable: true, hidden:true, extra:['width' => '61px','hidden' => true,  'editable' => $editperm,'info' => 'NEXT OF KIN','colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
            ->column('NOKname', 'NOKname', searchable: true, sortable: true, hidden:true, extra:['width' => '86px', 'hidden' => true, 'editable' => $editperm,'info' => 'NOK Name','colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])
            ->column('NOKhp', 'NOKhp', searchable: true, sortable: true, hidden:true, extra:['width' => '66px', 'hidden' => true, 'editable' => $editperm,'info' => 'NOK Contact','colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])

            ->column('resid', 'resid', searchable: true, sortable: true, extra:['width' => '105px','type' => 'select', 'info' => 'Residence', 'editable' => $editperm, 'freetext' => 'input', 'colKey' => ($NextColKey = Helper::NextColKey($NextColKey)),'options' => [['id' => '','label' => 'resid'],['id' => 'I am staying in Singapore (SG) now.','label' => 'I am staying in Singapore (SG) now.'],['id' => 'I am NOT in Singapore now but have a place to stay in SG.','label' => 'I am NOT in Singapore now but have a place to stay in SG.'],['id' => 'I am NOT in Singapore and do not have a place to stay in SG','label' => 'I am NOT in Singapore and do not have a place to stay in SG']]]);

            if((\Auth::user()->can('all'))) {
                $table
                ->column('cav', 'Caveat', searchable: true, sortable: true, extra:['width' => '65px','type' => 'select', 'info' => 'Caveat', 'editable' => $editperm, 'freetext' => 'input', 'colKey' => ($NextColKey = Helper::NextColKey($NextColKey)),'options' => [['id' => '','label' => 'Caveat'],['id' => 'Yes','label' => 'Yes'],['id' => 'No','label' => 'No']]]);
            } else {
                $table
                ->column('cav', 'Caveat', searchable: true, sortable: true, extra:['width' => '65px','type' => 'select', 'info' => 'Caveat',  'freetext' => 'input', 'colKey' => ($NextColKey = Helper::NextColKey($NextColKey)),'options' => [['id' => '','label' => 'Caveat'],['id' => 'Yes','label' => 'Yes'],['id' => 'No','label' => 'No']]]);
            }
            $table
            ->column('last_edited', 'LastEdit', searchable: true, sortable: true, extra:['type' => 'select', 'options' => $allUserAll,'colKey' => ($NextColKey = Helper::NextColKey($NextColKey))])


            ->dateFilter(key: 'created_at_start', label: 'Entry Date From')
            ->dateFilter(key: 'created_at_end', label: 'Entry Date To')
            ->dateFilter(key: 'dob_start', label: 'Birth day From')
            ->dateFilter(key: 'dob_end', label: 'Birth day To')
            ->dateFilter(key: 'aceptdate_start', label: 'Welcome From')
            ->dateFilter(key: 'aceptdate_end', label: 'Welcome To')
            ;

            if(!(\Auth::user()->can('all')) && \Auth::user()->can('bbapplicant_DisViewAllRow')) {
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
    public function destroy(Bbapplicant $bbapplicant)
    {
        $uname = $bbapplicant->id;
        $bbapplicant->delete();
        \ActivityLog::add(['action' => 'deleted','module' => 'bbapplicant','data_key' => $uname, 'data_ref' => $bbapplicant->id,]);
        return redirect()->back()->with(['message' => 'Bbapplicant Deleted !!','msg_type' => 'warning']);
    }

    public function fieldUpdate(Request $request)
    {
        $notAllowedBlank = ['email','whts_no'];
        $saved_value = $request->value;
        if(in_array($request->key, $notAllowedBlank) &&  $saved_value == '') {
            return redirect()->back()->with(['message' => 'Value can not be blank !!','msg_type' => 'danger']);
        } elseif($request->key == 'email') {
            if (!filter_var($saved_value, FILTER_VALIDATE_EMAIL)) {
                return redirect()->back()->with(['message' => 'Email format not valid !!','msg_type' => 'danger']);
            }
        } elseif($request->key == 'aceptdate') {
            $exvalue = explode('-', $saved_value);
            if(count($exvalue) == 3) {
                $aceptdateev = ($saved_value instanceof Carbon) ? $saved_value : Carbon::parse($saved_value);
                $saved_value = $aceptdateev->format('Y-m-d');
            }
        } elseif($request->key == 'BBSCR' || $request->key == 'WA-BL') {
            if($request->value == '') {
                $saved_value = null;
            } elseif(!preg_match('/^[0-9 .-]+$/', $saved_value)) {
                return redirect()->back()->with(['message' => $request->key . ' not valid !!','msg_type' => 'danger']);
            } elseif($saved_value > 99.99 || $saved_value < -99.99) {
                return redirect()->back()->with(['message' => $request->key . ' not valid !!','msg_type' => 'danger']);
            }
        } elseif($request->key == 'bonustask' || $request->key == 'workloc') {
            $saved_value = is_array($request->value) ? implode(PHP_EOL, $request->value) : $request->value;
        } elseif($request->key == 'dob') {
            if($request->value == '') {
                $request->value = null;
            } elseif (!(preg_match("/^[0-9]{4}-(0?[1-9]|1[0-2])-(0?[1-9]|[1-2][0-9]|3[0-1])$/", $request->value))) {
                return redirect()->back()->with(['message' => 'Wrong Format! Please key in format YYYY-MM-DD','msg_type' => 'danger']);
            }
        }



        $bbapplicant = Bbapplicant::find($request->id);
        $prev_value = $bbapplicant->{$request->key};
        $bbapplicant->{$request->key} = $saved_value;

        if($bbapplicant->aceptdate && ($request->key == 'full_name' || $request->key == 'email' ||  $request->key == 'whts_no')) {
            $bbapplicant->BUniqueID = substr(str_replace(' ', '', $bbapplicant->full_name), 0, 6) . $bbapplicant->BUNO . 'B' . substr($bbapplicant->email, 0, 3) . substr($bbapplicant->whts_no, -3);
        }

        if($request->key == 'full_name' || $request->key == 'gender' ||  $request->key == 'dob' || $request->key == 'ethnicity' || $request->key == 'nationality') {
            $bbapplicant->full_bio = $bbapplicant->full_name . ' ' . ($bbapplicant->gender == 'Male / 男' ? 'Male' : ($bbapplicant->gender == 'Female / 女' ? 'Female' : $bbapplicant->gender)) . ' ' . date('ymd', strtotime($bbapplicant->dob)) . ' ' . ($bbapplicant->ethnicity == 'Chinese / 华人' ? 'Chi' : $bbapplicant->ethnicity) . ' ' . ($bbapplicant->nationality == 'Singapore Citizen / 新加坡人公民' ? 'SG' : ($bbapplicant->nationality == 'PR / 永久住民' ? 'PR' : ($bbapplicant->nationality == 'Foreigner / 外劳' ? 'Foreign' : ($bbapplicant->nationality == 'LTVP (Long Term Visit Pass)' ? 'LTVP' : $bbapplicant->nationality)))) . ' Meide BB Join' ;
        }

        $isDirty = false;
        if($bbapplicant->isDirty()) {
            $bbapplicant->last_edited = Auth::user()->id;
            $bbapplicant->save();
            $isDirty = true;
        }

        if($isDirty) {
            $uname = $request->key;
            \ActivityLog::add(['action' => 'updated','module' => 'bbapplicant','data_key' => $uname, 'data_id' => $request->id, 'data_ref' => $request->ref, 'data_val' => $prev_value]);
            //broadcast(new MedideNotification($request->ref . ' Updated By ' . Auth::user()->name, 'bbapplicant'))->toOthers();
            $res = ['message' => 'Updated Successfully.','msg_type' => 'info'];
        } else {
            $res = ['message' => 'No Value  Updated in BB applicant .','msg_type' => 'warning'];
        }
        return redirect()->back()->with($res);
    }

    /**
     * replicate.
     */
    public function replicate($id)
    {
        $bbapplicant = Bbapplicant::find($id);
        for($i = 0; $i < request('nx');$i++) {
            $newBbapplicant = $bbapplicant->replicate();
            $newBbapplicant->created_at = Carbon::now();
            $newBbapplicant->aceptdate = null;
            $newBbapplicant->last_edited = null;
            $newBbapplicant->save();
            $bbapplicant->BUNO = 'UNV';
            $bbapplicant->BUniqueID = 'Unvalidated';
            $newBbapplicant->full_bio = $bbapplicant->full_name . ' ' . ($bbapplicant->gender == 'Male / 男' ? 'Male' : ($bbapplicant->gender == 'Female / 女' ? 'Female' : $bbapplicant->gender)) . ' ' . date('ymd', strtotime($bbapplicant->dob)) . ' ' . ($bbapplicant->ethnicity == 'Chinese / 华人' ? 'Chi' : $bbapplicant->ethnicity) . ' ' . ($bbapplicant->nationality == 'Singapore Citizen / 新加坡人公民' ? 'SG' : ($bbapplicant->nationality == 'PR / 永久住民' ? 'PR' : ($bbapplicant->nationality == 'Foreigner / 外劳' ? 'Foreign' : ($bbapplicant->nationality == 'LTVP (Long Term Visit Pass)' ? 'LTVP' : $bbapplicant->nationality)))) . ' Meide BB Join' ;

            $newBbapplicant->save();
        }
        $uname = $newBbapplicant->id;
        \ActivityLog::add(['action' => 'duplicated','module' => 'bbapplicant','data_key' => $uname, 'data_ref' => $uname,]);
        return redirect()->back()->with(['message' => 'Bbapplicant Duplicated !!','msg_type' => 'info']);
    }

    /**
    * Bulk replicate.
    */
    public function bulkDuplicate()
    {
        $allDuplicate = [];
        foreach (request('ids') as  $id) {
            $bbapplicant = Bbapplicant::find($id);
            for($i = 0; $i < request('nx');$i++) {
                $newBbapplicant = $bbapplicant->replicate();
                $newBbapplicant->created_at = Carbon::now();
                $newBbapplicant->aceptdate = null;
                $newBbapplicant->last_edited = null;
                $newBbapplicant->save();
                $newBbapplicant->BUNO = 'UNV';
                $newBbapplicant->BUniqueID = 'Unvalidated';
                $newBbapplicant->full_bio = $bbapplicant->full_name . ' ' . ($bbapplicant->gender == 'Male / 男' ? 'Male' : ($bbapplicant->gender == 'Female / 女' ? 'Female' : $bbapplicant->gender)) . ' ' . date('ymd', strtotime($bbapplicant->dob)) . ' ' . ($bbapplicant->ethnicity == 'Chinese / 华人' ? 'Chi' : $bbapplicant->ethnicity) . ' ' . ($bbapplicant->nationality == 'Singapore Citizen / 新加坡人公民' ? 'SG' : ($bbapplicant->nationality == 'PR / 永久住民' ? 'PR' : ($bbapplicant->nationality == 'Foreigner / 外劳' ? 'Foreign' : ($bbapplicant->nationality == 'LTVP (Long Term Visit Pass)' ? 'LTVP' : $bbapplicant->nationality)))) . ' Meide BB Join' ;

                $newBbapplicant->save();
                $allDuplicate[] = $id;
            }
        }
        $uname = (count($allDuplicate) > 10) ? 'Many' : $uname = implode(',', $allDuplicate);
        \ActivityLog::add(['action' => 'duplicated', 'module' => 'bbapplicant', 'data_key' => $uname, 'data_ref' => $uname,]);
        return redirect()->back()->with(['message' => 'Selected BB Applicant(s) duplicated Successfully! Please sort by Newest/Latest/Timestamp to see the newly duplicated row(s) on top of table. ','msg_type' => 'info']);
    }

    /**
     * bulk delete.
     */
    public function bulkDestroy()
    {
        Bbapplicant::whereIn('id', request('ids'))->delete();
        $uname = (count(request('ids')) > 10) ? 'Many' : $uname = implode(',', request('ids'));
        \ActivityLog::add(['action' => 'deleted','module' => 'bbapplicant','data_key' => $uname, 'data_ref' => $uname,]);
        return redirect()->back()->with(['message' => 'Selected Bbapplicant Deleted !!','msg_type' => 'warning']);
    }

    /**
     *  bulkAccept.
     */
    public function bulkAccept()
    {
        $allgenMBJ = [];
        foreach (request('ids') as  $id) {
            $bbapplicant = Bbapplicant::find($id);
            if(!$bbapplicant->aceptdate) {
                $bbapplicant->last_edited = Auth::user()->id;
                $bbapplicant->aceptdate = date('Y-m-d');

                $curBUniqID = Setting::where('slug', 'curBUniqID')->firstOrFail();
                $curBUniqIDno = $curBUniqID->value;
                $curBUniqIDno++;
                $bbapplicant->BUNO = sprintf("%05d", $curBUniqIDno);
                $bbapplicant->BUniqueID = substr(str_replace(' ', '', $bbapplicant->full_name), 0, 6) . $bbapplicant->BUNO . 'B' . substr($bbapplicant->email, 0, 3) . substr($bbapplicant->whts_no, -3);

                $bbapplicant->save();

                Setting::where('slug', 'curBUniqID')->update(['value' => $curBUniqIDno]);
                $allgenMBJ[] = $id;

                // Bbapplicant::newWelcomeMail(
                //   [
                //       'toemail' => $bbapplicant->email,
                //   ]
                //);
            }
        }
        if(count($allgenMBJ) > 0) {
            $uname = (count($allgenMBJ) > 10) ? 'Many' : $uname = implode(',', $allgenMBJ);
            $uname2 = (count(request('ids')) > 10) ? 'Many' : $uname = implode(',', request('ids'));
            \ActivityLog::add(['action' => 'accepted','module' => 'bbapplicant','data_key' => $uname, 'data_ref' => $uname2,]);
            return redirect()->back()->with(['message' => 'Selected Bbapplicant Acceptd!! ' . implode(', ', $allgenMBJ),'msg_type' => 'info','rData' => $allgenMBJ]);
        } else {
            return redirect()->back()->with(['message' => 'Selected Bbapplicant Already Validated or Rejected !!','msg_type' => 'danger']);
        }

    }

    public function bulkReject()
    {
        $allgenMBJ = [];

        foreach (request('ids') as  $id) {
            $bbapplicant = Bbapplicant::find($id);
            if(!$bbapplicant->aceptdate) {
                $bbapplicant->last_edited = Auth::user()->id;
                $bbapplicant->aceptdate = 'Rej';

                $bbapplicant->BUNO = 'Rej0';
                $bbapplicant->BUniqueID = 'ID-Rej';

                $bbapplicant->save();
                $allgenMBJ[] = $id;

            }
        }
        if(count($allgenMBJ) > 0) {
            $uname = (count($allgenMBJ) > 10) ? 'Many' : $uname = implode(',', $allgenMBJ);
            \ActivityLog::add(['action' => 'rejected','module' => 'bbapplicant', 'data_key' => $uname, 'data_ref' => $uname,]);
            return redirect()->back()->with(['message' => 'Selected Bbapplicant Rejected !!','msg_type' => 'warning']);
        } else {
            return redirect()->back()->with(['message' => 'Selected Bbapplicants not qualify to Reject !!','msg_type' => 'danger']);
        }

    }

    public function export()
    {

        $rows = Bbapplicant::select('bbapplicants.*', 'u3.name AS last_edit_name')->leftJoin('users  AS u3', 'u3.id', 'bbapplicants.last_edited', 'left')->orderBy('id', 'DESC')->get();

        $fileName = 'BBApplicants.csv';

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = ['Timestamp','Date','Time','BUniqueID','BUNO','Full Bio','Full Name','Gender','Bday','Ethnicity','Nationality','Welcomed','Bal','Reviews','Comment','BBSCR','WA-BL','WA','WA2','Email','Exp','Bonustask','Workloc','WhereAbleTodo','Days Available','Full Time','Notify','R-tele','R-WA','R-email','$info','PayNowN','BankName','BankNumb','NOK','NOKrs','NOKname','NOKhp','resid','Caveat','LastEdit','Row Ref'
    ];

        $callback = function () use ($rows, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($rows as $row) {
                $dtex = explode(' ', $row->created_at);
                fputcsv($file, [$row->created_at, $dtex[0], $dtex[1], $row->BUniqueID, $row->BUNO, $row->full_bio, $row->full_name, $row->gender, $row->dob, $row->ethnicity, $row->nationality, $row->aceptdate ?? 'no-email-yet', $row->bal, $row->review, $row->comment, $row->BBSCR, $row->{'WA-BL'}, $row->whts_no, $row->whts_no2, $row->email, $row->exp, $row->bonustask, $row->workloc, $row->wheretodo, $row->day_avl, $row->fulltime, '', $row->telegram, $row->whatsapp, $row->remail,'', $row->paynownum, $row->bankname, $row->banknumb, '', $row->NOKrs, $row->NOKname, $row->NOKhp, $row->resid, $row->cav,  $row->last_edit_name,$row->id]);
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
        
        $data = Bbapplicant::distinct()->pluck($fieldName)->toArray();
        $data = array_values(array_filter($data)); // filter data
        $result = array_map(function($element) { return ['id'=> $element, 'label' => $element];}, $data);
        return response()->json(['message' => 'Field Data shown successfully !!', 'data' => $result]);
        
    }
}
