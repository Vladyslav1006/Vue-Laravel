<?php

namespace App\Http\Controllers\Admin;

use App\Models\JobSearch;
use App\Models\OtherEmail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class OtherEmailController extends Controller
{
    /**
     * replace.
     */
    public function replace(Request $request)
    {
        $pre_email = OtherEmail::selectRaw('GROUP_CONCAT(CONCAT(if(email!="",email,""),"(", if(label!="",label,""),")")  ORDER BY id SEPARATOR "::") as combineddmail')-> where('jobId', $request->jobid)->groupBy("jobId")->first();
        $cur_email = [];
        $ids=[];
       // OtherPhone::where('jobId', $request->jobid)->delete();
        JobSearch::where('id', $request->jobid)->update(['last_edited' => Auth::user()->id]);
        foreach ($request->emails as $key => $value) {
            if(!empty($value['email'])) {
                if($value['primary']==true){
                    OtherEmail::where('jobId', $request->jobid)->update(['is_primary' => 0]);
                    JobSearch::where('id', $request->jobid)->update(['job_email' => $value['email']]);
                    $value['primary']=1;
                }
                $ids[]=$this->findAndUpdateEmail($request->jobid, $value['email'], ['is_primary'=>$value['primary'], 'label'=>$value['lb']], $value['id']);
                $cur_email[] = $value['email'] . "(" . $value['lb'] . ")";
            }
        }
        OtherEmail::where('jobId', $request->jobid)->whereNotIn('id', $ids)->delete();
        $lcn=JobSearch::updateLCN($request->jobid);
        $cur_email_str = implode('::', $cur_email);
        $pre_email_str = ($pre_email == null && empty($pre_email->combineddmail)) ? '' : $pre_email->combineddmail;
        if($pre_email_str != $cur_email_str) {
            \ActivityLog::add(['action' => 'updated', 'module' => 'jobsearch','data_key' => 'other-emails', 'data_id' => $request->jobid, 'data_ref' => $request->cellref, 'data_val' => $pre_email_str]);
        }
    }

    public function otherEmail(Request $request)
    {
        $jobSearch=JobSearch::where('id', $request->jobid)->first(['job_email']);
        if(!empty($jobSearch)){
            $data=['is_primary'=>1];
            $this->findAndUpdateEmail($request->jobid, $jobSearch->job_email, $data);
        }
        return  OtherEmail::where('jobId', $request->jobid)->orderBy('is_primary', 'DESC')->orderBy('id', 'ASC')->get();
    }

    protected function findAndUpdateEmail($jobId, $email, $data, $id=0){
       if($id>0){
         $emailData=OtherEmail::find($id);
       }else{
        $emailData=OtherEmail::where([['jobId', $jobId], ['email', $email]])->first();
       }
       $emailData=empty($emailData) ? new OtherEmail : $emailData;
       $emailData->email=$email;
       $emailData->jobId=$jobId;
       $emailData->is_primary=$data['is_primary']==1?1:0;
       if(!empty($data['label'])){
         $emailData->label=$data['label'];
       }
       $emailData->save();
       return $emailData->id;
    }
}
