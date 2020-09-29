function xxlb(team, type, time) {
    switch (type) {
        case 'wd':
            type = '0';
            break;
        case 'cd':
            type = '1';
            break;
        case 'sj':
            type = '2';
            break;
        case 'bj':
            type = '3';
            break;
        default:
            break;
    }


    if (team == 'all') {
        $.get("api/?type=query&query_type=admin&admin_type=zt_list&zt_list_type=" + type + "&zt_list_time=" + time + "&riqi="+$("#kqrq option:selected").val(), function (data) {
            var json = $.parseJSON(data);
            switch (json.code) {
                case 100:
                    var html = '<table> <tr> <td>队伍</td> <td>学号</td> <td>姓名</td> <td>宿舍</td> <td>电话</td> <td>QQ微信</td> <td>家长联系方式1</td> <td>家长联系方式2</td> </tr> ';
                    for (const key in json.result) {
                        let team = json.result[key][0];
                        let user = json.result[key][1];
                        let name = json.result[key][2];
                        let dorm = json.result[key][3];
                        let qw = json.result[key][4];
                        let dh = json.result[key][5];
                        let tel1 = json.result[key][6];
                        let tel2 = json.result[key][7];
    
                        html = html + '<tr> <td>' + team + ' 队</td> <td>' + user + '</td> <td>' + name + '</td> <td>' + dorm + '</td> <td>' + qw + '</td> <td>' + dh + '</td> <td>' + tel1 + '</td> <td>' + tel2 + '</td> </tr>';
                    }
                    html = html + ' </table>';
                    $(".info_list").html('');
                    $(".info_list").html(html);
                    $(".showinfo").show();
                    break;
                default:
                    alert(json.message);
                    break;
            }
        });
    }else{
        $.get("api/?type=query&query_type=admin&admin_type=zt_list&zt_list_type=" + type + "&zt_list_team=" + team + "&zt_list_time=" + time + "&riqi="+$("#kqrq option:selected").val(), function (data) {
            var json = $.parseJSON(data);
            switch (json.code) {
                case 100:
                    var html = '<table> <tr> <td>学号</td> <td>姓名</td> <td>宿舍</td> <td>电话</td> <td>QQ微信</td> <td>家长联系方式1</td> <td>家长联系方式2</td> </tr> ';
                    for (const key in json.result) {
                        let user = json.result[key][0];
                        let name = json.result[key][1];
                        let dorm = json.result[key][2];
                        let qw = json.result[key][3];
                        let dh = json.result[key][4];
                        let tel1 = json.result[key][5];
                        let tel2 = json.result[key][6];
    
                        html = html + '<tr> <td>' + user + '</td> <td>' + name + '</td> <td>' + dorm + '</td> <td>' + qw + '</td> <td>' + dh + '</td> <td>' + tel1 + '</td> <td>' + tel2 + '</td> </tr>';
                    }
                    html = html + ' </table>';
                    $(".info_list").html('');
                    $(".info_list").html(html);
                    $(".showinfo").show();
                    break;
                default:
                    alert(json.message);
                    break;
            }
        });
    }

}

$(document).ready(function () {
    
    $.get("api/?type=qxian", function (data) {
        var json = $.parseJSON(data);
        if (json.code != 100) {
            switch (json.code) {
                case 103:
                    var pass = prompt("请输入密码");
                    if (pass != null && pass != "") {
                        $.get("api/?type=qxian&pass=" + pass, function (data) {
                            var json = $.parseJSON(data);
                            switch (json.code) {
                                case 100:
                                    window.location.href = '';
                                    break;
                                default:
                                    alert(json.message);
                                    window.location.href = '';
                                    break;
                            }
                        });
                    } else {
                        window.location.href = '';
                    }
                    break;
                case 102:
                    var pass = prompt("密码权限不足！请重新输入密码！");
                    if (pass != null && pass != "") {
                        $.get("api/?type=qxian&pass=" + pass, function (data) {
                            var json = $.parseJSON(data);
                            switch (json.code) {
                                case 100:
                                    window.location.href = '';
                                    break;
                                default:
                                    alert(json.message);
                                    window.location.href = '';
                                    break;
                            }
                        });
                    } else {
                        window.location.href = '';
                    }
                    break;
                default:
                    var pass = prompt("请输入密码");
                    if (pass != null && pass != "") {
                        $.get("api/?type=qxian&pass=" + pass, function (data) {
                            var json = $.parseJSON(data);
                            switch (json.code) {
                                case 100:
                                    window.location.href = '';
                                    break;
                                default:
                                    alert(json.message);
                                    window.location.href = '';
                                    break;
                            }
                        });
                    } else {
                        window.location.href = '';
                    }
                    break;
            }
        }
    });
    
    $.get("api/?type=query&query_type=kqrq", function (data) {
        var json = $.parseJSON(data);
        switch (json.code) {
            case 100:
                var html = '';
                // var i = json.result.length;
                
                for (const key in json.result) {
                    // if (i > 1) {
                        html = html + '<option value="'+json.result[key]+'">'+json.result[key]+'</option>';
                    // }
                    // i--;
                }
                $("#kqrq").html(html);
                break;
            default:
                alert(json.message);
                break;
        }
    });
    
    $.get("api/?type=query&query_type=admin&admin_type=all", function (data) {
        var json = $.parseJSON(data);
        switch (json.code) {
            case 100:
                var html = '';
                $("#zong").html('');
                for (const key in json.result) {
                    html = '<table> <tr> <td colspan="7"><strong>' + json.result[key].time + '</strong></td> </tr> <tr> ';

                    if (json.result[key].time == '早上') {
                        html = html + '<td>应到</td> <td>实到</td> <td>未到</td> <td>迟到</td> <td>事假</td> <td>病假</td> </tr> <tr> <td><a onclick="xxlb(\'all\',\'yd\',\'z\')">' + json.result[key].yd + '</a></td> <td><a onclick="xxlb(\'all\',\'zc\',\'z\')">' + json.result[key].zc + '</a></td> <td><a onclick="xxlb(\'all\',\'wd\',\'z\')">' + json.result[key].wd + '</a></td> <td><a onclick="xxlb(\'all\',\'cd\',\'z\')">' + json.result[key].cd + '</a></td> <td><a onclick="xxlb(\'all\',\'sj\',\'z\')">' + json.result[key].sj + '</a></td> <td><a onclick="xxlb(\'all\',\'bj\',\'z\')">' + json.result[key].bj + '</a></td> </tr> </table>';
                        $("#zong").append(html);
                    }

                    if (json.result[key].time == '上午') {
                        html = html + '<td>应到</td> <td>实到</td> <td>未到</td> <td>迟到</td> <td>事假</td> <td>病假</td> </tr> <tr> <td><a onclick="xxlb(\'all\',\'yd\',\'s\')">' + json.result[key].yd + '</a></td> <td><a onclick="xxlb(\'all\',\'zc\',\'s\')">' + json.result[key].zc + '</a></td> <td><a onclick="xxlb(\'all\',\'wd\',\'s\')">' + json.result[key].wd + '</a></td> <td><a onclick="xxlb(\'all\',\'cd\',\'s\')">' + json.result[key].cd + '</a></td> <td><a onclick="xxlb(\'all\',\'sj\',\'s\')">' + json.result[key].sj + '</a></td> <td><a onclick="xxlb(\'all\',\'bj\',\'s\')">' + json.result[key].bj + '</a></td> </tr> </table>';
                        $("#zong").append(html);
                    }

                    if (json.result[key].time == '下午') {
                        html = html + '<td>应到</td> <td>实到</td> <td>未到</td> <td>迟到</td> <td>事假</td> <td>病假</td> </tr> <tr> <td><a onclick="xxlb(\'all\',\'yd\',\'x\')">' + json.result[key].yd + '</a></td> <td><a onclick="xxlb(\'all\',\'zc\',\'x\')">' + json.result[key].zc + '</a></td> <td><a onclick="xxlb(\'all\',\'wd\',\'x\')">' + json.result[key].wd + '</a></td> <td><a onclick="xxlb(\'all\',\'cd\',\'x\')">' + json.result[key].cd + '</a></td> <td><a onclick="xxlb(\'all\',\'sj\',\'x\')">' + json.result[key].sj + '</a></td> <td><a onclick="xxlb(\'all\',\'bj\',\'x\')">' + json.result[key].bj + '</a></td> </tr> </table>';
                        $("#zong").append(html);
                    }

                    if (json.result[key].time == '晚上') {
                        html = html + '<td>应到</td> <td>实到</td> <td>未到</td> <td>迟到</td> <td>事假</td> <td>病假</td> </tr> <tr> <td><a onclick="xxlb(\'all\',\'yd\',\'w\')">' + json.result[key].yd + '</a></td> <td><a onclick="xxlb(\'all\',\'zc\',\'w\')">' + json.result[key].zc + '</a></td> <td><a onclick="xxlb(\'all\',\'wd\',\'w\')">' + json.result[key].wd + '</a></td> <td><a onclick="xxlb(\'all\',\'cd\',\'w\')">' + json.result[key].cd + '</a></td> <td><a onclick="xxlb(\'all\',\'sj\',\'w\')">' + json.result[key].sj + '</a></td> <td><a onclick="xxlb(\'all\',\'bj\',\'w\')">' + json.result[key].bj + '</a></td> </tr> </table>';
                        $("#zong").append(html);
                    }
                    
                }
                break;
            default:
                alert(json.message);
                break;
        }
    });

    $.get("api/?type=query&query_type=admin&admin_type=list", function (data) {
        $("#list").html('');
        var json = $.parseJSON(data);
        switch (json.code) {
            case 100:
                var html = '';
                for (const key in json.result) {
                    html = '<table> <tr> <td colspan="7"><strong>' + json.result[key].time + '</strong></td> </tr> ';

                    if (json.result[key].time == '早上') {
                        for (const key1 in json.result[key].info) {
                            let team = json.result[key].info[key1].team;
                            html = html + '<tr> <td rowspan="2">' + team + '队</td> <td>应到</td> <td>实到</td> <td>未到</td> <td>迟到</td> <td>事假</td> <td>病假</td> </tr> <tr> <td><a onclick="xxlb(' + team + ',\'yd\',\'z\')">' + json.result[key].info[key1].yd + '</a></td> <td><a onclick="xxlb(' + team + ',\'zc\',\'z\')">' + json.result[key].info[key1].zc + '</a></td> <td><a onclick="xxlb(' + team + ',\'wd\',\'z\')">' + json.result[key].info[key1].wd + '</a></td> <td><a onclick="xxlb(' + team + ',\'cd\',\'z\')">' + json.result[key].info[key1].cd + '</a></td> <td><a onclick="xxlb(' + team + ',\'sj\',\'z\')">' + json.result[key].info[key1].sj + '</a></td> <td><a onclick="xxlb(' + team + ',\'bj\',\'z\')">' + json.result[key].info[key1].bj + '</a></td>';
                        }
                        html = html + ' </tr> </table>';
                        $("#list").append(html);
                    }

                    if (json.result[key].time == '上午') {
                        for (const key1 in json.result[key].info) {
                            let team = json.result[key].info[key1].team;
                            html = html + '<tr> <td rowspan="2">' + team + '队</td> <td>应到</td> <td>实到</td> <td>未到</td> <td>迟到</td> <td>事假</td> <td>病假</td> </tr> <tr> <td><a onclick="xxlb(' + team + ',\'yd\',\'s\')">' + json.result[key].info[key1].yd + '</a></td> <td><a onclick="xxlb(' + team + ',\'zc\',\'s\')">' + json.result[key].info[key1].zc + '</a></td> <td><a onclick="xxlb(' + team + ',\'wd\',\'s\')">' + json.result[key].info[key1].wd + '</a></td> <td><a onclick="xxlb(' + team + ',\'cd\',\'s\')">' + json.result[key].info[key1].cd + '</a></td> <td><a onclick="xxlb(' + team + ',\'sj\',\'s\')">' + json.result[key].info[key1].sj + '</a></td> <td><a onclick="xxlb(' + team + ',\'bj\',\'s\')">' + json.result[key].info[key1].bj + '</a></td>';
                        }
                        html = html + ' </tr> </table>';
                        $("#list").append(html);
                    }

                    if (json.result[key].time == '下午') {
                        for (const key1 in json.result[key].info) {
                            let team = json.result[key].info[key1].team;
                            html = html + '<tr> <td rowspan="2">' + team + '队</td> <td>应到</td> <td>实到</td> <td>未到</td> <td>迟到</td> <td>事假</td> <td>病假</td> </tr> <tr> <td><a onclick="xxlb(' + team + ',\'yd\',\'x\')">' + json.result[key].info[key1].yd + '</a></td> <td><a onclick="xxlb(' + team + ',\'zc\',\'x\')">' + json.result[key].info[key1].zc + '</a></td> <td><a onclick="xxlb(' + team + ',\'wd\',\'x\')">' + json.result[key].info[key1].wd + '</a></td> <td><a onclick="xxlb(' + team + ',\'cd\',\'x\')">' + json.result[key].info[key1].cd + '</a></td> <td><a onclick="xxlb(' + team + ',\'sj\',\'x\')">' + json.result[key].info[key1].sj + '</a></td> <td><a onclick="xxlb(' + team + ',\'bj\',\'x\')">' + json.result[key].info[key1].bj + '</a></td>';
                        }
                        html = html + ' </tr> </table>';
                        $("#list").append(html);
                    }

                    if (json.result[key].time == '晚上') {
                        for (const key1 in json.result[key].info) {
                            let team = json.result[key].info[key1].team;
                            html = html + '<tr> <td rowspan="2">' + team + '队</td> <td>应到</td> <td>实到</td> <td>未到</td> <td>迟到</td> <td>事假</td> <td>病假</td> </tr> <tr> <td><a onclick="xxlb(' + team + ',\'yd\',\'w\')">' + json.result[key].info[key1].yd + '</a></td> <td><a onclick="xxlb(' + team + ',\'zc\',\'w\')">' + json.result[key].info[key1].zc + '</a></td> <td><a onclick="xxlb(' + team + ',\'wd\',\'w\')">' + json.result[key].info[key1].wd + '</a></td> <td><a onclick="xxlb(' + team + ',\'cd\',\'w\')">' + json.result[key].info[key1].cd + '</a></td> <td><a onclick="xxlb(' + team + ',\'sj\',\'w\')">' + json.result[key].info[key1].sj + '</a></td> <td><a onclick="xxlb(' + team + ',\'bj\',\'w\')">' + json.result[key].info[key1].bj + '</a></td>';
                        }
                        html = html + ' </tr> </table>';
                        $("#list").append(html);
                    }
                }
                break;
            default:
                alert(json.message);
                break;
        }
    });

    $("#wdxz").click(function () {
        $("#tool").show();
        $("#kq").hide();
        $("#sshe").hide();

        $(this).addClass("select").siblings().removeClass("select");

        $.get("api/?type=query&query_type=team", function (data) {
            $("#teamlist").html('');
            $("#downteamlist").html('');
            var json = $.parseJSON(data);
            switch (json.code) {
                case 100:
                    var html = '<option value="-">---请选择---</option>';
                    $("#downteamlist").html(html);
                    for (const key in json.result) {
                        html = '<option value="' + json.result[key] + '">' + json.result[key] + ' 队</option>';
                        $("#teamlist").append(html);
                        $("#downteamlist").append(html);
                    }
                    break;
                default:
                    alert(json.message);
                    break;
            }
        });
    });

    $("#downteamlist").change(function () {
        if ($("#downteamlist option:selected").val() != '-') {
            $("#downnamelist").html('');
            $.get("api/?type=query&query_type=people&people_team=" + $("#downteamlist option:selected").val(), function (data) {
                var json = $.parseJSON(data);
                switch (json.code) {
                    case 100:
                        var html = '<option value="-">---请选择---</option>';
                        $("#downnamelist").html(html);
                        for (const key in json.result) {
                            html = '<option value="' + json.result[key].xh + '">' + json.result[key].xm + '</option>';
                            $("#downnamelist").append(html);
                        }
                        break;
                    default:
                        alert(json.message);
                        break;
                }
            });
        }
    });

    $("#kqxx").click(function () {
        $("#tool").hide();
        $("#kq").show();
        $("#sshe").hide();

        $("#zong").html('');
        $("#list").html('');
        $(this).addClass("select").siblings().removeClass("select");
        
        $.get("api/?type=query&query_type=admin&admin_type=all", function (data) {
            var json = $.parseJSON(data);
            switch (json.code) {
                case 100:
                    var html = '';
                    $("#zong").html('');
                    for (const key in json.result) {
                        html = '<table> <tr> <td colspan="7"><strong>' + json.result[key].time + '</strong></td> </tr> <tr> ';
    
                        if (json.result[key].time == '早上') {
                            html = html + '<td>应到</td> <td>实到</td> <td>未到</td> <td>迟到</td> <td>事假</td> <td>病假</td> </tr> <tr> <td><a onclick="xxlb(\'all\',\'yd\',\'z\')">' + json.result[key].yd + '</a></td> <td><a onclick="xxlb(\'all\',\'zc\',\'z\')">' + json.result[key].zc + '</a></td> <td><a onclick="xxlb(\'all\',\'wd\',\'z\')">' + json.result[key].wd + '</a></td> <td><a onclick="xxlb(\'all\',\'cd\',\'z\')">' + json.result[key].cd + '</a></td> <td><a onclick="xxlb(\'all\',\'sj\',\'z\')">' + json.result[key].sj + '</a></td> <td><a onclick="xxlb(\'all\',\'bj\',\'z\')">' + json.result[key].bj + '</a></td> </tr> </table>';
                            $("#zong").append(html);
                        }
    
                        if (json.result[key].time == '上午') {
                            html = html + '<td>应到</td> <td>实到</td> <td>未到</td> <td>迟到</td> <td>事假</td> <td>病假</td> </tr> <tr> <td><a onclick="xxlb(\'all\',\'yd\',\'s\')">' + json.result[key].yd + '</a></td> <td><a onclick="xxlb(\'all\',\'zc\',\'s\')">' + json.result[key].zc + '</a></td> <td><a onclick="xxlb(\'all\',\'wd\',\'s\')">' + json.result[key].wd + '</a></td> <td><a onclick="xxlb(\'all\',\'cd\',\'s\')">' + json.result[key].cd + '</a></td> <td><a onclick="xxlb(\'all\',\'sj\',\'s\')">' + json.result[key].sj + '</a></td> <td><a onclick="xxlb(\'all\',\'bj\',\'s\')">' + json.result[key].bj + '</a></td> </tr> </table>';
                            $("#zong").append(html);
                        }
    
                        if (json.result[key].time == '下午') {
                            html = html + '<td>应到</td> <td>实到</td> <td>未到</td> <td>迟到</td> <td>事假</td> <td>病假</td> </tr> <tr> <td><a onclick="xxlb(\'all\',\'yd\',\'x\')">' + json.result[key].yd + '</a></td> <td><a onclick="xxlb(\'all\',\'zc\',\'x\')">' + json.result[key].zc + '</a></td> <td><a onclick="xxlb(\'all\',\'wd\',\'x\')">' + json.result[key].wd + '</a></td> <td><a onclick="xxlb(\'all\',\'cd\',\'x\')">' + json.result[key].cd + '</a></td> <td><a onclick="xxlb(\'all\',\'sj\',\'x\')">' + json.result[key].sj + '</a></td> <td><a onclick="xxlb(\'all\',\'bj\',\'x\')">' + json.result[key].bj + '</a></td> </tr> </table>';
                            $("#zong").append(html);
                        }
    
                        if (json.result[key].time == '晚上') {
                            html = html + '<td>应到</td> <td>实到</td> <td>未到</td> <td>迟到</td> <td>事假</td> <td>病假</td> </tr> <tr> <td><a onclick="xxlb(\'all\',\'yd\',\'w\')">' + json.result[key].yd + '</a></td> <td><a onclick="xxlb(\'all\',\'zc\',\'w\')">' + json.result[key].zc + '</a></td> <td><a onclick="xxlb(\'all\',\'wd\',\'w\')">' + json.result[key].wd + '</a></td> <td><a onclick="xxlb(\'all\',\'cd\',\'w\')">' + json.result[key].cd + '</a></td> <td><a onclick="xxlb(\'all\',\'sj\',\'w\')">' + json.result[key].sj + '</a></td> <td><a onclick="xxlb(\'all\',\'bj\',\'w\')">' + json.result[key].bj + '</a></td> </tr> </table>';
                            $("#zong").append(html);
                        }
                        
                    }
                    break;
                default:
                    alert(json.message);
                    break;
            }
        });

        $.get("api/?type=query&query_type=admin&admin_type=list", function (data) {
            var json = $.parseJSON(data);
            switch (json.code) {
                case 100:
                    var html = '';
                    for (const key in json.result) {
                        html = '<table> <tr> <td colspan="7"><strong>' + json.result[key].time + '</strong></td> </tr> ';

                        if (json.result[key].time == '早上') {
                            for (const key1 in json.result[key].info) {
                                let team = json.result[key].info[key1].team;
                                html = html + '<tr> <td rowspan="2">' + team + '队</td> <td>应到</td> <td>实到</td> <td>未到</td> <td>迟到</td> <td>事假</td> <td>病假</td> </tr> <tr> <td><a onclick="xxlb(' + team + ',\'yd\',\'z\')">' + json.result[key].info[key1].yd + '</a></td> <td><a onclick="xxlb(' + team + ',\'zc\',\'z\')">' + json.result[key].info[key1].zc + '</a></td> <td><a onclick="xxlb(' + team + ',\'wd\',\'z\')">' + json.result[key].info[key1].wd + '</a></td> <td><a onclick="xxlb(' + team + ',\'cd\',\'z\')">' + json.result[key].info[key1].cd + '</a></td> <td><a onclick="xxlb(' + team + ',\'sj\',\'z\')">' + json.result[key].info[key1].sj + '</a></td> <td><a onclick="xxlb(' + team + ',\'bj\',\'z\')">' + json.result[key].info[key1].bj + '</a></td>';
                            }
                            html = html + ' </tr> </table>';
                            $("#list").append(html);
                        }

                        if (json.result[key].time == '上午') {
                            for (const key1 in json.result[key].info) {
                                let team = json.result[key].info[key1].team;
                                html = html + '<tr> <td rowspan="2">' + team + '队</td> <td>应到</td> <td>实到</td> <td>未到</td> <td>迟到</td> <td>事假</td> <td>病假</td> </tr> <tr> <td><a onclick="xxlb(' + team + ',\'yd\',\'s\')">' + json.result[key].info[key1].yd + '</a></td> <td><a onclick="xxlb(' + team + ',\'zc\',\'s\')">' + json.result[key].info[key1].zc + '</a></td> <td><a onclick="xxlb(' + team + ',\'wd\',\'s\')">' + json.result[key].info[key1].wd + '</a></td> <td><a onclick="xxlb(' + team + ',\'cd\',\'s\')">' + json.result[key].info[key1].cd + '</a></td> <td><a onclick="xxlb(' + team + ',\'sj\',\'s\')">' + json.result[key].info[key1].sj + '</a></td> <td><a onclick="xxlb(' + team + ',\'bj\',\'s\')">' + json.result[key].info[key1].bj + '</a></td>';
                            }
                            html = html + ' </tr> </table>';
                            $("#list").append(html);
                        }

                        if (json.result[key].time == '下午') {
                            for (const key1 in json.result[key].info) {
                                let team = json.result[key].info[key1].team;
                                html = html + '<tr> <td rowspan="2">' + team + '队</td> <td>应到</td> <td>实到</td> <td>未到</td> <td>迟到</td> <td>事假</td> <td>病假</td> </tr> <tr> <td><a onclick="xxlb(' + team + ',\'yd\',\'x\')">' + json.result[key].info[key1].yd + '</a></td> <td><a onclick="xxlb(' + team + ',\'zc\',\'x\')">' + json.result[key].info[key1].zc + '</a></td> <td><a onclick="xxlb(' + team + ',\'wd\',\'x\')">' + json.result[key].info[key1].wd + '</a></td> <td><a onclick="xxlb(' + team + ',\'cd\',\'x\')">' + json.result[key].info[key1].cd + '</a></td> <td><a onclick="xxlb(' + team + ',\'sj\',\'x\')">' + json.result[key].info[key1].sj + '</a></td> <td><a onclick="xxlb(' + team + ',\'bj\',\'x\')">' + json.result[key].info[key1].bj + '</a></td>';
                            }
                            html = html + ' </tr> </table>';
                            $("#list").append(html);
                        }

                        if (json.result[key].time == '晚上') {
                            for (const key1 in json.result[key].info) {
                                let team = json.result[key].info[key1].team;
                                html = html + '<tr> <td rowspan="2">' + team + '队</td> <td>应到</td> <td>实到</td> <td>未到</td> <td>迟到</td> <td>事假</td> <td>病假</td> </tr> <tr> <td><a onclick="xxlb(' + team + ',\'yd\',\'w\')">' + json.result[key].info[key1].yd + '</a></td> <td><a onclick="xxlb(' + team + ',\'zc\',\'w\')">' + json.result[key].info[key1].zc + '</a></td> <td><a onclick="xxlb(' + team + ',\'wd\',\'w\')">' + json.result[key].info[key1].wd + '</a></td> <td><a onclick="xxlb(' + team + ',\'cd\',\'w\')">' + json.result[key].info[key1].cd + '</a></td> <td><a onclick="xxlb(' + team + ',\'sj\',\'w\')">' + json.result[key].info[key1].sj + '</a></td> <td><a onclick="xxlb(' + team + ',\'bj\',\'w\')">' + json.result[key].info[key1].bj + '</a></td>';
                            }
                            html = html + ' </tr> </table>';
                            $("#list").append(html);
                        }
                    }
                    break;
                default:
                    alert(json.message);
                    break;
            }
        });

    });

    $("#sushe").click(function () {
        $("#sstab").html('');
        $("#sshe").show();
        $("#kq").hide();
        $("#tool").hide();
        $(this).addClass("select").siblings().removeClass("select");

        $.get("api/?type=query&query_type=admin&admin_type=dormrq", function (data) {
            var json = $.parseJSON(data);
            switch (json.code) {
                case 100:
                    var html = '<option value="-">---请选择---</option>';
                    for (const key in json.result) {
                        html = html + '<option value="'+json.result[key]+'">'+json.result[key]+'</option>';
                    }
                    $("#ssheriqi").html(html);
                    break;
                default:
                    alert(json.message);
                    break;
            }
        });

    });

    $("#ssbtn").click(function () {
        var time = $("#ssheriqi option:selected").val();
        if (time == '-') {
            alert('请选择日期');
        } else {
            $.get("api/?type=query&query_type=admin&admin_type=dorm&time=" + time, function (data) {
                var json = $.parseJSON(data);
                switch (json.code) {
                    case 100:
                        var html = '<table> <tr> <td>队伍全称</td> <td>学号</td> <td>姓名</td> <td>宿舍</td> <td>情况</td> <td>操作人</td> </tr> ';
                        for (const key in json.result) {
                            let team = json.result[key][0];
                            let user = json.result[key][1];
                            let name = json.result[key][2];
                            let dorm = json.result[key][3];
                            let cqqk = json.result[key][4];
                            let cqczr = json.result[key][5];
                            html = html + '<tr> <td>' + team + '队</td> <td>' + user + '</td> <td>' + name + '</td> <td>' + dorm + '</td> <td>' + cqqk + '</td> <td>' + cqczr + '</td> </tr>';
                        }
                        html = html + ' </table>';
                        $("#sstab").html(html);
                        break;
                    default:
                        alert(json.message);
                        break;
                }
            });
        }
    });

    $(".close").click(function () {
        $(".showinfo").hide();
    });

    $("#mrkq_dow").click(function () {
        var team = $("#teamlist option:selected").val();
        $.get("api/?type=oper&oper_type=mrkq&mrkq_team=" + team, function (data) {
            var json = $.parseJSON(data);
            switch (json.code) {
                case 100:
                    window.open(json.result);
                    break;
                default:
                    alert(json.message);
                    break;
            }
        });
    });

    $("#jxhz_dow").click(function () {
        if ($("#downnamelist option:selected").val() == '-') {
            alert('请选择成员！');
        } else {
            var user = $("#downnamelist option:selected").val();
            $.get("api/?type=oper&oper_type=huizong&huizong_user=" + user, function (data) {
                var json = $.parseJSON(data);
                switch (json.code) {
                    case 100:
                        window.open(json.result);
                        break;
                    default:
                        alert(json.message);
                        break;
                }
            });
        }

    });

    $("#kqrq").change(function () {
        $("#zong").html('');
        $("#list").html('');
        $.get("api/?type=query&query_type=admin&admin_type=all&riqi="+$("#kqrq option:selected").val(), function (data) {
            var json = $.parseJSON(data);
            switch (json.code) {
                case 100:
                    var html = '';
                    $("#zong").html('');
                    for (const key in json.result) {
                        html = '<table> <tr> <td colspan="7"><strong>' + json.result[key].time + '</strong></td> </tr> <tr> ';
    
                        if (json.result[key].time == '早上') {
                            html = html + '<td>应到</td> <td>实到</td> <td>未到</td> <td>迟到</td> <td>事假</td> <td>病假</td> </tr> <tr> <td><a onclick="xxlb(\'all\',\'yd\',\'z\')">' + json.result[key].yd + '</a></td> <td><a onclick="xxlb(\'all\',\'zc\',\'z\')">' + json.result[key].zc + '</a></td> <td><a onclick="xxlb(\'all\',\'wd\',\'z\')">' + json.result[key].wd + '</a></td> <td><a onclick="xxlb(\'all\',\'cd\',\'z\')">' + json.result[key].cd + '</a></td> <td><a onclick="xxlb(\'all\',\'sj\',\'z\')">' + json.result[key].sj + '</a></td> <td><a onclick="xxlb(\'all\',\'bj\',\'z\')">' + json.result[key].bj + '</a></td> </tr> </table>';
                            $("#zong").append(html);
                        }
    
                        if (json.result[key].time == '上午') {
                            html = html + '<td>应到</td> <td>实到</td> <td>未到</td> <td>迟到</td> <td>事假</td> <td>病假</td> </tr> <tr> <td><a onclick="xxlb(\'all\',\'yd\',\'s\')">' + json.result[key].yd + '</a></td> <td><a onclick="xxlb(\'all\',\'zc\',\'s\')">' + json.result[key].zc + '</a></td> <td><a onclick="xxlb(\'all\',\'wd\',\'s\')">' + json.result[key].wd + '</a></td> <td><a onclick="xxlb(\'all\',\'cd\',\'s\')">' + json.result[key].cd + '</a></td> <td><a onclick="xxlb(\'all\',\'sj\',\'s\')">' + json.result[key].sj + '</a></td> <td><a onclick="xxlb(\'all\',\'bj\',\'s\')">' + json.result[key].bj + '</a></td> </tr> </table>';
                            $("#zong").append(html);
                        }
    
                        if (json.result[key].time == '下午') {
                            html = html + '<td>应到</td> <td>实到</td> <td>未到</td> <td>迟到</td> <td>事假</td> <td>病假</td> </tr> <tr> <td><a onclick="xxlb(\'all\',\'yd\',\'x\')">' + json.result[key].yd + '</a></td> <td><a onclick="xxlb(\'all\',\'zc\',\'x\')">' + json.result[key].zc + '</a></td> <td><a onclick="xxlb(\'all\',\'wd\',\'x\')">' + json.result[key].wd + '</a></td> <td><a onclick="xxlb(\'all\',\'cd\',\'x\')">' + json.result[key].cd + '</a></td> <td><a onclick="xxlb(\'all\',\'sj\',\'x\')">' + json.result[key].sj + '</a></td> <td><a onclick="xxlb(\'all\',\'bj\',\'x\')">' + json.result[key].bj + '</a></td> </tr> </table>';
                            $("#zong").append(html);
                        }
    
                        if (json.result[key].time == '晚上') {
                            html = html + '<td>应到</td> <td>实到</td> <td>未到</td> <td>迟到</td> <td>事假</td> <td>病假</td> </tr> <tr> <td><a onclick="xxlb(\'all\',\'yd\',\'w\')">' + json.result[key].yd + '</a></td> <td><a onclick="xxlb(\'all\',\'zc\',\'w\')">' + json.result[key].zc + '</a></td> <td><a onclick="xxlb(\'all\',\'wd\',\'w\')">' + json.result[key].wd + '</a></td> <td><a onclick="xxlb(\'all\',\'cd\',\'w\')">' + json.result[key].cd + '</a></td> <td><a onclick="xxlb(\'all\',\'sj\',\'w\')">' + json.result[key].sj + '</a></td> <td><a onclick="xxlb(\'all\',\'bj\',\'w\')">' + json.result[key].bj + '</a></td> </tr> </table>';
                            $("#zong").append(html);
                        }
                        
                    }
                    break;
                default:
                    alert(json.message);
                    break;
            }
        });
    
        $.get("api/?type=query&query_type=admin&admin_type=list&riqi="+$("#kqrq option:selected").val(), function (data) {
            $("#list").html('');
            var json = $.parseJSON(data);
            switch (json.code) {
                case 100:
                    var html = '';
                    for (const key in json.result) {
                        html = '<table> <tr> <td colspan="7"><strong>' + json.result[key].time + '</strong></td> </tr> ';
    
                        if (json.result[key].time == '早上') {
                            for (const key1 in json.result[key].info) {
                                let team = json.result[key].info[key1].team;
                                html = html + '<tr> <td rowspan="2">' + team + '队</td> <td>应到</td> <td>实到</td> <td>未到</td> <td>迟到</td> <td>事假</td> <td>病假</td> </tr> <tr> <td><a onclick="xxlb(' + team + ',\'yd\',\'z\')">' + json.result[key].info[key1].yd + '</a></td> <td><a onclick="xxlb(' + team + ',\'zc\',\'z\')">' + json.result[key].info[key1].zc + '</a></td> <td><a onclick="xxlb(' + team + ',\'wd\',\'z\')">' + json.result[key].info[key1].wd + '</a></td> <td><a onclick="xxlb(' + team + ',\'cd\',\'z\')">' + json.result[key].info[key1].cd + '</a></td> <td><a onclick="xxlb(' + team + ',\'sj\',\'z\')">' + json.result[key].info[key1].sj + '</a></td> <td><a onclick="xxlb(' + team + ',\'bj\',\'z\')">' + json.result[key].info[key1].bj + '</a></td>';
                            }
                            html = html + ' </tr> </table>';
                            $("#list").append(html);
                        }
    
                        if (json.result[key].time == '上午') {
                            for (const key1 in json.result[key].info) {
                                let team = json.result[key].info[key1].team;
                                html = html + '<tr> <td rowspan="2">' + team + '队</td> <td>应到</td> <td>实到</td> <td>未到</td> <td>迟到</td> <td>事假</td> <td>病假</td> </tr> <tr> <td><a onclick="xxlb(' + team + ',\'yd\',\'s\')">' + json.result[key].info[key1].yd + '</a></td> <td><a onclick="xxlb(' + team + ',\'zc\',\'s\')">' + json.result[key].info[key1].zc + '</a></td> <td><a onclick="xxlb(' + team + ',\'wd\',\'s\')">' + json.result[key].info[key1].wd + '</a></td> <td><a onclick="xxlb(' + team + ',\'cd\',\'s\')">' + json.result[key].info[key1].cd + '</a></td> <td><a onclick="xxlb(' + team + ',\'sj\',\'s\')">' + json.result[key].info[key1].sj + '</a></td> <td><a onclick="xxlb(' + team + ',\'bj\',\'s\')">' + json.result[key].info[key1].bj + '</a></td>';
                            }
                            html = html + ' </tr> </table>';
                            $("#list").append(html);
                        }
    
                        if (json.result[key].time == '下午') {
                            for (const key1 in json.result[key].info) {
                                let team = json.result[key].info[key1].team;
                                html = html + '<tr> <td rowspan="2">' + team + '队</td> <td>应到</td> <td>实到</td> <td>未到</td> <td>迟到</td> <td>事假</td> <td>病假</td> </tr> <tr> <td><a onclick="xxlb(' + team + ',\'yd\',\'x\')">' + json.result[key].info[key1].yd + '</a></td> <td><a onclick="xxlb(' + team + ',\'zc\',\'x\')">' + json.result[key].info[key1].zc + '</a></td> <td><a onclick="xxlb(' + team + ',\'wd\',\'x\')">' + json.result[key].info[key1].wd + '</a></td> <td><a onclick="xxlb(' + team + ',\'cd\',\'x\')">' + json.result[key].info[key1].cd + '</a></td> <td><a onclick="xxlb(' + team + ',\'sj\',\'x\')">' + json.result[key].info[key1].sj + '</a></td> <td><a onclick="xxlb(' + team + ',\'bj\',\'x\')">' + json.result[key].info[key1].bj + '</a></td>';
                            }
                            html = html + ' </tr> </table>';
                            $("#list").append(html);
                        }
    
                        if (json.result[key].time == '晚上') {
                            for (const key1 in json.result[key].info) {
                                let team = json.result[key].info[key1].team;
                                html = html + '<tr> <td rowspan="2">' + team + '队</td> <td>应到</td> <td>实到</td> <td>未到</td> <td>迟到</td> <td>事假</td> <td>病假</td> </tr> <tr> <td><a onclick="xxlb(' + team + ',\'yd\',\'w\')">' + json.result[key].info[key1].yd + '</a></td> <td><a onclick="xxlb(' + team + ',\'zc\',\'w\')">' + json.result[key].info[key1].zc + '</a></td> <td><a onclick="xxlb(' + team + ',\'wd\',\'w\')">' + json.result[key].info[key1].wd + '</a></td> <td><a onclick="xxlb(' + team + ',\'cd\',\'w\')">' + json.result[key].info[key1].cd + '</a></td> <td><a onclick="xxlb(' + team + ',\'sj\',\'w\')">' + json.result[key].info[key1].sj + '</a></td> <td><a onclick="xxlb(' + team + ',\'bj\',\'w\')">' + json.result[key].info[key1].bj + '</a></td>';
                            }
                            html = html + ' </tr> </table>';
                            $("#list").append(html);
                        }
                    }
                    break;
                default:
                    alert(json.message);
                    break;
            }
        });
    
    });
});