<?php
include __DIR__ . '/functions.php';
header("content-Type: text/html; charset=utf-8");
date_default_timezone_set("PRC");

class NEInfo
{
    private static $type = "";

    function __construct($type = "") {
        self::$type = $type;
        $this->Start();
    }

    function Start() {
        session_name('Session');
        session_start();
        switch (self::$type) {
            case "register":
                $this->Register();
                break;
            case "query":
                $this->query();
                break;
            case "page":
                $this->page();
                break;
            case "oper":
                $this->oper();
                break;
            case "qxian":
                $this->qxian();
                break;
            // case "demo":
            //     $this->demo();
            //     break;
            default:
                echo "请不要再对本站进行攻击<br>你的一举一动我都知道";
                break;
        }
    }
    
    function demo() {
        echo '123';
    }

    /**
     * 导入
     */
    function Register() {
        if ($_SESSION["Extent"] == 'admin'){
            $temp_file = explode(".", $_FILES["file"]["name"]);
            $extension = end($temp_file);
            $extension = strtolower($extension);
            $extension = trim($extension);
    
            $file_name = SetName(PATH . '/temp/', $extension);
            if ($extension == "xlsx") {
                if ($_FILES["file"]["error"] > 0) {
                    $return = array(
                        "code" => 104,
                        "message" => "错误",
                        "result" => $_FILES["file"]["error"]
                    );
                    die(json_encode($return, JSON_UNESCAPED_UNICODE));
                } else {
                    if (move_uploaded_file($_FILES["file"]["tmp_name"], PATH . '/temp/' . $file_name)) {
                        $excel = format_excel(excel_read($file_name));
                        $arr = json_decode($excel, true);
                        unlink(PATH . '/temp/' . $file_name);
    
                        switch ($arr['code']) {
                            case '100':
                                $sqlstr = $arr['result'];
                                $mysql = new DB();
                                if ($mysql->query($sqlstr)) {
                                    $return = array(
                                        "code" => 100,
                                        "message" => "导入成功"
                                    );
                                    die(json_encode($return, JSON_UNESCAPED_UNICODE));
                                } else {
                                    $return = array(
                                        "code" => 103,
                                        "message" => "导入失败"
                                    );
                                    die(json_encode($return, JSON_UNESCAPED_UNICODE));
                                }
                                break;
                            default:
                                $return = array(
                                    "code" => $arr['code'],
                                    "message" => $arr['message'],
                                );
                                die(json_encode($return, JSON_UNESCAPED_UNICODE));
                                break;
                        }
                    } else {
                        $return = array(
                            "code" => 102,
                            "message" => '文件移动失败，请联系管理员',
                        );
                        die(json_encode($return, JSON_UNESCAPED_UNICODE));
                    }
                }
            } else {
                $return = array(
                    'code' => 101,
                    'message' => '非法的文件格式',
                );
                die(json_encode($return, JSON_UNESCAPED_UNICODE));
            }
        }
    }


    /**
     * 获取信息
     * 100 - 登陆成功
     * 101 - 登陆失败
     */
    function query() {
        if ($_SESSION["Extent"] == 'admin' || $_SESSION["Extent"] == 'putong'){
            if (isset($_GET['query_type'])) {
                $mysql = new DB();
                // 获取查询类型
                $query_type = $_GET['query_type'];
    
                /**
                 * 查询队伍信息
                 * 100 - 获取成功
                 * 101 - 缺少参数
                 * 102 - 无队伍信息
                 */
                if($query_type == 'team') {
                    $sql_code = "SELECT DISTINCT `team` FROM `user` ORDER BY `team`";
                    $sql = $mysql->query($sql_code);
                    $num = $sql->num_rows;
                    if ($num > 0) {
                        while ($row = $sql->fetch_assoc()) {
                            $Json[] = $row['team'];
                        }
                        $return = array(
                            "code" => 100,
                            "message" => "OK",
                            "result" => $Json
                        );
                        die(json_encode($return, JSON_UNESCAPED_UNICODE));
                    }else{
                        $return = array(
                            "code" => 102,
                            "message" => "无队伍信息"
                        );
                        die(json_encode($return, JSON_UNESCAPED_UNICODE));
                    }
                }
    
                /**
                 * 查询宿舍信息
                 * 100 - 获取成功
                 * 101 - 缺少参数
                 * 102 - 无队伍信息
                 */
                if($query_type == 'dorm') {
                    $dorm_type = $_GET['dorm_type'];
                    
                    /**
                     * 查询楼号
                     */
                    if($dorm_type == 'L') {
                        $sql_code = "SELECT DISTINCT substring_index(`dorm`,'#', 1) AS `Lou` FROM `user` ORDER BY `Lou`";
                        $sql = $mysql->query($sql_code);
                        $num = $sql->num_rows;
                        if ($num > 0) {
                            while ($row = $sql->fetch_assoc()) {
                                $Json[] = $row['Lou'];
                            }
                            $return = array(
                                "code" => 100,
                                "message" => "OK",
                                "result" => $Json
                            );
                            die(json_encode($return, JSON_UNESCAPED_UNICODE));
                        }else{
                            $return = array(
                                "code" => 102,
                                "message" => "无信息"
                            );
                            die(json_encode($return, JSON_UNESCAPED_UNICODE));
                        }
                    }
    
                    /**
                     * 查询宿舍号
                     */
                    if($dorm_type == 'S') {
                        $dorm_L = $_GET['dorm_L'];
                        
                        $sql_code = "SELECT DISTINCT substring_index(`dorm`,'#', -1) AS `Su` FROM `user` WHERE `dorm` LIKE '$dorm_L#%' ORDER BY `Su`";
                        $sql = $mysql->query($sql_code);
                        $num = $sql->num_rows;
                        if ($num > 0) {
                            while ($row = $sql->fetch_assoc()) {
                                $Json[] = $row['Su'];
                            }
                            $return = array(
                                "code" => 100,
                                "message" => "OK",
                                "result" => $Json
                            );
                            die(json_encode($return, JSON_UNESCAPED_UNICODE));
                        }else{
                            $return = array(
                                "code" => 102,
                                "message" => "无信息"
                            );
                            die(json_encode($return, JSON_UNESCAPED_UNICODE));
                        }
                    }
    
                    /**
                     * 查询宿舍人
                     */
                    if($dorm_type == 'R') {
                        $dorm_L = $_GET['dorm_L'];
                        $dorm_S = $_GET['dorm_S'];
    
                        $sql_code = "SELECT `user`,`name` FROM `user` WHERE `dorm` = '$dorm_L#$dorm_S' ORDER BY `name`";
                        $sql = $mysql->query($sql_code);
                        $num = $sql->num_rows;
                        if ($num > 0) {
                            while ($row = $sql->fetch_assoc()) {
                                $Json[] = array(
                                    "user" => $row['user'],
                                    "name" => $row['name']
                                );
                            }
                            $return = array(
                                "code" => 100,
                                "message" => "OK",
                                "result" => $Json
                            );
                            die(json_encode($return, JSON_UNESCAPED_UNICODE));
                        }else{
                            $return = array(
                                "code" => 102,
                                "message" => "无信息"
                            );
                            die(json_encode($return, JSON_UNESCAPED_UNICODE));
                        }
                    }
                    
                }
    
                /**
                 * 获取队伍成员
                 * 100 - 获取成功
                 * 102 - 无成员信息
                 */
                if($query_type == 'people') {
                    $mysql1 = new DB();
                    $people_team = $_GET['people_team'];
                    if(isset($_GET['people_user']) && $_GET['people_user'] != ''){
                        $people_user = $_GET['people_user'];
                        if(is_numeric($people_user)){
                            $sql_code = "SELECT * FROM `user` WHERE `team` = '$people_team' AND `user` = '$people_user' ORDER BY `user`";
                            $sql = $mysql->query($sql_code);
                            $num = $sql->num_rows;
                            if ($num > 0) {
                                while ($row = $sql->fetch_assoc()) {
                                    $yesterday = date('Y-m-d',strtotime('-1 day'));
                                    $sql_code = "SELECT * FROM `kaohe` WHERE `z` > 0  AND `riqi` = '$yesterday' AND `user` = '" . $row['user'] . "' OR `z` IS NULL AND `riqi` = '$yesterday' AND `user` = '" . $row['user'] . "' OR `s` > 0  AND `riqi` = '$yesterday' AND `user` = '" . $row['user'] . "' OR `s` IS NULL AND `riqi` = '$yesterday' AND `user` = '" . $row['user'] . "' OR `x` > 0  AND `riqi` = '$yesterday' AND `user` = '" . $row['user'] . "' OR `x` IS NULL AND `riqi` = '$yesterday' AND `user` = '" . $row['user'] . "' OR `w` > 0  AND `riqi` = '$yesterday' AND `user` = '" . $row['user'] . "' OR `w` IS NULL AND `riqi` = '$yesterday' AND `user` = '" . $row['user'] . "'";
                                    $sql1 = $mysql1->query($sql_code);
                                    $num1 = $sql1->num_rows;
                                    $Json[] = array(
                                        'xh' => $row['user'],
                                        'xm' => $row['name'],
                                        'ztbx' => $num1
                                    );
                                }
                                $return = array(
                                    "code" => 100,
                                    "message" => "OK",
                                    "result" => $Json
                                );
                                die(json_encode($return, JSON_UNESCAPED_UNICODE));
                            }else{
                                $return = array(
                                    "code" => 102,
                                    "message" => "无成员信息"
                                );
                                die(json_encode($return, JSON_UNESCAPED_UNICODE));
                            }
                        }else{
                            $sql_code = "SELECT * FROM `user` WHERE `team` = '$people_team' AND `name` LIKE '%$people_user%' ORDER BY `user`";
                            $sql = $mysql->query($sql_code);
                            $num = $sql->num_rows;
                            if ($num > 0) {
                                while ($row = $sql->fetch_assoc()) {
                                    $yesterday = date('Y-m-d',strtotime('-1 day'));
                                    $sql_code = "SELECT * FROM `kaohe` WHERE `z` > 0  AND `riqi` = '$yesterday' AND `user` = '" . $row['user'] . "' OR `z` IS NULL AND `riqi` = '$yesterday' AND `user` = '" . $row['user'] . "' OR `s` > 0  AND `riqi` = '$yesterday' AND `user` = '" . $row['user'] . "' OR `s` IS NULL AND `riqi` = '$yesterday' AND `user` = '" . $row['user'] . "' OR `x` > 0  AND `riqi` = '$yesterday' AND `user` = '" . $row['user'] . "' OR `x` IS NULL AND `riqi` = '$yesterday' AND `user` = '" . $row['user'] . "' OR `w` > 0  AND `riqi` = '$yesterday' AND `user` = '" . $row['user'] . "' OR `w` IS NULL AND `riqi` = '$yesterday' AND `user` = '" . $row['user'] . "'";
                                    $sql1 = $mysql1->query($sql_code);
                                    $num1 = $sql1->num_rows;
                                    $Json[] = array(
                                        'xh' => $row['user'],
                                        'xm' => $row['name'],
                                        'ztbx' => $num1
                                    );
                                }
                                $return = array(
                                    "code" => 100,
                                    "message" => "OK",
                                    "result" => $Json
                                );
                                die(json_encode($return, JSON_UNESCAPED_UNICODE));
                            }else{
                                $return = array(
                                    "code" => 102,
                                    "message" => "无成员信息"
                                );
                                die(json_encode($return, JSON_UNESCAPED_UNICODE));
                            }
                        }
                    }else{
                        $sql_code = "SELECT * FROM `user` WHERE `team` = '$people_team' ORDER BY `user`";
                        $sql = $mysql->query($sql_code);
                        $num = $sql->num_rows;
                        if ($num > 0) {
                            while ($row = $sql->fetch_assoc()) {
                                $yesterday = date('Y-m-d',strtotime('-1 day'));
                                $sql_code = "SELECT * FROM `kaohe` WHERE `z` > 0  AND `riqi` = '$yesterday' AND `user` = '" . $row['user'] . "' OR `z` IS NULL AND `riqi` = '$yesterday' AND `user` = '" . $row['user'] . "' OR `s` > 0  AND `riqi` = '$yesterday' AND `user` = '" . $row['user'] . "' OR `s` IS NULL AND `riqi` = '$yesterday' AND `user` = '" . $row['user'] . "' OR `x` > 0  AND `riqi` = '$yesterday' AND `user` = '" . $row['user'] . "' OR `x` IS NULL AND `riqi` = '$yesterday' AND `user` = '" . $row['user'] . "' OR `w` > 0  AND `riqi` = '$yesterday' AND `user` = '" . $row['user'] . "' OR `w` IS NULL AND `riqi` = '$yesterday' AND `user` = '" . $row['user'] . "'";
                                $sql1 = $mysql1->query($sql_code);
                                $num1 = $sql1->num_rows;
                                $Json[] = array(
                                    'xh' => $row['user'],
                                    'xm' => $row['name'],
                                    'ztbx' => $num1
                                );
                            }
                            $return = array(
                                "code" => 100,
                                "message" => "OK",
                                "result" => $Json
                            );
                            die(json_encode($return, JSON_UNESCAPED_UNICODE));
                        }else{
                            $return = array(
                                "code" => 102,
                                "message" => "无成员信息"
                            );
                            die(json_encode($return, JSON_UNESCAPED_UNICODE));
                        }
                    }
                }
    
                /**
                 * 考勤记录
                 * 100 - 查询成功
                 * 102 - 无异常信息
                 */
                if($query_type == 'info') {
                    $info_user = $_GET['info_user'];
                    $sql_code = "SELECT * FROM `kaohe` WHERE `user` = '$info_user' AND `z` > 0 AND `user` = '$info_user' OR `z` IS NULL AND `user` = '$info_user' OR `s` > 0 AND `user` = '$info_user' OR `s` IS NULL AND `user` = '$info_user' OR `x` > 0 AND `user` = '$info_user' OR `x` IS NULL AND `user` = '$info_user' OR `w` > 0 AND `user` = '$info_user' OR `w` IS NULL AND `user` = '$info_user' ORDER BY `riqi` DESC";
                    
                    $sql = $mysql->query($sql_code);
                    $num = $sql->num_rows;
                    if ($num > 0) {
                        while ($row = $sql->fetch_assoc()) {
                            $qk = '无';
                            if(isset($row['qk']) && $row['qk'] != ''){
                                $qk = $row['qk'];
                            }
                            $Json[] = array(
                                'rq' => $row['riqi'],
                                'z' => $this->num_str($row['z']),
                                's' => $this->num_str($row['s']),
                                'x' => $this->num_str($row['x']),
                                'w' => $this->num_str($row['w']),
                                'qk' => $qk,
                                'czr' => $row['dmczr']
                            );
                        }
                        $return = array(
                            "code" => 100,
                            "message" => "OK",
                            "result" => $Json
                        );
                        die(json_encode($return, JSON_UNESCAPED_UNICODE));
                    }else{
                        $return = array(
                            "code" => 102,
                            "message" => "无异常信息"
                        );
                        die(json_encode($return, JSON_UNESCAPED_UNICODE));
                    }
                }
    
                /**
                 * 个人查看信息
                 */
                if($query_type == 'xinxi') {
                    $xinxi_user = $_GET['xinxi_user'];
                    $sql_code = "SELECT * FROM `user` WHERE `user` = '$xinxi_user'";
                    $sql = $mysql->query($sql_code);
                    $num = $sql->num_rows;
                    if ($num > 0) {
                        $sql_code = "SELECT * FROM `kaohe` WHERE `user` = '$xinxi_user' ORDER BY `riqi`";
                        $sql = $mysql->query($sql_code);
                        $num = $sql->num_rows;
                        if ($num > 0) {
                            $i = 0;
                            $time = time_range('2020-09-22',date("Y-m-d",time()));
                            
                            while ($row = $sql->fetch_assoc()) {
                                
                                while ($row['riqi'] !== $time[$i]) {
                                    if($row['riqi'] !== $time[$i]){
                                        $Json[] = array(
                                            'rq' => $time[$i],
                                            'z' => '√',
                                            's' => '√',
                                            'x' => '√',
                                            'w' => '√',
                                            'qk' => '无'
                                        );
                                    }
                                    $i++;
                                }

                                $qk = '无';
                                if(isset($row['qk']) && $row['qk'] != ''){
                                    $qk = $row['qk'];
                                }
                                
                                $Json[] = array(
                                    'rq' => $row['riqi'],
                                    'z' => $this->num_str($row['z']),
                                    's' => $this->num_str($row['s']),
                                    'x' => $this->num_str($row['x']),
                                    'w' => $this->num_str($row['w']),
                                    'qk' => $qk
                                );
                                $i++;
                            }

                            while($i < count($time)){
                                $Json[] = array(
                                    'rq' => $time[$i],
                                    'z' => '√',
                                    's' => '√',
                                    'x' => '√',
                                    'w' => '√',
                                    'qk' => '无'
                                );
                                $i++;
                            }
    
                            $return = array(
                                "code" => 100,
                                "message" => "OK",
                                "result" => $Json
                            );
                            die(json_encode($return, JSON_UNESCAPED_UNICODE));
                        }else{
                            $i = 0;
                            $time = time_range('2020-09-22',date("Y-m-d",time()));
                            while ($i < count($time)) {
                                $Json[] = array(
                                    'rq' => $time[$i],
                                    'z' => '√',
                                    's' => '√',
                                    'x' => '√',
                                    'w' => '√',
                                    'qk' => '无'
                                );
                                ++$i;
                            }
                            $return = array(
                                "code" => 100,
                                "message" => "OK",
                                "result" => $Json
                            );
                            die(json_encode($return, JSON_UNESCAPED_UNICODE));
                        }
                    }else{
                        $return = array(
                            "code" => 102,
                            "message" => "无信息"
                        );
                        die(json_encode($return, JSON_UNESCAPED_UNICODE));
                    }
                }
                
                if($query_type == 'kqrq'){
                        $return = array(
                            "code" => 100,
                            "message" => "OK",
                            "result" => array_reverse(time_range('2020-09-22',date("Y-m-d",time())))
                        );
                        die(json_encode($return, JSON_UNESCAPED_UNICODE));
                    
                }

                if($query_type == 'kqsj'){
                    $kqsj_type = $_GET['kqsj_type'];
                    if($kqsj_type == 'r') {
                        $sql_code = "SELECT `z_start`,`z_end`,`s_start`,`s_end`,`x_start`,`x_end`,`w_start`,`w_end` FROM `set`";
                        $sql = $mysql->query($sql_code);
                        while ($row = $sql->fetch_assoc()) {
                            $Json = array(
                                'z_start' => $row['z_start'],
                                'z_end' => $row['z_end'],
                                's_start' => $row['s_start'],
                                's_end' => $row['s_end'],
                                'x_start' => $row['x_start'],
                                'x_end' => $row['x_end'],
                                'w_start' => $row['w_start'],
                                'w_end' => $row['w_end']
                            );
                        }
                        $return = array(
                            "code" => 100,
                            "message" => "OK",
                            "result" => $Json
                        );
                        die(json_encode($return, JSON_UNESCAPED_UNICODE));
                    }

                    if($kqsj_type == 'w') {
                        $z_s = $_POST['z_start'];
                        $z_e = $_POST['z_end'];
                        $s_s = $_POST['s_start'];
                        $s_e = $_POST['s_end'];
                        $x_s = $_POST['x_start'];
                        $x_e = $_POST['x_end'];
                        $w_s = $_POST['w_start'];
                        $w_e = $_POST['w_end'];
                        $sql_code = "UPDATE `set` SET `z_start` = '$z_s', `z_end` = '$z_e', `s_start` = '$s_s', `s_end` = '$s_e', `x_start` = '$x_s', `x_end` = '$x_e', `w_start` = '$w_s', `w_end` = '$w_e'";
                        if($mysql->query($sql_code)) {
                            $return = array(
                                "code" => 100,
                                "message" => "修改成功"
                            );
                            die(json_encode($return, JSON_UNESCAPED_UNICODE));
                        }else{
                            $return = array(
                                "code" => 101,
                                "message" => "修改失败"
                            );
                            die(json_encode($return, JSON_UNESCAPED_UNICODE));
                        }
                    }
                }
                /**
                 * 后台信息
                 */
                if($query_type == 'admin') {
                    $admin_type = $_GET['admin_type'];

                    $sql_code = "SELECT `z_start`,`z_end`,`s_start`,`s_end`,`x_start`,`x_end`,`w_start`,`w_end` FROM `set`";
                    $sql = $mysql->query($sql_code);
                    $num = $sql->num_rows;
                    if ($num <= 0) {
                        $return = array(
                            "code" => 102,
                            "message" => "考勤日期范围获取失败"
                        );
                        die(json_encode($return, JSON_UNESCAPED_UNICODE));
                    }
                    $row = $sql->fetch_assoc();
                    $now = strtotime(date("Y-m-d H:i:s"));
                    if(isset($_GET['riqi']) && $_GET['riqi'] != ''){
                        $now = $_GET['riqi'] . '23:59:59';
                        $today = $_GET['riqi'];
                        $z_start = $now . ' ' . $row['z_start'];
                        $z_end = $now . ' ' . $row['z_end'];
        
                        $s_start = $now . ' ' . $row['s_start'];
                        $s_end = $now . ' ' . $row['s_end'];
        
                        $x_start = $now . ' ' . $row['x_start'];
                        $x_end = $now . ' ' . $row['x_end'];
        
                        $w_start = $now . ' ' . $row['w_start'];
                        $w_end = $now . ' ' . $row['w_end'];
                    }else{
                        $now = strtotime(date("Y-m-d H:i:s"));
                        $z_start = date("Y-m-d") . ' ' . $row['z_start'];
                        $z_end = date("Y-m-d") . ' ' . $row['z_end'];
        
                        $s_start = date("Y-m-d") . ' ' . $row['s_start'];
                        $s_end = date("Y-m-d") . ' ' . $row['s_end'];
        
                        $x_start = date("Y-m-d") . ' ' . $row['x_start'];
                        $x_end = date("Y-m-d") . ' ' . $row['x_end'];
        
                        $w_start = date("Y-m-d") . ' ' . $row['w_start'];
                        $w_end = date("Y-m-d") . ' ' . $row['w_end'];
                    }
                    
                    

                    /**
                     * 全部汇总
                     */
                    if ($admin_type == 'all') {
                        if ($now > strtotime($z_start)) {
                            $Json[] = array(
                                "time" => '早上',
                                "yd" => count($this->yd()),
                                "wd" => count($this->wd('','z',$today)),
                                "zc" => count($this->zc('','z',$today)),
                                "cd" => count($this->cd('','z',$today)),
                                "sj" => count($this->sj('','z',$today)),
                                "bj" => count($this->bj('','z',$today))
                            );
                        }
    
                        if ($now > strtotime($s_start)) {
                            $Json[] = array(
                                "time" => '上午',
                                "yd" => count($this->yd()),
                                "wd" => count($this->wd('','s',$today)),
                                "zc" => count($this->zc('','s',$today)),
                                "cd" => count($this->cd('','s',$today)),
                                "sj" => count($this->sj('','s',$today)),
                                "bj" => count($this->bj('','s',$today))
                            );
                        }
    
                        if ($now > strtotime($x_start)) {
                            $Json[] = array(
                                "time" => '下午',
                                "yd" => count($this->yd()),
                                "wd" => count($this->wd('','x',$today)),
                                "zc" => count($this->zc('','x',$today)),
                                "cd" => count($this->cd('','x',$today)),
                                "sj" => count($this->sj('','x',$today)),
                                "bj" => count($this->bj('','x',$today))
                            );
                        }
    
                        if ($now > strtotime($w_start)) {
                            $Json[] = array(
                                "time" => '晚上',
                                "yd" => count($this->yd()),
                                "wd" => count($this->wd('','w',$today)),
                                "zc" => count($this->zc('','w',$today)),
                                "cd" => count($this->cd('','w',$today)),
                                "sj" => count($this->sj('','w',$today)),
                                "bj" => count($this->bj('','w',$today))
                            );
                        }
                        $return = array(
                            "code" => 100,
                            "message" => "OK",
                            "result" => $Json
                        );
                        die(json_encode($return, JSON_UNESCAPED_UNICODE));
                    }
    
                    /**
                     * 列表
                     */
                    if ($admin_type == 'list') {
                        $sql_code = "SELECT DISTINCT `team` FROM `user` ORDER BY `team`";
                        if ($now > strtotime($z_start)) {
                            $sql = $mysql->query($sql_code);
                            $num = $sql->num_rows;
                            if ($num > 0) {
                                while ($row = $sql->fetch_assoc()) {
                                    $team = $row['team'];
                                    $temp[] = array(
                                        "team" => $team,
                                        "yd" => count($this->yd($team,'z',$today)),
                                        "zc" => count($this->zc($team,'z',$today)),
                                        "wd" => count($this->wd($team,'z',$today)),
                                        "cd" => count($this->cd($team,'z',$today)),
                                        "sj" => count($this->sj($team,'z',$today)),
                                        "bj" => count($this->bj($team,'z',$today))
                                    );
                                }
    
                                $Json[] = array(
                                    "time" => '早上',
                                    "info" => $temp
                                );
                            }
                        }
    
                        $temp = array();
    
                        if ($now > strtotime($s_start)) {
                            $sql = $mysql->query($sql_code);
                            $num = $sql->num_rows;
                            if ($num > 0) {
                                while ($row = $sql->fetch_assoc()) {
                                    $team = $row['team'];
                                    $temp[] = array(
                                        "team" => $team,
                                        "yd" => count($this->yd($team,'s',$today)),
                                        "zc" => count($this->zc($team,'s',$today)),
                                        "wd" => count($this->wd($team,'s',$today)),
                                        "cd" => count($this->cd($team,'s',$today)),
                                        "sj" => count($this->sj($team,'s',$today)),
                                        "bj" => count($this->bj($team,'s',$today))
                                    );
                                }
                                
                                $Json[] = array(
                                    "time" => '上午',
                                    "info" => $temp
                                );
                            }
                        }
    
                        $temp = array();
    
                        if ($now > strtotime($x_start)) {
                            $sql = $mysql->query($sql_code);
                            $num = $sql->num_rows;
                            if ($num > 0) {
                                while ($row = $sql->fetch_assoc()) {
                                    $team = $row['team'];
                                    $temp[] = array(
                                        "team" => $team,
                                        "yd" => count($this->yd($team,'x',$today)),
                                        "zc" => count($this->zc($team,'x',$today)),
                                        "wd" => count($this->wd($team,'x',$today)),
                                        "cd" => count($this->cd($team,'x',$today)),
                                        "sj" => count($this->sj($team,'x',$today)),
                                        "bj" => count($this->bj($team,'x',$today))
                                    );
                                }
                                
                                $Json[] = array(
                                    "time" => '下午',
                                    "info" => $temp
                                );
                            }
                        }
                        
                        $temp = array();
    
                        if ($now > strtotime($w_start)) {
                            $sql = $mysql->query($sql_code);
                            $num = $sql->num_rows;
                            if ($num > 0) {
                                while ($row = $sql->fetch_assoc()) {
                                    $team = $row['team'];
                                    $temp[] = array(
                                        "team" => $team,
                                        "yd" => count($this->yd($team,'w',$today)),
                                        "zc" => count($this->zc($team,'w',$today)),
                                        "wd" => count($this->wd($team,'w',$today)),
                                        "cd" => count($this->cd($team,'w',$today)),
                                        "sj" => count($this->sj($team,'w',$today)),
                                        "bj" => count($this->bj($team,'w',$today))
                                    );
                                }
                                
                                $Json[] = array(
                                    "time" => '晚上',
                                    "info" => $temp
                                );
                            }
                        }
    
                        $return = array(
                            "code" => 100,
                            "message" => "OK",
                            "result" => $Json
                        );
                        die(json_encode($return, JSON_UNESCAPED_UNICODE));
                        
                    }
    
                    /**
                     * 查看宿舍情况
                     */
                    if ($admin_type == 'dorm') {
                        // $time = date("Y-m-d",time());
                        $time = $_GET['time'];
                        $sql_code = "select * from `kaohe` left outer join `user` on `kaohe`.`user`=`user`.`user` WHERE `kaohe`.`cqqk` IS NOT NULL AND `kaohe`.`riqi` = '$time' ORDER BY `team`";
                        $sql = $mysql->query($sql_code);
                        $num = $sql->num_rows;
                        if ($num > 0) {
                            while ($row = $sql->fetch_assoc()) {
                                $Json[] = array(
                                    $row['team'],
                                    $row['user'],
                                    $row['name'],
                                    $row['dorm'],
                                    $row['cqqk'],
                                    $row['cqczr']
                                );
                            }
                            $return = array(
                                "code" => 100,
                                "message" => "OK",
                                "result" => $Json
                            );
                            die(json_encode($return, JSON_UNESCAPED_UNICODE));
                        } else {
                            $return = array(
                                "code" => 102,
                                "message" => "无信息"
                            );
                            die(json_encode($return, JSON_UNESCAPED_UNICODE));
                        }
                    }
    
                    /**
                     * 查询状态的列表
                     */
                    if ($admin_type == 'zt_list') {
                        $zt_list_type = $_GET['zt_list_type'];
                        $zt_list_time = $_GET['zt_list_time'];
    
                        if (isset($_GET['zt_list_team']) && $_GET['zt_list_team'] != '') {
                            $zt_list_team = $_GET['zt_list_team'];

                            if ($zt_list_type == '0' || $zt_list_type == '1' || $zt_list_type == '2' || $zt_list_type == '3') {
                                // $today = date("Y-m-d",time());
        
                                $sql_code = "SELECT * FROM `user` WHERE `user` IN (SELECT `user` FROM `kaohe` WHERE `riqi`='$today' AND `$zt_list_time` = '$zt_list_type') AND team = '$zt_list_team'";
                                $sql = $mysql->query($sql_code);
                                $num = $sql->num_rows;
                                if ($num > 0) {
                                    while ($row = $sql->fetch_assoc()) {
                                        $Json[] = array(
                                            $row['user'],
                                            $row['name'],
                                            $row['dorm'],
                                            $row['dh'],
                                            $row['qw'],
                                            $row['tel1'],
                                            $row['tel2']
                                        );
                                    }
                                    $return = array(
                                        "code" => 100,
                                        "message" => "OK",
                                        "result" => $Json
                                    );
                                    die(json_encode($return, JSON_UNESCAPED_UNICODE));
                                }else{
                                    $return = array(
                                        "code" => 102,
                                        "message" => "无信息"
                                    );
                                    die(json_encode($return, JSON_UNESCAPED_UNICODE));
                                }
                            }else{
                                if ($zt_list_type == 'yd'){
                                    $sql_code = "SELECT * FROM `user` WHERE `team` = '$zt_list_team'";
                                    $sql = $mysql->query($sql_code);
                                    $num = $sql->num_rows;
                                    if ($num > 0) {
                                        while ($row = $sql->fetch_assoc()) {
                                            $Json[] = array(
                                                $row['user'],
                                                $row['name'],
                                                $row['dorm'],
                                                $row['dh'],
                                                $row['qw'],
                                                $row['tel1'],
                                                $row['tel2']
                                            );
                                        }
                                        $return = array(
                                            "code" => 100,
                                            "message" => "OK",
                                            "result" => $Json
                                        );
                                        die(json_encode($return, JSON_UNESCAPED_UNICODE));
                                    }else{
                                        $return = array(
                                            "code" => 102,
                                            "message" => "无信息"
                                        );
                                        die(json_encode($return, JSON_UNESCAPED_UNICODE));
                                    }
                                }
        
                                if ($zt_list_type == 'zc'){
                                    // $today = date("Y-m-d",time());
                                    $sql_code = "SELECT * FROM `user` WHERE `user` NOT IN (SELECT `user` FROM `kaohe` WHERE `riqi`='$today' AND `$zt_list_time` IS NOT NULL) AND `team` = '$zt_list_team'";
                                    $sql = $mysql->query($sql_code);
                                    $num = $sql->num_rows;
                                    if ($num > 0) {
                                        while ($row = $sql->fetch_assoc()) {
                                            $Json[] = array(
                                                $row['user'],
                                                $row['name'],
                                                $row['dorm'],
                                                $row['dh'],
                                                $row['qw'],
                                                $row['tel1'],
                                                $row['tel2']
                                            );
                                        }
                                        $return = array(
                                            "code" => 100,
                                            "message" => "OK",
                                            "result" => $Json
                                        );
                                        die(json_encode($return, JSON_UNESCAPED_UNICODE));
                                    }else{
                                        $return = array(
                                            "code" => 102,
                                            "message" => "无信息"
                                        );
                                        die(json_encode($return, JSON_UNESCAPED_UNICODE));
                                    }
                                }
                            }
                        }else{
                            if ($zt_list_type == '0' || $zt_list_type == '1' || $zt_list_type == '2' || $zt_list_type == '3') {
                                // $today = date("Y-m-d",time());
        
                                $sql_code = "SELECT * FROM `user` WHERE `user` IN (SELECT `user` FROM `kaohe` WHERE `riqi`='$today' AND `$zt_list_time` = '$zt_list_type') ORDER by `team`";
                                $sql = $mysql->query($sql_code);
                                $num = $sql->num_rows;
                                if ($num > 0) {
                                    while ($row = $sql->fetch_assoc()) {
                                        $Json[] = array(
                                            $row['team'],
                                            $row['user'],
                                            $row['name'],
                                            $row['dorm'],
                                            $row['dh'],
                                            $row['qw'],
                                            $row['tel1'],
                                            $row['tel2']
                                        );
                                    }
                                    $return = array(
                                        "code" => 100,
                                        "message" => "OK",
                                        "result" => $Json
                                    );
                                    die(json_encode($return, JSON_UNESCAPED_UNICODE));
                                }else{
                                    $return = array(
                                        "code" => 102,
                                        "message" => "无信息"
                                    );
                                    die(json_encode($return, JSON_UNESCAPED_UNICODE));
                                }
                            }else{
                                if ($zt_list_type == 'yd'){
                                    $sql_code = "SELECT * FROM `user` ORDER by `team`";
                                    $sql = $mysql->query($sql_code);
                                    $num = $sql->num_rows;
                                    if ($num > 0) {
                                        while ($row = $sql->fetch_assoc()) {
                                            $Json[] = array(
                                                $row['team'],
                                                $row['user'],
                                                $row['name'],
                                                $row['dorm'],
                                                $row['dh'],
                                                $row['qw'],
                                                $row['tel1'],
                                                $row['tel2']
                                            );
                                        }
                                        $return = array(
                                            "code" => 100,
                                            "message" => "OK",
                                            "result" => $Json
                                        );
                                        die(json_encode($return, JSON_UNESCAPED_UNICODE));
                                    }else{
                                        $return = array(
                                            "code" => 102,
                                            "message" => "无信息"
                                        );
                                        die(json_encode($return, JSON_UNESCAPED_UNICODE));
                                    }
                                }
        
                                if ($zt_list_type == 'zc'){
                                    // $today = date("Y-m-d",time());
                                    $sql_code = "SELECT * FROM `user` WHERE `user` NOT IN (SELECT `user` FROM `kaohe` WHERE `riqi`='$today' AND `$zt_list_time` IS NOT NULL)  ORDER by `team`";
                                    $sql = $mysql->query($sql_code);
                                    $num = $sql->num_rows;
                                    if ($num > 0) {
                                        while ($row = $sql->fetch_assoc()) {
                                            $Json[] = array(
                                                $row['team'],
                                                $row['user'],
                                                $row['name'],
                                                $row['dorm'],
                                                $row['dh'],
                                                $row['qw'],
                                                $row['tel1'],
                                                $row['tel2']
                                            );
                                        }
                                        $return = array(
                                            "code" => 100,
                                            "message" => "OK",
                                            "result" => $Json
                                        );
                                        die(json_encode($return, JSON_UNESCAPED_UNICODE));
                                    }else{
                                        $return = array(
                                            "code" => 102,
                                            "message" => "无信息"
                                        );
                                        die(json_encode($return, JSON_UNESCAPED_UNICODE));
                                    }
                                }
                            }
                        }
                    }
                    
                    if ($admin_type == 'people_info') {
                        $people_info_type = $_GET['people_info_type'];
    
                        if($people_info_type == 'r'){
                            $user = $_GET['user'];
                            $sql_code = "SELECT * FROM `user` WHERE `user` = '$user'";
                            $sql = $mysql->query($sql_code);
                            $num = $sql->num_rows;
                            if ($num > 0) {
                                $row = $sql->fetch_assoc();
                                $Json = array(
                                    "user" => $row['user'],
                                    "name" => $row['name'],
                                    "sex" => $row['sex'],
                                    "team" => $row['team'],
                                    "zy" => $row['zy'],
                                    "bh" => $row['bh'],
                                    "sfzh" => $row['sfzh'],
                                    "dh" => $row['dh'],
                                    "dorm" => $row['dorm'],
                                    "qw" => $row['qw'],
                                    "jtzz" => $row['jtzz'],
                                    "tel1" => $row['tel1'],
                                    "tel2" => $row['tel2'],
                                    "tc" => $row['tc']
                                );
                                $return = array(
                                    "code" => 100,
                                    "message" => "OK",
                                    "result" => $Json
                                );
                                die(json_encode($return, JSON_UNESCAPED_UNICODE));
                            }else{
                                $return = array(
                                    "code" => 102,
                                    "message" => "无信息"
                                );
                                die(json_encode($return, JSON_UNESCAPED_UNICODE));
                            }
                        }
    
                        if($people_info_type == 'w'){
                            $user = $_POST['user'];
                            $name = $_POST['name'];
                            $sex = $_POST['sex'];
                            $team = $_POST['team'];
                            $zy = $_POST['zy'];
                            $sfzh = $_POST['sfzh'];
                            $dh = $_POST['dh'];
                            $dorm = $_POST['dorm'];
                            $qw = $_POST['qw'];
                            $jtzz = $_POST['jtzz'];
                            $tel1 = $_POST['tel1'];
                            $tel2 = $_POST['tel2'];
                            $tc = $_POST['tc'];
    
                            // if ($user != '' && $name != '' && $sex != '' && $team != '' && $zy != '' && $sfzh != '' && $dh != '' && $dorm != '' && $qw != '' && $jtzz != '' && $tel1 != '' && $tel2 != '' && $tc != '') {
    
                            // }else{
                            //     $return = array(
                            //         "code" => 103,
                            //         "message" => "数据不能为空"
                            //     );
                            //     die(json_encode($return, JSON_UNESCAPED_UNICODE));
                            // }
    
                            $sql_code = "UPDATE `user` SET `name` = '$name', `sex` = '$sex', `team` = '$team', `zy` = '$zy', `sfzh` = '$sfzh', `dh` = '$dh', `dorm` = '$dorm', `qw` = '$qw', `jtzz` = '$jtzz', `tel1` = '$tel1', `tel2` = '$tel2', `tc` = '$tc' WHERE `user` = '$user'";
                            if($mysql->query($sql_code)) {
                                $return = array(
                                    "code" => 100,
                                    "message" => "修改成功"
                                );
                                die(json_encode($return, JSON_UNESCAPED_UNICODE));
                            } else {
                                $return = array(
                                    "code" => 102,
                                    "message" => "修改失败"
                                );
                                die(json_encode($return, JSON_UNESCAPED_UNICODE));
                            }
                        }

                        if($people_info_type == 'd'){
                            $user = $_GET['user'];
                            $sql_code = "DELETE FROM `user` WHERE `user` = '$user'";
                            if($mysql->query($sql_code)){
                                $return = array(
                                    "code" => 100,
                                    "message" => "删除成功",
                                    "result" => $Json
                                );
                                die(json_encode($return, JSON_UNESCAPED_UNICODE));
                            }else{
                                $return = array(
                                    "code" => 102,
                                    "message" => "删除失败"
                                );
                                die(json_encode($return, JSON_UNESCAPED_UNICODE));
                            }
                        }

                    }

                    if ($admin_type == 'dormrq') {
                        $sql_code = "SELECT DISTINCT `riqi` FROM `kaohe` WHERE `cqqk` IS NOT NULL ORDER BY `riqi` DESC";
                        $sql = $mysql->query($sql_code);
                        $num = $sql->num_rows;
                        if ($num > 0) {
                            while ($row = $sql->fetch_assoc()) {
                                $Json[] = $row['riqi'];
                            }
                            $return = array(
                                "code" => 100,
                                "message" => "OK",
                                "result" => $Json
                            );
                            die(json_encode($return, JSON_UNESCAPED_UNICODE));
                        } else {
                            $return = array(
                                "code" => 102,
                                "message" => "日期获取失败"
                            );
                            die(json_encode($return, JSON_UNESCAPED_UNICODE));
                        }
                    }

                }

            }else{
                $return = array(
                    "code" => 101,
                    "message" => "缺少参数"
                );
                die(json_encode($return, JSON_UNESCAPED_UNICODE));
            }
        }
    }

    /**
     * 数字考勤转文字考勤
     */
    function num_str($num) {
        
        if ($num === '0') {
            return '×';
        }
        if ($num === '1') {
            return '〇';
        }
        if ($num === '2') {
            return '事';
        }
        if ($num === '3') {
            return '病';
        }
        if ($num === null) {
            return '√';
        }
    }

    /**
     * 获取日常考勤
     */
    function list($user){
        $mysql = new DB();
        $sql_code = "SELECT * FROM `kaohe` WHERE `user` = '$user' ORDER BY `riqi`";
        $sql = $mysql->query($sql_code);
        $num = $sql->num_rows;
        if ($num > 0) {
            $i = 0;
            $time = time_range('2020-09-22',date("Y-m-d",time()));
            while ($row = $sql->fetch_assoc()) {
                while ($row['riqi'] !== $time[$i]) {
                    if($row['riqi'] !== $time[$i]){
                        $Json[] = array(
                            "rq" => $time[$i],
                            "z" => '√',
                            "s" => '√',
                            "x" => '√',
                            "w" => '√',
                            "qk" => '无',
                            "jly" => '小队长'
                        );
                    }
                    ++$i;
                }

                $z = $row['z'];
                if ($z === null) {
                    $z = '4';
                }
                $s = $row['s'];
                if ($s === null) {
                    $s = '4';
                }
                $x = $row['x'];
                if ($x === null) {
                    $x = '4';
                }
                $w = $row['w'];
                if ($w === null) {
                    $w = '4';
                }

                $z = str_replace("0", "×", $z);
                $z = str_replace("1", "〇", $z);
                $z = str_replace("2", "事", $z);
                $z = str_replace("3", "病", $z);
                $z = str_replace("4", "√", $z);

                $s = str_replace("0", "×", $s);
                $s = str_replace("1", "〇", $s);
                $s = str_replace("2", "事", $s);
                $s = str_replace("3", "病", $s);
                $s = str_replace("4", "√", $s);

                $x = str_replace("0", "×", $x);
                $x = str_replace("1", "〇", $x);
                $x = str_replace("2", "事", $x);
                $x = str_replace("3", "病", $x);
                $x = str_replace("4", "√", $x);

                $w = str_replace("0", "×", $w);
                $w = str_replace("1", "〇", $w);
                $w = str_replace("2", "事", $w);
                $w = str_replace("3", "病", $w);
                $w = str_replace("4", "√", $w);

                
                if ($row['cqqk'] != '') {
                    $Json[] = array(
                        "rq" => $row['riqi'],
                        "z" => $z,
                        "s" => $s,
                        "x" => $x,
                        "w" => $w,
                        "qk" => $row['qk'] . '宿舍:' . $row['cqqk'],
                        "jly" => $row['dmczr']
                    );
                }else{
                    $Json[] = array(
                        "rq" => $row['riqi'],
                        "z" => $z,
                        "s" => $s,
                        "x" => $x,
                        "w" => $w,
                        "qk" => $row['qk'],
                        "jly" => $row['dmczr']
                    );
                }
                $i++;
            }
            
            while($i < count($time)){
                $Json[] = array(
                    'rq' => $time[$i],
                    'z' => '√',
                    's' => '√',
                    'x' => '√',
                    'w' => '√',
                    'qk' => '无',
                    "jly" => '小队长'
                );
                $i++;
            }
            return $Json;
        }else{
            $i = 0;
            $time = time_range('2020-09-22',date("Y-m-d",time()));
            while ($i < count($time)) {
                $Json[] = array(
                    "rq" => $time[$i],
                    "z" => '√',
                    "s" => '√',
                    "x" => '√',
                    "w" => '√',
                    "qk" => '无',
                    "jly" => '小队长'
                );
                ++$i;
            }
            return $Json;
        }
        
    }

    /**
     * 查询状态
     */
    function zt($user,$time) {
        $mysql = new DB();
        $today = date("Y-m-d",time());
        $sql_code = "SELECT * FROM `kaohe` WHERE `user` = '$user' AND `riqi`='$today'";
        $sql = $mysql->query($sql_code);
        $num = $sql->num_rows;
        if ($num > 0) {
            $row = $sql->fetch_assoc();
            $qk = '';
            switch ($row[$time]) {
                case '0':
                    $qk = '√';
                    break;
                case '1':
                    $qk = '〇';
                    break;
                case '2':
                    $qk = '事';
                    break;
                case '3':
                    $qk = '病';
                    break;
                default:
                    break;
            }
            return $qk;
        }else{
            return '×';
        }
    }

    /**
     * 应到
     */
    function yd($team = ''){
        $mysql = new DB();
        if($team == ''){
            $sql_code = "SELECT * FROM `user`";
            $sql = $mysql->query($sql_code);
            $num = $sql->num_rows;
            if ($num > 0) {
                while ($row = $sql->fetch_assoc()) {
                    $Json[] = array(
                        "user" => $row['user'],
                        "name" => $row['name']
                    );
                }
                return $Json;
            }
        }else{
            $sql_code = "SELECT * FROM `user` WHERE `team` = '$team'";
            $sql = $mysql->query($sql_code);
            $num = $sql->num_rows;
            if ($num > 0) {
                while ($row = $sql->fetch_assoc()) {
                    $Json[] = array(
                        "user" => $row['user'],
                        "name" => $row['name']
                    );
                }
                return $Json;
            }
        }
    }

    /**
     * 未到
     */
    function wd($team = '',$time = '',$today = ''){
        $mysql = new DB();
        if($today == ''){
            $today = date("Y-m-d",time());
        }
        if($team == ''){
            $sql_code = "SELECT * FROM `user` WHERE `user` IN (SELECT `user` FROM `kaohe` WHERE `riqi`='$today' AND `$time` = '0')";
            $sql = $mysql->query($sql_code);
            $num = $sql->num_rows;
            if ($num > 0) {
                while ($row = $sql->fetch_assoc()) {
                    $Json[] = array(
                        "user" => $row['user'],
                        "name" => $row['name']
                    );
                }
                return $Json;
            }
        }else{
            $sql_code = "SELECT * FROM `user` WHERE `user` IN (SELECT `user` FROM `kaohe` WHERE `riqi`='$today' AND `$time` = '0') AND `team` = '$team'";
            $sql = $mysql->query($sql_code);
            $num = $sql->num_rows;
            if ($num > 0) {
                while ($row = $sql->fetch_assoc()) {
                    $Json[] = array(
                        "user" => $row['user'],
                        "name" => $row['name']
                    );
                }
                return $Json;
            }
        }
    }

    /**
     * 正常
     */
    function zc($team = '',$time = '',$today = ''){
        $mysql = new DB();
        if($today == ''){
            $today = date("Y-m-d",time());
        }
        if($team == ''){
            $sql_code = "SELECT * FROM `user` WHERE `user` NOT IN (SELECT `user` FROM `kaohe` WHERE `riqi`='$today' AND `$time` IS NOT NULL)";
            $sql = $mysql->query($sql_code);
            $num = $sql->num_rows;
            if ($num > 0) {
                while ($row = $sql->fetch_assoc()) {
                    $Json[] = array(
                        "user" => $row['user'],
                        "name" => $row['name']
                    );
                }
                return $Json;
            }
        }else{
            $sql_code = "SELECT * FROM `user` WHERE `user` NOT IN (SELECT `user` FROM `kaohe` WHERE `riqi`='$today' AND `$time` IS NOT NULL) AND team = '$team'";
            $sql = $mysql->query($sql_code);
            $num = $sql->num_rows;
            if ($num > 0) {
                while ($row = $sql->fetch_assoc()) {
                    $Json[] = array(
                        "user" => $row['user'],
                        "name" => $row['name']
                    );
                }
                return $Json;
            }
        }
        
    }

    /**
     * 迟到
     */
    function cd($team = '',$time = '',$today = ''){
        $mysql = new DB();
        if($today == ''){
            $today = date("Y-m-d",time());
        }
        if($team == ''){
            $sql_code = "SELECT * FROM `user` WHERE `user` IN (SELECT `user` FROM `kaohe` WHERE `riqi`='$today' AND `$time` = '1')";
            $sql = $mysql->query($sql_code);
            $num = $sql->num_rows;
            if ($num > 0) {
                while ($row = $sql->fetch_assoc()) {
                    $Json[] = array(
                        "user" => $row['user'],
                        "name" => $row['name']
                    );
                }
                return $Json;
            }
        }else{
            $sql_code = "SELECT * FROM `user` WHERE `user` IN (SELECT `user` FROM `kaohe` WHERE `riqi`='$today' AND `$time` = '1') AND team = '$team'";
            $sql = $mysql->query($sql_code);
            $num = $sql->num_rows;
            if ($num > 0) {
                while ($row = $sql->fetch_assoc()) {
                    $Json[] = array(
                        "user" => $row['user'],
                        "name" => $row['name']
                    );
                }
                return $Json;
            }
        }
        
    }

    /**
     * 事假
     */
    function sj($team = '',$time = '',$today = ''){
        $mysql = new DB();
        if($today == ''){
            $today = date("Y-m-d",time());
        }
        if($team == ''){
            $sql_code = "SELECT * FROM `user` WHERE `user` IN (SELECT `user` FROM `kaohe` WHERE `riqi`='$today' AND `$time` = '2')";
            $sql = $mysql->query($sql_code);
            $num = $sql->num_rows;
            if ($num > 0) {
                while ($row = $sql->fetch_assoc()) {
                    $Json[] = array(
                        "user" => $row['user'],
                        "name" => $row['name']
                    );
                }
                return $Json;
            }
        }else{
            $sql_code = "SELECT * FROM `user` WHERE `user` IN (SELECT `user` FROM `kaohe` WHERE `riqi`='$today' AND `$time` = '2') AND team = '$team'";
            $sql = $mysql->query($sql_code);
            $num = $sql->num_rows;
            if ($num > 0) {
                while ($row = $sql->fetch_assoc()) {
                    $Json[] = array(
                        "user" => $row['user'],
                        "name" => $row['name']
                    );
                }
                return $Json;
            }
        }
        
    }

    /**
     * 病假
     */
    function bj($team = '',$time = '',$today = ''){
        $mysql = new DB();
        if($today == ''){
            $today = date("Y-m-d",time());
        }
        if($team == ''){
            $sql_code = "SELECT * FROM `user` WHERE `user` IN (SELECT `user` FROM `kaohe` WHERE `riqi`='$today' AND `$time` = '3')";
            $sql = $mysql->query($sql_code);
            $num = $sql->num_rows;
            if ($num > 0) {
                while ($row = $sql->fetch_assoc()) {
                    $Json[] = array(
                        "user" => $row['user'],
                        "name" => $row['name']
                    );
                }
                return $Json;
            }
        }else{
            $sql_code = "SELECT * FROM `user` WHERE `user` IN (SELECT `user` FROM `kaohe` WHERE `riqi`='$today' AND `$time` = '3') AND team = '$team'";
            $sql = $mysql->query($sql_code);
            $num = $sql->num_rows;
            if ($num > 0) {
                while ($row = $sql->fetch_assoc()) {
                    $Json[] = array(
                        "user" => $row['user'],
                        "name" => $row['name']
                    );
                }
                return $Json;
            }
        }
        
    }

    /**
     * 解析
     * 返回人名数组
     */
    function jx($array){
        foreach ($array as $key => $value) {
            $json[] = $value['name'];
        }
        return $json;
    }

    /**
     * 动态页面
     * 101 - 缺少参数
     */
    function page() {
        if (isset($_GET['page_type'])) {
            $page_type = $_GET['page_type'];

            if ($page_type == 'kaoqin') {
                $teamid = $_GET['teamid'];
                $page = red_file('../page/kaoqin.html');
                $page = str_replace("{team}", $teamid, $page);
                die($page);
            }

        }else{
            $return = array(
                "code" => 101,
                "message" => "缺少参数"
            );
            die(json_encode($return, JSON_UNESCAPED_UNICODE));
        }
    }

    /**
     * 操作
     */
    function oper() {
        if (isset($_GET['oper_type'])) {
            $mysql = new DB();
            $oper_type = $_GET['oper_type'];

            /**
             * 汇总
             * 100 - 操作成功
             * 102 - 操作失败
             */
            if ($oper_type == 'huizong') {
                if ($_SESSION["Extent"] == 'admin'){

                    if (isset($_GET['huizong_user']) && $_GET['huizong_user'] != '') {
                        $huizong_user = $_GET['huizong_user'];
                        $mysql = new DB();
                        $sql_code = "SELECT * FROM `user` WHERE `user` = '$huizong_user'";
                        $sql = $mysql->query($sql_code);
                        $num = $sql->num_rows;
                        if ($num > 0) {
                            while ($row = $sql->fetch_assoc()) {
                                $Json = array(
                                    "team" => $row['team'],
                                    "zy" => $row['zy'],
                                    "bh" => $row['bh'],
                                    "name" => $row['name'],
                                    "xb" => $row['sex'],
                                    "xh" => $row['user'],
                                    "sfzh" => $row['sfzh'],
                                    "dh" => $row['dh'],
                                    "ssh" => $row['dorm'],
                                    "qw" => $row['qw'],
                                    "jtzz" => $row['jtzz'],
                                    "tel1" => $row['tel1'],
                                    "tel2" => $row['tel2'],
                                    "tc" => $row['tc'],
                                    "list" => $this->list($row['user'])
                                );
                            }
                            echo word_write_hz($Json);
                        }else{
                            $return = array(
                                "code" => 102,
                                "message" => "无信息"
                            );
                            die(json_encode($return, JSON_UNESCAPED_UNICODE));
                        }
                    }else{
                        $return = array(
                            "code" => 101,
                            "message" => "缺少参数"
                        );
                        die(json_encode($return, JSON_UNESCAPED_UNICODE));
                    }
                }
            }

            /**
             * 每日考勤
             */
            if ($oper_type == 'mrkq') {
                if ($_SESSION["Extent"] == 'admin'){

                    if (isset($_GET['mrkq_team']) && $_GET['mrkq_team'] != '') {
                        $mrkq_team = $_GET['mrkq_team'];
                        $today = date("Y年m月d日",time());
    
                        if($this->yd($mrkq_team) != false) {
                            $yd = count($this->yd($mrkq_team));
                        }else{
                            $yd = 0;
                        }
    
                        if($this->zc($mrkq_team,'z') != false) {
                            $zsd = count($this->zc($mrkq_team,'z'));
                        }else{
                            $zsd = 0;
                        }
                        if($this->zc($mrkq_team,'s') != false) {
                            $ssd = count($this->zc($mrkq_team,'s'));
                        }else{
                            $ssd = 0;
                        }
                        if($this->zc($mrkq_team,'x') != false) {
                            $xsd = count($this->zc($mrkq_team,'x'));
                        }else{
                            $xsd = 0;
                        }
                        if($this->zc($mrkq_team,'w') != false) {
                            $wsd = count($this->zc($mrkq_team,'w'));
                        }else{
                            $wsd = 0;
                        }
    
                        if($this->wd($mrkq_team,'z') != false) {
                            $arr = $this->wd($mrkq_team,'z');
                            $zwd = count($arr);
                            $zwdlist = join("、",$this->jx($arr));
                        }else{
                            $zwd = 0;
                            $zwdlist = '无';
                        }
                        if($this->wd($mrkq_team,'s') != false) {
                            $arr = $this->wd($mrkq_team,'s');
                            $swd = count($arr);
                            $swdlist = join("、",$this->jx($arr));
                        }else{
                            $swd = 0;
                            $swdlist = '无';
                        }
                        if($this->wd($mrkq_team,'x') != false) {
                            $arr = $this->wd($mrkq_team,'x');
                            $xwd = count($arr);
                            $xwdlist = join("、",$this->jx($arr));
                        }else{
                            $xwd = 0;
                            $xwdlist = '无';
                        }
                        if($this->wd($mrkq_team,'w') != false) {
                            $arr = $this->wd($mrkq_team,'w');
                            $wwd = count($arr);
                            $wwdlist = join("、",$this->jx($arr));
                        }else{
                            $wwd = 0;
                            $wwdlist = '无';
                        }
    
    
                        if($this->sj($mrkq_team,'z') != false) {
                            $arr = $this->sj($mrkq_team,'z');
                            $zsj = count($arr);
                            $zsjlist = join("、",$this->jx($arr));
                        }else{
                            $zsj = 0;
                            $zsjlist = '无';
                        }
                        if($this->sj($mrkq_team,'s') != false) {
                            $arr = $this->sj($mrkq_team,'s');
                            $ssj = count($arr);
                            $ssjlist = join("、",$this->jx($arr));
                            
                        }else{
                            $ssj = 0;
                            $ssjlist = '无';
                        }
                        if($this->sj($mrkq_team,'x') != false) {
                            $arr = $this->sj($mrkq_team,'x');
                            $xsj = count($arr);
                            $xsjlist = join("、",$this->jx($arr));
                        }else{
                            $xsj = 0;
                            $xsjlist = '无';
                        }
                        if($this->sj($mrkq_team,'w') != false) {
                            $arr = $this->sj($mrkq_team,'w');
                            $wsj = count($arr);
                            $wsjlist = join("、",$this->jx($arr));
                        }else{
                            $wsj = 0;
                            $wsjlist = '无';
                        }
    
    
                        if($this->bj($mrkq_team,'z') != false) {
                            $arr = $this->bj($mrkq_team,'z');
                            $zbj = count($arr);
                            $zbjlist = join("、",$this->jx($arr));
                        }else{
                            $zbj = 0;
                            $zbjlist = '无';
                        }
                        if($this->bj($mrkq_team,'s') != false) {
                            $arr = $this->bj($mrkq_team,'s');
                            $sbj = count($arr);
                            $sbjlist = join("、",$this->jx($arr));
                        }else{
                            $sbj = 0;
                            $sbjlist = '无';
                        }
                        if($this->bj($mrkq_team,'x') != false) {
                            $arr = $this->bj($mrkq_team,'x');
                            $xbj = count($arr);
                            $xbjlist = join("、",$this->jx($arr));
                        }else{
                            $xbj = 0;
                            $xbjlist = '无';
                        }
                        if($this->bj($mrkq_team,'w') != false) {
                            $arr = $this->bj($mrkq_team,'w');
                            $wbj = count($arr);
                            $wbjlist = join("、",$this->jx($arr));
                        }else{
                            $wbj = 0;
                            $wbjlist = '无';
                        }
    
    
                        if($this->cd($mrkq_team,'z') != false) {
                            $arr = $this->cd($mrkq_team,'z');
                            $zcd = count($arr);
                            $zcdlist = join("、",$this->jx($arr));
                        }else{
                            $zcd = 0;
                            $zcdlist = '无';
                        }
                        if($this->cd($mrkq_team,'s') != false) {
                            $arr = $this->cd($mrkq_team,'s');
                            $scd = count($arr);
                            $scdlist = join("、",$this->jx($arr));
                        }else{
                            $scd = 0;
                            $scdlist = '无';
                        }
                        if($this->cd($mrkq_team,'x') != false) {
                            $arr = $this->cd($mrkq_team,'x');
                            $xcd = count($arr);
                            $xcdlist = join("、",$this->jx($arr));
                        }else{
                            $xcd = 0;
                            $xcdlist = '无';
                        }
                        if($this->cd($mrkq_team,'w') != false) {
                            $arr = $this->cd($mrkq_team,'w');
                            $wcd = count($arr);
                            $wcdlist = join("、",$this->jx($arr));
                        }else{
                            $wcd = 0;
                            $wcdlist = '无';
                        }
                        
    
                        $Json = array(
                            'team' => $mrkq_team,
                            'riqi' => $today,
    
                            'zyd' => $yd,
                            'zsd' => $zsd,
                            'zwd' => $zwd,
                            'zsj' => $zsj,
                            'zbj' => $zbj,
                            'zcd' => $zcd,
                            'zwdlist' => $zwdlist,
                            'zcdlist' => $zcdlist,
                            'zsjlist' => $zsjlist,
                            'zbjlist' => $zbjlist,
                            
                            'syd' => $yd,
                            'ssd' => $ssd,
                            'swd' => $swd,
                            'ssj' => $ssj,
                            'sbj' => $sbj,
                            'scd' => $scd,
                            'swdlist' => $swdlist,
                            'scdlist' => $scdlist,
                            'ssjlist' => $ssjlist,
                            'sbjlist' => $sbjlist,
    
                            'xyd' => $yd,
                            'xsd' => $xsd,
                            'xwd' => $xwd,
                            'xsj' => $xsj,
                            'xbj' => $xbj,
                            'xcd' => $xcd,
                            'xwdlist' => $xwdlist,
                            'xcdlist' => $xcdlist,
                            'xsjlist' => $xsjlist,
                            'xbjlist' => $xbjlist,
    
                            'wyd' => $yd,
                            'wsd' => $wsd,
                            'wwd' => $wwd,
                            'wsj' => $wsj,
                            'wbj' => $wbj,
                            'wcd' => $wcd,
                            'wwdlist' => $wwdlist,
                            'wcdlist' => $wcdlist,
                            'wsjlist' => $wsjlist,
                            'wbjlist' => $wbjlist,
                        );
                        echo word_write_kq($Json);
                    }else{
                        $return = array(
                            "code" => 101,
                            "message" => "缺少参数"
                        );
                        die(json_encode($return, JSON_UNESCAPED_UNICODE));
                    }
                }
            }


            /**
             * 考勤
             */
            if ($oper_type == 'kaoqin') {
                if ($_SESSION["Extent"] == 'admin' || $_SESSION["Extent"] == 'putong'){
                    $sql_code = "SELECT `z_start`,`z_end`,`s_start`,`s_end`,`x_start`,`x_end`,`w_start`,`w_end` FROM `set`";
                    $sql = $mysql->query($sql_code);
                    $num = $sql->num_rows;
                    if ($num <= 0) {
                        $return = array(
                            "code" => 102,
                            "message" => "考勤日期范围获取失败"
                        );
                        die(json_encode($return, JSON_UNESCAPED_UNICODE));
                    }
                    $row = $sql->fetch_assoc();

                    $now = strtotime(date("Y-m-d H:i:s"));
                    $z_start = date("Y-m-d") . ' ' . $row['z_start'];
                    $z_end = date("Y-m-d") . ' ' . $row['z_end'];
    
                    $s_start = date("Y-m-d") . ' ' . $row['s_start'];
                    $s_end = date("Y-m-d") . ' ' . $row['s_end'];
    
                    $x_start = date("Y-m-d") . ' ' . $row['x_start'];
                    $x_end = date("Y-m-d") . ' ' . $row['x_end'];
    
                    $w_start = date("Y-m-d") . ' ' . $row['w_start'];
                    $w_end = date("Y-m-d") . ' ' . $row['w_end'];
    
                    $kaoqin_user = $_GET['kaoqin_user'];
                    $kaoqin_type = $_GET['kaoqin_type'];
                    $kaoqin_note = '无';
                    $kaoqin_date = date("Y-m-d",time());
                    if (isset($_GET['kaoqin_note']) && $_GET['kaoqin_note'] != '') {
                        $kaoqin_note = $_GET['kaoqin_note'];
                    }
                    $kaoqin_czr = $_GET['kaoqin_czr'];
                    
                    $sql_code = "SELECT * FROM `kaohe` WHERE `user` = '$kaoqin_user' AND `riqi` = '$kaoqin_date'";
                    $sql = $mysql->query($sql_code);
                    $num = $sql->num_rows;
                    
                    if($kaoqin_type == 'null'){
                        if ($num > 0) {
                            if ($now > strtotime($z_start) && $now < strtotime($z_end)) {
                                $sql_code = "UPDATE `kaohe` SET `z` = NULL, `dmczr` = '$kaoqin_czr' WHERE `user` = '$kaoqin_user' AND `riqi` = '$kaoqin_date'";
                            } elseif ($now > strtotime($s_start) && $now < strtotime($s_end)) {
                                $sql_code = "UPDATE `kaohe` SET `s` = NULL, `dmczr` = '$kaoqin_czr' WHERE `user` = '$kaoqin_user' AND `riqi` = '$kaoqin_date'";
                            } elseif ($now > strtotime($x_start) && $now < strtotime($x_end)) {
                                $sql_code = "UPDATE `kaohe` SET `x` = NULL, `dmczr` = '$kaoqin_czr' WHERE `user` = '$kaoqin_user' AND `riqi` = '$kaoqin_date'";
                            } elseif ($now > strtotime($w_start) && $now < strtotime($w_end)) {
                                $sql_code = "UPDATE `kaohe` SET `w` = NULL, `dmczr` = '$kaoqin_czr' WHERE `user` = '$kaoqin_user' AND `riqi` = '$kaoqin_date'";
                            } else {
                                $return = array(
                                    "code" => 104,
                                    "message" => "现在不是考勤时间"
                                );
                                die(json_encode($return, JSON_UNESCAPED_UNICODE));
                            }
        
                            if($mysql->query($sql_code)) {
                                $return = array(
                                    "code" => 100,
                                    "message" => "OK"
                                );
                                die(json_encode($return, JSON_UNESCAPED_UNICODE));
                            }else{
                                $return = array(
                                    "code" => 102,
                                    "message" => "操作失败"
                                );
                                die(json_encode($return, JSON_UNESCAPED_UNICODE));
                            }
                        } else {
                            $return = array(
                                "code" => 102,
                                "message" => "无需清除，已是正常考勤"
                            );
                            die(json_encode($return, JSON_UNESCAPED_UNICODE));
                        }
                    }else{
                        if ($num > 0) {
                            if ($now > strtotime($z_start) && $now < strtotime($z_end)) {
                                $sql_code = "UPDATE `kaohe` SET `z` = '$kaoqin_type', `qk` = CONCAT(`qk`,'$kaoqin_note,'), `dmczr` = '$kaoqin_czr' WHERE `user` = '$kaoqin_user' AND `riqi` = '$kaoqin_date'";
                            } elseif ($now > strtotime($s_start) && $now < strtotime($s_end)) {
                                $sql_code = "UPDATE `kaohe` SET `s` = '$kaoqin_type', `qk` = CONCAT(`qk`,'$kaoqin_note,'), `dmczr` = '$kaoqin_czr' WHERE `user` = '$kaoqin_user' AND `riqi` = '$kaoqin_date'";
                            } elseif ($now > strtotime($x_start) && $now < strtotime($x_end)) {
                                $sql_code = "UPDATE `kaohe` SET `x` = '$kaoqin_type', `qk` = CONCAT(`qk`,'$kaoqin_note,'), `dmczr` = '$kaoqin_czr' WHERE `user` = '$kaoqin_user' AND `riqi` = '$kaoqin_date'";
                            } elseif ($now > strtotime($w_start) && $now < strtotime($w_end)) {
                                $sql_code = "UPDATE `kaohe` SET `w` = '$kaoqin_type', `qk` = CONCAT(`qk`,'$kaoqin_note,'), `dmczr` = '$kaoqin_czr' WHERE `user` = '$kaoqin_user' AND `riqi` = '$kaoqin_date'";
                            } else {
                                $return = array(
                                    "code" => 104,
                                    "message" => "现在不是考勤时间"
                                );
                                die(json_encode($return, JSON_UNESCAPED_UNICODE));
                            }
        
                            if($mysql->query($sql_code)) {
                                $return = array(
                                    "code" => 100,
                                    "message" => "OK"
                                );
                                die(json_encode($return, JSON_UNESCAPED_UNICODE));
                            }else{
                                $return = array(
                                    "code" => 102,
                                    "message" => "操作失败"
                                );
                                die(json_encode($return, JSON_UNESCAPED_UNICODE));
                            }
                        } else {
                            if ($now > strtotime($z_start) && $now < strtotime($z_end)) {
                                $sql_code = "INSERT INTO `kaohe`(`user`, `riqi`, `z`, `qk`, `dmczr`) VALUES ('$kaoqin_user', '$kaoqin_date', '$kaoqin_type', '$kaoqin_note', '$kaoqin_czr')";
                            } elseif ($now > strtotime($s_start) && $now < strtotime($s_end)) {
                                $sql_code = "INSERT INTO `kaohe`(`user`, `riqi`, `s`, `qk`, `dmczr`) VALUES ('$kaoqin_user', '$kaoqin_date', '$kaoqin_type', '$kaoqin_note', '$kaoqin_czr')";
                            } elseif ($now > strtotime($x_start) && $now < strtotime($x_end)) {
                                $sql_code = "INSERT INTO `kaohe`(`user`, `riqi`, `x`, `qk`, `dmczr`) VALUES ('$kaoqin_user', '$kaoqin_date', '$kaoqin_type', '$kaoqin_note', '$kaoqin_czr')";
                            } elseif ($now > strtotime($w_start) && $now < strtotime($w_end)) {
                                $sql_code = "INSERT INTO `kaohe`(`user`, `riqi`, `w`, `qk`, `dmczr`) VALUES ('$kaoqin_user', '$kaoqin_date', '$kaoqin_type', '$kaoqin_note', '$kaoqin_czr')";
                            } else {
                                $return = array(
                                    "code" => 104,
                                    "message" => "现在不是考勤时间"
                                );
                                die(json_encode($return, JSON_UNESCAPED_UNICODE));
                            }
                            if($mysql->query($sql_code)) {
                                $return = array(
                                    "code" => 100,
                                    "message" => "OK"
                                );
                                die(json_encode($return, JSON_UNESCAPED_UNICODE));
                            }else{
                                $return = array(
                                    "code" => 102,
                                    "message" => "操作失败"
                                );
                                die(json_encode($return, JSON_UNESCAPED_UNICODE));
                            }
                        }
                    }

                    
                }

            }
            
            /**
             * 查寝
             */
            if ($oper_type == 'chaqin') {
                if ($_SESSION["Extent"] == 'admin' || $_SESSION["Extent"] == 'putong'){
                    
                    $chaqin_user = $_GET['chaqin_user'];
                    $chaqin_qk = '无';
                    
                    $chaqin_date = date("Y-m-d",time());
                    if (isset($_GET['chaqin_qk']) && $_GET['chaqin_qk'] != '') {
                        $chaqin_qk = $_GET['chaqin_qk'];
                    }
                    $chaqin_czr = $_GET['chaqin_czr'];
                    
                    $sql_code = "SELECT * FROM `kaohe` WHERE `user` = '$chaqin_user' AND `riqi` = '$chaqin_date'";
                    $sql = $mysql->query($sql_code);
                    $num = $sql->num_rows;
    
                    if ($num > 0) {
                        $sql_code = "UPDATE `kaohe` SET `cqqk` = '$chaqin_qk', `cqczr` = '$chaqin_czr' WHERE `user` = '$chaqin_user'";
                        if($mysql->query($sql_code)) {
                            $return = array(
                                "code" => 100,
                                "message" => "OK"
                            );
                            die(json_encode($return, JSON_UNESCAPED_UNICODE));
                        }else{
                            $return = array(
                                "code" => 102,
                                "message" => "操作失败"
                            );
                            die(json_encode($return, JSON_UNESCAPED_UNICODE));
                        }
                    }else{
                        $sql_code = "INSERT INTO `kaohe`(`user`, `riqi`, `cqqk`, `cqczr`) VALUES ('$chaqin_user', '$chaqin_date', '$chaqin_qk', '$chaqin_czr')";
                        if($mysql->query($sql_code)) {
                            $return = array(
                                "code" => 100,
                                "message" => "OK"
                            );
                            die(json_encode($return, JSON_UNESCAPED_UNICODE));
                        }else{
                            $return = array(
                                "code" => 102,
                                "message" => "操作失败"
                            );
                            die(json_encode($return, JSON_UNESCAPED_UNICODE));
                        }
                    }
                }
                
            }

            /**
             * 修改密码
             */
            if($oper_type == 'xgmm') {
                if ($_SESSION["Extent"] == 'admin'){
                    $pass_type = $_GET['pass_type'];
                    $pass = $_GET['pass'];

                    if($pass_type == 'admin'){
                        $sql_code = "UPDATE `set` SET `adminpass` = '$pass'";
                        if($mysql->query($sql_code)) {
                            $return = array(
                                "code" => 100,
                                "message" => "修改成功"
                            );
                            die(json_encode($return, JSON_UNESCAPED_UNICODE));
                        }else{
                            $return = array(
                                "code" => 101,
                                "message" => "修改失败"
                            );
                            die(json_encode($return, JSON_UNESCAPED_UNICODE));
                        }
                    }

                    if($pass_type == 'putong'){
                        $sql_code = "UPDATE `set` SET `pass` = '$pass'";
                        if($mysql->query($sql_code)) {
                            $return = array(
                                "code" => 100,
                                "message" => "修改成功"
                            );
                            die(json_encode($return, JSON_UNESCAPED_UNICODE));
                        }else{
                            $return = array(
                                "code" => 101,
                                "message" => "修改失败"
                            );
                            die(json_encode($return, JSON_UNESCAPED_UNICODE));
                        }
                    }
                }
            }

            if($oper_type == 'xgtime') {
                if ($_SESSION["Extent"] == 'admin'){
                    $pass_type = $_GET['pass_type'];
                    $pass = $_GET['pass'];

                    if($pass_type == 'admin'){
                        $sql_code = "UPDATE `set` SET `adminpass` = '$pass'";
                        if($mysql->query($sql_code)) {
                            $return = array(
                                "code" => 100,
                                "message" => "修改成功"
                            );
                            die(json_encode($return, JSON_UNESCAPED_UNICODE));
                        }else{
                            $return = array(
                                "code" => 101,
                                "message" => "修改失败"
                            );
                            die(json_encode($return, JSON_UNESCAPED_UNICODE));
                        }
                    }

                    if($pass_type == 'putong'){
                        $sql_code = "UPDATE `set` SET `pass` = '$pass'";
                        if($mysql->query($sql_code)) {
                            $return = array(
                                "code" => 100,
                                "message" => "修改成功"
                            );
                            die(json_encode($return, JSON_UNESCAPED_UNICODE));
                        }else{
                            $return = array(
                                "code" => 101,
                                "message" => "修改失败"
                            );
                            die(json_encode($return, JSON_UNESCAPED_UNICODE));
                        }
                    }
                }
            }
            
        }else{
            $return = array(
                "code" => 101,
                "message" => "缺少参数"
            );
            die(json_encode($return, JSON_UNESCAPED_UNICODE));
        }
    }

    /**
     * 权限检测
     */
    function qxian() {
        if(isset($_GET['pass']) && $_GET['pass'] != ''){
            $pass = $_GET['pass'];
            $mysql = new DB();
            $sql_code = "SELECT * FROM `set`";
            $sql = $mysql->query($sql_code); 
            $row = $sql->fetch_assoc();

            if ($pass == $row['adminpass']){
                $_SESSION["Extent"] = 'admin';
                $return = array(
                    "code" => 100,
                    "message" => "密码正确！"
                );
                die(json_encode($return, JSON_UNESCAPED_UNICODE));
            }
            
            if ($pass == $row['pass']){
                $_SESSION["Extent"] = 'putong';
                $return = array(
                    "code" => 100,
                    "message" => "密码正确！"
                );
                die(json_encode($return, JSON_UNESCAPED_UNICODE));
            }

            $return = array(
                "code" => 101,
                "message" => "密码错误"
            );
            die(json_encode($return, JSON_UNESCAPED_UNICODE));
        }else{
            if (isset($_SESSION["Extent"]) && $_SESSION["Extent"] != '') {
                switch ($_SESSION["Extent"]) {
                    case 'admin':
                        $return = array(
                            "code" => 100,
                            "message" => "管理员，欢迎您！"
                        );
                        die(json_encode($return, JSON_UNESCAPED_UNICODE));
                        break;
                    case 'putong':
                        $return = array(
                            "code" => 101,
                            "message" => "会员，欢迎您！"
                        );
                        die(json_encode($return, JSON_UNESCAPED_UNICODE));
                        break;
                    default:
                        $return = array(
                            "code" => 102,
                            "message" => "权限不足"
                        );
                        die(json_encode($return, JSON_UNESCAPED_UNICODE));
                        break;
                }
            }else{
                $return = array(
                    "code" => 103,
                    "message" => "请输入密码"
                );
                die(json_encode($return, JSON_UNESCAPED_UNICODE));
            }
        }

    }

}