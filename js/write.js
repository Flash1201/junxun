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

    $("#cx").click(function () {
        var user = $("#zh").val();
        $.get("api/?type=query&query_type=admin&admin_type=people_info&people_info_type=r&user=" + user, function (data) {
            var json = $.parseJSON(data);
            switch (json.code) {
                case 100:
                    $("#user").val(json.result.user);
                    $("#name").val(json.result.name);
                    $("#sex").val(json.result.sex);
                    $("#team").val(json.result.team);
                    $("#zy").val(json.result.zy);
                    $("#sfzh").val(json.result.sfzh);
                    $("#dh").val(json.result.dh);
                    $("#dorm").val(json.result.dorm);
                    $("#qw").val(json.result.qw);
                    $("#jtzz").val(json.result.jtzz);
                    $("#tel1").val(json.result.tel1);
                    $("#tel2").val(json.result.tel2);
                    $("#tc").val(json.result.tc);

                    $("#user").attr("disabled", true);
                    $("#name").attr("disabled", true);
                    $("#sex").attr("disabled", true);
                    $("#team").attr("disabled", true);
                    $("#zy").attr("disabled", true);
                    $("#sfzh").attr("disabled", true);
                    $("#dh").attr("disabled", true);
                    $("#dorm").attr("disabled", true);
                    $("#qw").attr("disabled", true);
                    $("#jtzz").attr("disabled", true);
                    $("#tel1").attr("disabled", true);
                    $("#tel2").attr("disabled", true);
                    $("#tc").attr("disabled", true);
                    $("#btn").html('修改');
                    $(".info").show();
                    break;
                default:
                    alert(json.message);
                    break;
            }
        });
    });

    $("#btn").click(function () {
        var zt = $("#btn").html();
        if (zt == '修改') {
            $("#name").removeAttr("disabled");
            $("#sex").removeAttr("disabled");
            $("#team").removeAttr("disabled");
            $("#zy").removeAttr("disabled");
            $("#sfzh").removeAttr("disabled");
            $("#dh").removeAttr("disabled");
            $("#dorm").removeAttr("disabled");
            $("#qw").removeAttr("disabled");
            $("#jtzz").removeAttr("disabled");
            $("#tel1").removeAttr("disabled");
            $("#tel2").removeAttr("disabled");
            $("#tc").removeAttr("disabled");
            $("#btn").html('确认');
        }

        if (zt == '确认') {
            $("#name").attr("disabled", true);
            $("#sex").attr("disabled", true);
            $("#team").attr("disabled", true);
            $("#zy").attr("disabled", true);
            $("#sfzh").attr("disabled", true);
            $("#dh").attr("disabled", true);
            $("#dorm").attr("disabled", true);
            $("#qw").attr("disabled", true);
            $("#jtzz").attr("disabled", true);
            $("#tel1").attr("disabled", true);
            $("#tel2").attr("disabled", true);
            $("#tc").attr("disabled", true);
            $("#btn").html('修改');

            var user = $("#user").val();
            var name = $("#name").val();
            var sex = $("#sex").val();
            var team = $("#team").val();
            var zy = $("#zy").val();
            var sfzh = $("#sfzh").val();
            var dh = $("#dh").val();
            var dorm = $("#dorm").val();
            var qw = $("#qw").val();
            var jtzz = $("#jtzz").val();
            var tel1 = $("#tel1").val();
            var tel2 = $("#tel2").val();
            var tc = $("#tc").val();

            $.post("api/?type=query&query_type=admin&admin_type=people_info&people_info_type=w", {
                user: user,
                name: name,
                sex: sex,
                team: team,
                zy: zy,
                sfzh: sfzh,
                dh: dh,
                dorm: dorm,
                qw: qw,
                jtzz: jtzz,
                tel1: tel1,
                tel2: tel2,
                tc: tc
            }, function (data) {
                var json = $.parseJSON(data);
                switch (json['code']) {
                    case 100:
                        $(".info").hide();
                        alert(json['message']);
                        break;
                    default:
                        alert(json.message);
                        break;
                }
            });
        }
    });

    $("#del").click(function () {
        var user = $("#zh").val();
        if (confirm("确定要删除吗？")) {
            $.get("api/?type=query&query_type=admin&admin_type=people_info&people_info_type=d&user=" + user, function (data) {
                var json = $.parseJSON(data);
                switch (json['code']) {
                    case 100:
                        alert(json.message);
                        break;
                    default:
                        alert(json.message);
                        break;
                }
            });
        }
    });
});