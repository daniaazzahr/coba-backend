<?php

namespace App\Helper;

use App\Models\BmTask;
use Carbon\Carbon;
use Exception;

class Tool{

    // public static function generatefilter($arguments){
    //     $strfilter="";
    //     foreach($arguments as $props=>$val){
    //         if($props!="total"&&$props!="page"&&$val!=""){
    //             $strfilter.=",@".$props."='".$val."'";
    //         }
    //     }
    //     $strfilter.=",@offset=".(($arguments["limit"]*$arguments["page"])-$arguments["limit"]);
    //  //   throw new Exception($strfilter);
    //     return $strfilter;
    // }

    public static function response($issuccess,$err,$data,$responsecode){
        $msg="";
        $errcode=$responsecode;
        if($responsecode!=200){
            if($responsecode==428){
                $msg=$err;
            }else{
                if(property_exists($err,'validator')){
                    $msg=$err->validator->errors();
                }else{
                    if(method_exists($err,'getMessage')){
                        $msg=$err->getMessage();
                    }else{
                        $msg=$err;
                    }
                }
                $errcode=property_exists($err,'validator')?427:$responsecode;
            }

        }else{
            $msg=$err;
        }
        return response()->json([
            "success"=>$issuccess,
            "messages"=>$msg,
            "data"=>$data
        ],$errcode);
    }
}
?>
