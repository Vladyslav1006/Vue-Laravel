<?php

namespace App\Http\Controllers\Admin;

use App\Models\JobSearch;
use App\Models\AllocatedBB;
use App\Models\Bbapplicant;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AllocatedBBController extends Controller
{
    /**
     * replace.
     */

    public function save(Request $request)
    {
        $pre_members = AllocatedBB::selectRaw('GROUP_CONCAT(CONCAT("(",allocated_b_b_s.rank,")(", bbapl.BUniqueID,")(",if(allocated_b_b_s.remark!="",allocated_b_b_s.remark,""),")")  ORDER BY allocated_b_b_s.rank SEPARATOR " /// ") as allctbbs')->leftJoin('bbapplicants  AS bbapl', 'bbapl.id', 'baysitterId', 'left')-> where('jobId', $request->jobid)->groupBy("jobId")->first();

        AllocatedBB::create(['jobId' => $request->jobid, 'baysitterId' => $request->baysitterId, 'rank' => $request->rank, 'remark' => $request->remark]);
        JobSearch::where('id', $request->jobid)->update(['last_edited' => Auth::user()->id]);

        $pre_members_str = ($pre_members == null && empty($pre_members->allctbbs)) ? '' : $pre_members->allctbbs;
        \ActivityLog::add(['action' => 'updated', 'module' => 'jobsearch','data_key' => 'a-l-l-o-c-a-t-e-d-b-b', 'data_id' => $request->jobid, 'data_ref' => $request->cellref, 'data_val' => $pre_members_str]);
    }
    public function delete(Request $request)
    {
        $pre_members = AllocatedBB::selectRaw('GROUP_CONCAT(CONCAT("(",allocated_b_b_s.rank,")(", bbapl.BUniqueID,")(",if(allocated_b_b_s.remark!="",allocated_b_b_s.remark,""),")")  ORDER BY allocated_b_b_s.rank SEPARATOR " /// ") as allctbbs')->leftJoin('bbapplicants  AS bbapl', 'bbapl.id', 'baysitterId', 'left')-> where('jobId', $request->jobid)->groupBy("jobId")->first();

        AllocatedBB::where('id', $request->id)->delete();
        JobSearch::where('id', $request->jobid)->update(['last_edited' => Auth::user()->id]);
        $pre_members_str = ($pre_members == null && empty($pre_members->allctbbs)) ? '' : $pre_members->allctbbs;
        \ActivityLog::add(['action' => 'updated', 'module' => 'jobsearch','data_key' => 'a-l-l-o-c-a-t-e-d-b-b', 'data_id' => $request->jobid, 'data_ref' => $request->cellref, 'data_val' => $pre_members_str]);

    }
    public function allocatedbbs(Request $request)
    {
        return
        AllocatedBB::select('allocated_b_b_s.id as alocbbid', 'allocated_b_b_s.rank', 'allocated_b_b_s.remark', 'allocated_b_b_s.baysitterId', 'u1.*', )->leftJoin('bbapplicants AS u1', 'u1.id', 'allocated_b_b_s.baysitterId', 'left')->where('jobId', $request->jobid)->orderBy('allocated_b_b_s.rank', 'ASC')->get();

    }
    public function getCandidates(Request $request)
    {
        return empty($request->keystr) ? [] :
        Bbapplicant::selectRaw("bbapplicants.id, CONCAT(BUniqueID, '-', full_name) as label")->where('BUniqueID', '!=', 'Unvalidated')->where('BUniqueID', 'LIKE', "{$request->keystr}%")->orderBy('BUniqueID', 'ASC')->get();

    }
    public function getCandidatesAll()
    {
        return Bbapplicant::selectRaw("bbapplicants.id, CONCAT(BUniqueID, '-', full_name) as label")->where('BUniqueID', '!=', 'Unvalidated')->orderBy('BUniqueID', 'ASC')->get();

    }
    public function changeremark(Request $request)
    {
        AllocatedBB::where('id', $request->id)->update(['remark' => $request->remark]);
        JobSearch::where('id', $request->jobid)->update(['last_edited' => Auth::user()->id]);
    }
    public function changeRankNum(Request $request)
    {
        AllocatedBB::where('id', $request->id)->update(['rank' => $request->rank]);
        JobSearch::where('id', $request->jobid)->update(['last_edited' => Auth::user()->id]);
    }


    public function changeRank(Request $request)
    {
        $pre_members = AllocatedBB::selectRaw('GROUP_CONCAT(CONCAT("(",allocated_b_b_s.rank,")(", bbapl.BUniqueID,")(",if(allocated_b_b_s.remark!="",allocated_b_b_s.remark,""),")")  ORDER BY allocated_b_b_s.rank SEPARATOR " /// ") as allctbbs')->leftJoin('bbapplicants  AS bbapl', 'bbapl.id', 'baysitterId', 'left')-> where('jobId', $request->jobid)->groupBy("jobId")->first();

        $prerank = AllocatedBB::select('rank')->where('jobId', $request->jobid)->orderBy('rank')->get();
        JobSearch::where('id', $request->jobid)->update(['last_edited' => Auth::user()->id]);
        foreach ($request->ranks as $key => $rank) {
            AllocatedBB::where('id', $rank['id'])->update(['rank' => $prerank[$key]->rank]);
        }

        $pre_members_str = ($pre_members == null && empty($pre_members->allctbbs)) ? '' : $pre_members->allctbbs;
        \ActivityLog::add(['action' => 'updated', 'module' => 'jobsearch','data_key' => 'a-l-l-o-c-a-t-e-d-b-b', 'data_id' => $request->jobid, 'data_ref' => $request->cellref, 'data_val' => $pre_members_str]);
    }

}
