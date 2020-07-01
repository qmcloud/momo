<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Http\Resources\DefaultAddr;

class Address extends Model
{
    const ISDEFAULT_YES = 1;
    const ISDEFAULT_NO = 0;
    const ISDEFAULT_YES_STRING = '是';
    const ISDEFAULT_NO_STRING = '否';

    const STATE_NULL = 0;
    const STATE_INVALID = 1;
    const STATE_VALID = 2;
    const STATE_NULL_STRING = 'null';
    const STATE_INVALID_STRING = '无效';
    const STATE_VALID_STRING = '有效';
    //
    protected $table = 'addresses';

    public static function getIsDefaultDisplayConfig()
    {
        return [
            'on' => [
                'value' => self::ISDEFAULT_YES,
                'text' => self::ISDEFAULT_YES_STRING,
            ],
            'off' => [
                'value' => self::ISDEFAULT_NO,
                'text' => self::ISDEFAULT_NO_STRING,
            ],
        ];
    }

    public static function getIsDefaultDisplayMap()
    {
        return [
            self::ISDEFAULT_YES => self::ISDEFAULT_YES_STRING,
            self::ISDEFAULT_NO => self::ISDEFAULT_NO_STRING,
        ];
    }

    public static function getStateDisplayMap()
    {
        return [
            self::STATE_NULL => self::STATE_NULL_STRING,
            self::STATE_INVALID => self::STATE_INVALID_STRING,
            self::STATE_VALID => self::STATE_VALID_STRING,
        ];
    }

    /**
     * 关联学校
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function get_school(){
        return $this->hasOne('App\Models\School','id','sid');
    }

    /**
     * 关联宿舍
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function get_dorm(){
        return $this->hasOne('App\Models\Dorm','id','did');
    }

    /**
 * 获取默认收获地址
 * @return DefaultAddr
 */
    public static function getAddr($where){
        return new DefaultAddr(Address::where($where)->first());
    }

    /**
     * 获取地区列表
     * @return DefaultAddr
     */
    public static function getAddrList($where){
        return  DefaultAddr::collection(Address::where($where)->get());
    }

}
