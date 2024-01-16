<?php

namespace App\Http\Controllers\Admin;

use App\Models\JobSearch;
use Illuminate\Http\Request;
use App\Models\OtherAddrress;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class OtherAddrressController extends Controller
{
    /**
     * replace.
     */
    public function replace(Request $request)
    {
        $pre_address = OtherAddrress::selectRaw('GROUP_CONCAT(CONCAT(if(address!="",address,""),"/",if(aunit!="",aunit,""),"(", if(other_addrresses.rank!="",other_addrresses.rank,""),")","(", if(prime!=0,prime,"0"),")","(", if(label!="",label,""),")")  ORDER BY id SEPARATOR "::") as combinedaddress')-> where('jobId', $request->jobid)->groupBy("jobId")->first();
        //OtherAddrress::where('jobId', $request->jobid)->delete();
        JobSearch::where('id', $request->jobid)->update(['last_edited' => Auth::user()->id]);
        $cur_address = [];
        $ids=[];
        foreach ($request->address as $key => $value) {
            if(!empty($value['num'])) {
                //OtherAddrress::create(['jobId' => $request->jobid, 'address' => $value['num'], 'aunit' => $value['un'], 'rank' => $value['rn'], 'prime' => ($request->prime == $key ? 1 : 0), 'label' => $value['lb']]);
                if($value['pr']==true){
                    OtherAddrress::where('jobId', $request->jobid)->update(['prime' => 0]);
                    JobSearch::where('id', $request->jobid)->update(['job_addr' => $value['num']]);
                    $value['primary']=1;
                }
                $ids[]=$this->findAndUpdate($request->jobid, $value['num'], ['is_primary'=>$value['pr'], 'label'=>$value['lb'], 'rank' => $value['rn'], 'aunit' => $value['un']], $value['id']);
                $cur_phone[] = $value['num'] . "(" . $value['lb'] . ")";
                $cur_address[] = $value['num'] . "/" . $value['un'] . "(" . $value['rn'] . ")" . "(" . $value['pr'] . ")" . "(" . $value['lb'] . ")";
            }
        }
        OtherAddrress::where('jobId', $request->jobid)->whereNotIn('id', $ids)->delete();
        $lcn=JobSearch::updateLCN($request->jobid);
        $cur_address_str = implode('::', $cur_address);
        $pre_address_str = ($pre_address == null && empty($pre_address->combinedaddress)) ? '' : $pre_address->combinedaddress;
        if($pre_address_str != $cur_address_str) {
            \ActivityLog::add(['action' => 'updated', 'module' => 'jobsearch','data_key' => 'other-addresses', 'data_id' => $request->jobid, 'data_ref' => $request->cellref, 'data_val' => $pre_address_str]);
        }
    }

    public function otherAddress(Request $request)
    {
        $jobSearch=JobSearch::where('id', $request->jobid)->first(['job_addr']);
        if(!empty($jobSearch)){
            $data=['is_primary'=>1];
            $this->findAndUpdate($request->jobid, $jobSearch->job_addr, $data);
        }
        return  OtherAddrress::where('jobId', $request->jobid)->orderBy('prime', 'DESC')->orderBy('id', 'ASC')->get();
    }

    protected function findAndUpdate($jobId, $address, $data, $id=0){
        if($id>0){
          $addressData=OtherAddrress::find($id);
        }else{
         $addressData=OtherAddrress::where([['jobId', $jobId], ['address', $address]])->first();
        }
        $addressData=empty($addressData) ? new OtherAddrress : $addressData;
        $addressData->address=$address;
        $addressData->jobId=$jobId;
        $addressData->prime=$data['is_primary']==1?1:0;
        if(!empty($data['label'])){
          $addressData->label=$data['label'];
        }
        if(!empty($data['aunit'])){
            $addressData->aunit=$data['aunit'];
        }
        if(!empty($data['rank'])){
            $addressData->rank=$data['rank'];
        }
        $addressData->save();
        return $addressData->id;
    }


}
