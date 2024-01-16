<?php

namespace App\Http\Controllers\Admin;

use App\Models\JobSearch;
use App\Models\OtherPhone;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class OtherPhoneController extends Controller
{
    /**
     * replace.
     */
    public function replace(Request $request)
    {
        $pre_phone = OtherPhone::selectRaw('GROUP_CONCAT(CONCAT(if(phone!="",phone,""),"(", if(label!="",label,""),")")  ORDER BY id SEPARATOR "::") as combinedphone')-> where('jobId', $request->jobid)->groupBy("jobId")->first();
        $cur_phone = [];
        $ids=[];
        //OtherPhone::where('jobId', $request->jobid)->delete();
        JobSearch::where('id', $request->jobid)->update(['last_edited' => Auth::user()->id]);

        foreach ($request->phones as $key => $value) {
            if(!empty($value['num'])) {
                //OtherPhone::create(['jobId' => $request->jobid, 'phone' => $value['num'], 'label' => $value['lb']]);
                if($value['primary']==true){
                    OtherPhone::where('jobId', $request->jobid)->update(['is_primary' => 0]);
                    JobSearch::where('id', $request->jobid)->update(['job_phn' => $value['num']]);
                    $value['primary']=1;
                }
                $ids[]=$this->findAndUpdateEmail($request->jobid, $value['num'], ['is_primary'=>$value['primary'], 'label'=>$value['lb']], $value['id']);
                $cur_phone[] = $value['num'] . "(" . $value['lb'] . ")";
            }
        }
        OtherPhone::where('jobId', $request->jobid)->whereNotIn('id', $ids)->delete();
        $lcn=JobSearch::updateLCN($request->jobid);
        $cur_phone_str = implode('::', $cur_phone);
        $pre_phone_str = ($pre_phone == null && empty($pre_phone->combinedphone)) ? '' : $pre_phone->combinedphone;
        if($pre_phone_str != $cur_phone_str) {
            \ActivityLog::add(['action' => 'updated', 'module' => 'jobsearch','data_key' => 'other-phone-numbers', 'data_id' => $request->jobid, 'data_ref' => $request->cellref, 'data_val' => $pre_phone_str]);
        }
    }

    public function otherPhone(Request $request)
    {
        $jobSearch=JobSearch::where('id', $request->jobid)->first(['job_phn']);
        if(!empty($jobSearch)){
            $data=['is_primary'=>1];
            $this->findAndUpdateEmail($request->jobid, $jobSearch->job_phn, $data);
        }
        return  OtherPhone::where('jobId', $request->jobid)->orderBy('is_primary', 'DESC')->orderBy('id', 'ASC')->get();
    }

    protected function findAndUpdateEmail($jobId, $phn, $data, $id=0){
        if($id>0){
          $phData=OtherPhone::find($id);
        }else{
         $phData=OtherPhone::where([['jobId', $jobId], ['phone', $phn]])->first();
        }
        $phData=empty($phData) ? new OtherPhone : $phData;
        $phData->phone=$phn;
        $phData->jobId=$jobId;
        $phData->is_primary=$data['is_primary']==1?1:0;
        if(!empty($data['label'])){
          $phData->label=$data['label'];
        }
        $phData->save();
        return $phData->id;
    }
 
}
