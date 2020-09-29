<?php
/**
 * 本API错误代码汇总
 * 100 - 解析成功
 * 101 - 未查询到数据！
 * 102 - 数据超限，请使用模板填写！
 * 103 - 账号重复！
 * 104 - 禁止空数据
 * 修改日期：2020/8/14
 */
include __DIR__ . '/Excel/index.php';
function format_excel($json)
{
    $Json = json_decode($json, true);
    $col_arr = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L");
    $sql_str = "INSERT INTO `user`(`user`, `name`, `sex`, `team`, `zy`, `sfzh`, `dh`, `dorm`, `qw`, `jtzz`, `tel1`, `tel2`) VALUES ";
    $max = count($Json);

    if ($max < 2) {
        $return = array(
            "code" => 101,
            "message" => "未查询到数据！",
        );
        return json_encode($return, JSON_UNESCAPED_UNICODE);
    } else {
        $Pool = [];
        foreach ($Json as $row_num => $rows) {
            if ($row_num >= 2) {
                $temp_type = 1;
                $str = '(';
                foreach ($rows as $col_num => $cols) {
                    if (in_array($col_num, $col_arr)) {

                        if (($Json[$row_num]['A'] == '')
                            || ($Json[$row_num]['B'] == '')
                            || ($Json[$row_num]['C'] == '')
                            || ($Json[$row_num]['D'] == '')
                            || ($Json[$row_num]['E'] == '')
                            || ($Json[$row_num]['F'] == '')
                            || ($Json[$row_num]['G'] == '')
                            || ($Json[$row_num]['H'] == '')
                            || ($Json[$row_num]['I'] == '')
                            || ($Json[$row_num]['J'] == '')
                            || ($Json[$row_num]['K'] == '')
                            || ($Json[$row_num]['L'] == '')
                        ) {
                            if (($Json[$row_num]['A'] == '')
                                && ($Json[$row_num]['B'] == '')
                                && ($Json[$row_num]['C'] == '')
                                && ($Json[$row_num]['D'] == '')
                                && ($Json[$row_num]['E'] == '')
                                && ($Json[$row_num]['F'] == '')
                                && ($Json[$row_num]['G'] == '')
                                && ($Json[$row_num]['H'] == '')
                                && ($Json[$row_num]['I'] == '')
                                && ($Json[$row_num]['J'] == '')
                                && ($Json[$row_num]['K'] == '')
                                && ($Json[$row_num]['L'] == '')
                            ) {
                                $temp_type = 0;
                            } else {
                                $return = array(
                                    "code" => 104,
                                    "message" => "禁止空数据",
                                );
                                return json_encode($return, JSON_UNESCAPED_UNICODE);
                            }
                        } else {
                            if ($col_num == 'A') {
                                if (in_array($cols, $Pool)) {
                                    $return = array(
                                        "code" => 103,
                                        "message" => "账号重复！",
                                    );
                                    return json_encode($return, JSON_UNESCAPED_UNICODE);
                                } else {
                                    array_push($Pool, $cols);
                                }
                            }

                            if ($col_num == 'L') {
                                $str .= "'$cols'";
                            } else {
                                $str .= "'$cols', ";
                            }

                        }

                    } else {
                        $return = array(
                            "code" => 102,
                            "message" => "数据超限，请使用模板填写！",
                        );
                        return json_encode($return, JSON_UNESCAPED_UNICODE);
                    }
                }

                if ($temp_type == 1) {
                    if ($row_num >= $max) {
                        $str .= ")";
                    } else {
                        $str .= "), ";
                    }
                    $sql_str .= $str;
                } else {
                    $max--;
                }
            }
        }

        $return = array(
            "code" => 100,
            "message" => "表格数据解析通过！",
            "result" => $sql_str,

        );
        return json_encode($return, JSON_UNESCAPED_UNICODE);
    }
}
