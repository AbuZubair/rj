<?php
namespace App\Library\Services;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
  
class Api
{

    public function get($link)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $link);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json; charset=utf-8',
        ));
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);

        Log::info("Try to call API ".$link);
        $result = curl_exec($ch);
        Log::info("Result ".$result);
        if ($result === FALSE) {

          Log::error(curl_error($ch));

        }

        $curl_errno = curl_errno($ch);
        $curl_error = curl_error($ch);	
        $http_code  = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($curl_errno > 0) {
            $senddatax = array(
            'sending_respon'=>array(
                'globalstatus' => 90, 
                'globalstatustext' => $curl_errno."|".$http_code)
            );
            Log::error(json_encode($senddatax));
        } else {
            if ($http_code<>"200") {
                $senddatax = array(
                'sending_respon'=>array(
                    'globalstatus' => 90, 
                    'globalstatustext' => $curl_errno."|".$http_code)
                );
                Log::error(json_encode($senddatax));	
            }
        }		        

        return $result;
    }
    
    public function getMedia($link,$type)
    {
 
        $filtered = false;
        $result = $this->get($link);

        $arrresult = json_decode($result);
        // $strhashtag = array('#cerdasbersamasamsung','#samsungbisnis');
        if(!empty($arrresult->data)){
            $arr = array();
            foreach ($arrresult->data as $value) {
                if($value->media_type == $type){
                    // if (in_array($value->caption, $strhashtag)) {
                        $arr[] = $value;
                    // }
                }
            }
            $filtered['data'] = $arr;
            if(isset($arrresult->paging))$filtered['next'] = $arrresult->paging->next;
        }
        
        return $filtered;
    }

}