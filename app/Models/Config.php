<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
class Config extends Model
{
    protected $table = 'options';

    protected function get($name){
        if(!empty($name)){

            $point=strstr($name, ".");
            if($point){
                $op=explode('.',$name);
                $data=Config::where('option_name', $op[0])->get()->toArray();
                $value=json_decode($data[0]['option_value'],true);
                return $value[$op[1]];
            }else{
                $data=Config::where('option_name', $name)->get()->toArray();
                $value=json_decode($data[0]['option_value'],true);
                return $value;
            }

        }else{
            return [];
        }
    }


}
