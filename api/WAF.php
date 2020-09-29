<?php
$getfilter = "select|(and|or)\\b.+?(>|<|=|in|like)|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";
$postfilter = "\\b(and|or)\\b.{1,6}?(=|>|<|\\bin\\b|\\blike\\b)|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";
$cookiefilter = "\\b(and|or)\\b.{1,6}?(=|>|<|\\bin\\b|\\blike\\b)|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";
function Codepage($strgets, $strgetsq, $strgetsql)
{
    if (preg_match("/" . $strgetsql . "/is", $strgetsq) == 1) {
        $url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'];
        $hackclientip = $_SERVER['REMOTE_ADDR'];
        http_response_code(502);

        echo '<br>You have triggered the WAF protection rules';
        echo '<br>你已经触发WAF防护规则';
        echo '<br>触发IP：' . $hackclientip;
        // echo '<br>提交地址：' . $url;
        $POSTSTRING = http_build_query($_POST);
        $GETSTRING = http_build_query($_GET);
        $COOKIESTRING = http_build_query($_COOKIE);
        $sqlfile = PATH . '/log.txt';
        $content = '[' . date("Y-m-d h:i:s") . '] - [' . $hackclientip . '] - [' . $url . '] - [' . $POSTSTRING . '] - [' . $GETSTRING . '] -[' . $COOKIESTRING . "]\r\n";
        if (file_put_contents($sqlfile, $content, FILE_APPEND)) {
            echo '<br>你的操作记录已经被记录！如继续非法攻击本站！将采取法律措施！';
        }
        exit();
    }
}
foreach ($_GET as $key => $value) {
    Codepage($key, $value, $getfilter);
}
foreach ($_POST as $key => $value) {
    Codepage($key, $value, $postfilter);
}
foreach ($_COOKIE as $key => $value) {
    Codepage($key, $value, $cookiefilter);
}
