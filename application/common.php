<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
/**
 * 密码加密
 * @param string $str
 * @return bool|string
 */
function pwd_encode($str=''){
    $key_secret = config('database.auto_code');
    $pwd_encode = password_hash('###'.$key_secret.$str,PASSWORD_DEFAULT);
    return $pwd_encode;
}

/**
 * 密码验证
 * @param string $verifyStr
 * @param $passwordHash
 * @return bool
 */
function pwd_verify($verifyStr='',$passwordHash){
    $key_secret = config('database.auto_code');
    if (password_verify('###'.$key_secret.$verifyStr,$passwordHash)) {
        return true;
    } else {
        return false;
    }
}
