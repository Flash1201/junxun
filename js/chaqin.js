$(document).ready(function () {
    $.get("api/?type=qxian", function (data) {
        var json = $.parseJSON(data);
        if (json.code != 100 && json.code != 101) {
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


    $.get("api/?type=query&query_type=dorm&dorm_type=L", function (data) {
        $("#dorm_L").html('');
        var json = $.parseJSON(data);
        switch (json.code) {
            case 100:
                var html = '<option value="-">---请选择---</option>';
                for (const key in json.result) {
                    html = html + '<option value="' + json.result[key] + '">' + json.result[key] + '</option>';
                }
                $("#dorm_L").html(html);
                break;
            default:
                alert(json.message);
                break;
        }
    });


    $("#dorm_L").change(function () {
        if ($("#dorm_L option:selected").val() != '-') {
            var dorm_L = $("#dorm_L option:selected").val();
            $.get("api/?type=query&query_type=dorm&dorm_type=S&dorm_L=" + dorm_L, function (data) {
                $("#dorm_S").html('');
                var json = $.parseJSON(data);
                switch (json.code) {
                    case 100:
                        var html = '<option value="-">---请选择---</option>';
                        for (const key in json.result) {
                            html = html + '<option value="' + json.result[key] + '">' + json.result[key] + '</option>';
                        }
                        $("#dorm_S").html(html);
                        break;
                    default:
                        alert(json.message);
                        break;
                }
            });
        }
    });

    $("#dorm_S").change(function () {
        if ($("#dorm_S option:selected").val() != '-') {
            var dorm_L = $("#dorm_L option:selected").val();
            var dorm_S = $("#dorm_S option:selected").val();
            $("#peoplist").html('');
            $.get("api/?type=query&query_type=dorm&dorm_type=R&dorm_L=" + dorm_L + "&dorm_S=" + dorm_S, function (data) {
                var json = $.parseJSON(data);
                switch (json.code) {
                    case 100:
                        var html = '';
                        for (const key in json.result) {
                            html = html + '<label> <input type="radio" name="people" value="' + json.result[key].user + '"> <span>' + json.result[key].name + '</span> </label>';
                        }
                        $("#peoplist").html(html);
                        break;
                    default:
                        alert(json.message);
                        break;
                }
            });
        }
    });

    $("#btn").click(function () {
        var user = $('input:radio[name="people"]:checked').val();
        var cqqk = $("#dorm_txt").val();
        var czr = $("#czr").val();
        console.log(user);
        console.log(cqqk);
        console.log(czr);
        if (user != undefined && cqqk != '' && czr != '') {
            $.get("api/?type=oper&oper_type=chaqin&chaqin_user=" + user + "&chaqin_qk=" + cqqk + "&chaqin_czr=" + czr, function (data) {
                var json = $.parseJSON(data);
                switch (json.code) {
                    case 100:
                        alert(json.message);
                        break;
                    default:
                        alert(json.message);
                        break;
                }
            });
        }else{
            alert('请认真选择');
        }
    });
});