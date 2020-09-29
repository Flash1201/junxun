function getinfo(index) {
    window.location.href = "api/?type=page&page_type=kaoqin&teamid=" + index;
}

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

    $.get("api/?type=query&query_type=team", function (data) {
        var json = $.parseJSON(data);
        switch (json.code) {
            case 100:
                for (const key in json.result) {
                    let html = '<button onclick="getinfo(' + json.result[key] + ')">' + json.result[key] + '&nbsp;队</button>';
                    $(".btn").append(html);
                }
                break;
            default:
                alert(json.message);
                break;
        }
    });
});