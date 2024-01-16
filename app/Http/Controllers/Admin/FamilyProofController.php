<?php

namespace App\Http\Controllers\Admin;

use App\Models\JobSearch;
use App\Models\FamilyProof;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class FamilyProofController extends Controller
{
    /**
     * replace.
     */
    public function replace(Request $request)
    {
        $pre_members = FamilyProof::selectRaw('GROUP_CONCAT(CONCAT(if(member!="",member,""),"(", if(nati!="",nati,""),")","(", if(occup!="",occup,""),")")  ORDER BY id SEPARATOR "::") as members')-> where('jobId', $request->jobid)->groupBy("jobId")->first();
        $cur_members = [];
        JobSearch::where('id', $request->jobid)->update(['declineFProof' => $request->declineFProof ?? null, 'last_edited' => Auth::user()->id]);
        FamilyProof::where('jobId', $request->jobid)->delete();
        foreach ($request->members as $key => $value) {
            if(!empty($value['member'])) {
                FamilyProof::create(['jobId' => $request->jobid, 'member' => $value['member'], 'nati' => $value['nati'], 'occup' => $value['occup']]);
                $cur_members[] = $value['member'] . "(" . $value['nati'] . ")" . "(" . $value['occup'] . ")";
            }
        }
        $cur_members_str = implode('::', $cur_members);
        $pre_members_str = ($pre_members == null && empty($pre_members->members)) ? '' : $pre_members->members;
        if($pre_members_str != $cur_members_str) {
            \ActivityLog::add(['action' => 'updated', 'module' => 'jobsearch','data_key' => 'familyprof', 'data_id' => $request->jobid, 'data_ref' => $request->cellref, 'data_val' => $pre_members_str]);
        }
    }

    public function familyProofs(Request $request)
    {
        return  ['declineFProof' => DB::table('job_searches')->select('declineFProof')->where('id', $request->jobid)->first()->declineFProof,'data' => FamilyProof::where('jobId', $request->jobid)->orderBy('id', 'ASC')->get()];
    }
}
