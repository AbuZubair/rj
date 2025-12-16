<?php
namespace App\Library\Services;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Logs;
use App\Anggota;
use PDF;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
  
class Shared
{
    
    public function logs($msg)
    {
        try{
            $data = new Logs;

            $data->created_by = Auth::user() != null?Auth::user()->getUsername():'system';
            $data->message =$msg;            
            $data->save();
            Log::info((Auth::user() != null?Auth::user()->getUsername():'system').' save log succesfully : '.json_encode($data));
        }
        catch (Exception $e){
            Log::error($e->getMessage());
        }
    }

    public function getParamQuery($param, $param1 = null, $param2 = null){
        $query = DB::table('parameter')
            ->where('param',$param)
            ->where('is_active','Y')
            ->orderBy('label', 'asc');
        if($param1 != null){
            $query->where('param1', $param1);
        }
        if($param2 != null){
            $query->where('param2', $param2);
        }
        return $query;
    }

    public function getParamDropdown($param)
    {
        $data = $this->getParamQuery($param)->get()->toArray();
        return $data;
    }

    public function getTahunAjaran()
    {
        $data = $this->getParamQuery('th_ajaran')->first();
        return $data->value;
    }

    static public function getTahunAjaranStatic()
    {
        $self = new Shared();
        $data = $self->getParamQuery('th_ajaran')->first();
        return $data ? $data->value : null;
    }

    static public function getLastAngkatan($tingkat)
    {
        $self = new Shared();
        $data = $self->getParamQuery('angkatan_terakhir', $tingkat)->first();
        return $data->value;
    }

    public function getMonthName($month)
    {
        $monthName = array(
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember'
        );
        return $monthName[$month];
    }

    public function getMonthIndex($month)
    {
        $months = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        return array_search($month, $months);
    }

    public function sendEmail($email,$datas,$type)
    {
        $data = array('data'=>$datas,'type' => $type);
        $subject = "";        
        switch ($type) {
            case 'pengajuan':
                $html = 'mail_pengajuan_notif';
                $subject = '[PENGAJUAN] KOPKAR KPMS';
                break;
            default: 
                $html = '';
                break;
        }
        try{
            Mail::send($html, $data, function($message) use ($email, $subject){
                $message->subject($subject);
                $message->to($email);             
             });

            Log::info('Succesfully send email to: '.json_encode($email));
            return array('status' => true, 'message' => 'Berhasil kirim email');
        }
        catch (Exception $e){
            Log::error($e->getMessage());
            return response (['status' => false,'errors' => $e->getMessage()]);
        }
    }

}