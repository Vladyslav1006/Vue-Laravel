<?php

namespace App\Http\Controllers\Admin;

use App\Models\JobSearch;
use App\Models\OtherEty;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class OtherEtyController extends Controller
{
    /**
     * replace.
     */
    public function replace(Request $request)
    {
        $pre_ety = OtherEty::selectRaw('GROUP_CONCAT(CONCAT(if(ety!="",ety,""),"(", if(label!="",label,""),")")  ORDER BY id SEPARATOR "::") as combineddety')-> where('jobId', $request->jobid)->groupBy("jobId")->first();
        $cur_ety = [];
        $ids=[];
       // OtherPhone::where('jobId', $request->jobid)->delete();
        JobSearch::where('id', $request->jobid)->update(['last_edited' => Auth::user()->id]);
        foreach ($request->ety as $key => $value) {
            if(!empty($value['ety'])) {
                if($value['primary']==true){
                    OtherEty::where('jobId', $request->jobid)->update(['is_primary' => 0]);
                    JobSearch::where('id', $request->jobid)->update(['ety' => $value['ety']]);
                    $value['primary']=1;
                }
                $ids[]=$this->findAndUpdateEmail($request->jobid, $value['ety'], ['is_primary'=>$value['primary'], 'label'=>$value['lb']], $value['id']);
                $cur_ety[] = $value['ety'] . "(" . $value['lb'] . ")";
            }
        }
        OtherEty::where('jobId', $request->jobid)->whereNotIn('id', $ids)->delete();
        $lcn=JobSearch::updateLCN($request->jobid);
        $cur_ety_str = implode('::', $cur_ety);
        $pre_ety_str = ($pre_ety == null && empty($pre_ety->combineddety)) ? '' : $pre_ety->combineddety;
        if($pre_ety_str != $cur_ety_str) {
            \ActivityLog::add(['action' => 'updated', 'module' => 'jobsearch','data_key' => 'other-ety', 'data_id' => $request->jobid, 'data_ref' => $request->cellref, 'data_val' => $pre_ety_str]);
        }
    }

    public function otherEty(Request $request)
    {
        $jobSearch=JobSearch::where('id', $request->jobid)->first(['ety']);
        if(!empty($jobSearch)){
            $data=['is_primary'=>1];
            $this->findAndUpdateEmail($request->jobid, $jobSearch->ety, $data);
        }
        return  OtherEty::where('jobId', $request->jobid)->orderBy('is_primary', 'DESC')->orderBy('id', 'ASC')->get();
    }

    protected function findAndUpdateEmail($jobId, $ety, $data, $id=0){
       if($id>0){
         $etyData=OtherEty::find($id);
       }else{
        $etyData=OtherEty::where([['jobId', $jobId], ['ety', $ety]])->first();
       }
       $etyData=empty($etyData) ? new OtherEty : $etyData;
       $etyData->ety=$ety;
       $etyData->jobId=$jobId;
       $etyData->is_primary=$data['is_primary']==1?1:0;
       if(!empty($data['label'])){
         $etyData->label=$data['label'];
       }
       $etyData->save();
       return $etyData->id;
    }
}
