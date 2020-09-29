<?php
function getSubstr($str, $leftStr, $rightStr)
{
    if ($leftStr == '') {
        $llen = strlen($leftStr); //获取左边文本的长度
        $left = 0; //寻找左边文本在$str第一次出现的位置
        $right = strpos($str, $rightStr, $left + $llen); //寻找右边文本在$str第一次出现的位置，并且从左边文本后开始搜索
    } elseif ($rightStr == '') {
        $llen = strlen($leftStr); //获取左边文本的长度
        $left = strpos($str, $leftStr); //寻找左边文本在$str第一次出现的位置
        $right = strlen($str) - $llen + 1; //寻找右边文本在$str第一次出现的位置，并且从左边文本后开始搜索
    } else {
        $llen = strlen($leftStr); //获取左边文本的长度
        $left = strpos($str, $leftStr); //寻找左边文本在$str第一次出现的位置
        $right = strpos($str, $rightStr, $left + $llen); //寻找右边文本在$str第一次出现的位置，并且从左边文本后开始搜索
    }

    //判断左边不小于0 或者 右边是否大于左边
    if ($left < 0 || $right < $left) {
        return "";
    } else {
        return substr($str, $left + $llen, $right - $left - $llen);
    }
}

/**
 * 数字转excel字母
 */
function IntToChr($index, $start = 65)
{
    $str = '';
    if (floor($index / 26) > 0) {
        $str .= IntToChr(floor($index / 26) - 1);
    }
    return $str . chr($index % 26 + $start);
}

/**
 * 读取文件内容
 */
function red_file($path) {
    //文件目录
    $file_path = $path;
    //判断文件是否存在
    if (file_exists($file_path)) {
        //只读方式打开，将文件指针指向文件头。
        $file = fopen($file_path, "r");
        //初始化str
        $str = "";
        //循环读取，直至读取完整个文件
        while (!feof($file)) {
            $str .= fgets($file);
        }
        //返回文件内容
        return $str;
        //关闭文件
        fclose($file);
    }
}

function SetName($path, $ext, $name = "") {
    if ($name == "") {
        $time = time();
        $file_name = "$time.$ext";
        if (file_exists($path . $file_name)) {
            $time++;
            return SetName($path, $ext, $time);
        } else {
            return $file_name;
        }
    } else {
        $time = $name;
        $file_name = "$time.$ext";
        if (file_exists($path . $file_name)) {
            $time++;
            return SetName($path, $ext, $time);
        } else {
            return $file_name;
        }
    }
}

function time_range($start,$end) {
    $dt_start = strtotime($start);
    $dt_end = strtotime($end);
    while ($dt_start<=$dt_end){
        $arr[] = date('Y-m-d',$dt_start);
        $dt_start = strtotime('+1 day',$dt_start);
    }
    return $arr;
}