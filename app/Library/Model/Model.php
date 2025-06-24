<?php
namespace App\Library\Model;

use App\Approval;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Library\Services\Shared;
use App\User;
use App\Project;
use App\Trans;

use Carbon\Carbon;

class Model
{
    public function createApproval($data)
    {
        $param = array(
            'entryDate' => $data['entryDate'],
            'requestor' => $data['requestor'],
            'projectID' => $data['projectID'],
            'approvalType' => $data['approvalType'],
            'created_by' => Auth::user()->getUsername()
        );
        if(isset($data['amentBudget']))$param['amentBudget'] = $data['amentBudget'];
        if(isset($data['transID']))$param['transID'] = $data['transID'];        
        $id = DB::table('approval')
            ->insertGetId($param);                        
        Log::info(' Approval created : '.json_encode($param)); 
        $param['rowID'] = $id;
        $this->sendApprovalEmail($param,'approval');
    }

    public function checkApproval($id)
    {
        $data = DB::table('approval')->where('status','0')->where('projectID',$id)->get()->first();
        if(isset($data)){
            return false;
        }else{
            return true;
        }
    }
    
    public function sendApprovalEmail($data,$type)
    {
        $approval = Approval:: leftJoin('user as a', 'a.rowID', '=', 'approval.requestor')
            ->leftJoin('user as b', 'b.rowID', '=', 'approval.granter')
            ->select('approval.*',DB::raw('CONCAT(a.firstName, " ",a.lastName) AS requestorName'),DB::raw('CONCAT(b.firstName, " ",b.lastName) AS granterName'))
            ->where('approval.rowID',$data['rowID'])->first()->toArray();
        if($type == 'approval'){
            $receiver = User::where('role',1)->select('email')->get()->toArray();
        }
    
        if(in_array($type, ['approved','rejected'])){
            $receiver = User::where('rowID',$approval['requestor'])->select('email')->get()->toArray();
        }

        if($type == 'finance'){
            $receiver = User::where('role',4)->select('email')->get()->toArray();
        }

        $receiver = array_map(function ($v) {
            if(isset($v['email']))return $v['email'];
        },$receiver);  
        $project = Project::where('rowID',$approval['projectID'])->first()->toArray();
        $approval['project'] = $project;
        if(isset($approval['transID'])){
            $trans = Trans::
            leftJoin('akun', 'akun.idAkun', '=', 'transaction.transAkunID')
            ->leftJoin('client', 'client.rowID', '=', 'transaction.transClientID')            
            ->where('transaction.rowID',$approval['transID'])
            ->first()->toArray();
            $approval['trans'] = $trans;            
        }
        $shared = new Shared;
        $shared->sendEmail($receiver,$approval,$type);

    }

}