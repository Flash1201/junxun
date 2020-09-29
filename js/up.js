function cssformat(type = 0) {
    if (type == 0) {
        $("#upload").css({
            "border-color": "#222428",
        });
        $("#upload").html('点击上传，或将文件拖拽到这里<div id="percent"><div></div></div>');
    }
    if (type == 1) {
        $("#upload").css({
            "border-color": "#7289da",
        });
        $("#upload").html('松开鼠标<div id="percent"><div></div></div>');
    }
}


function upload(files) {
    if (files.length > 1) {
        alert('只能上传一个文件！');
    } else {
        if (files[0].type == '') {
            alert('不能上传文件夹！');
        } else {
            let file_name = files[0].name;
            let arr = file_name.split(".");
            let ext = arr.slice(-1);
            if (files[0].size > (20 * 1024 * 1024)) {
                alert('文件必须小于20M');
            } else {
                if (ext[0] != 'xlsx') {
                    alert('文件类型不对，请使用模板填写！');
                } else {
                    var formData = new FormData();
                    formData.append("file", files[0]);
                    $.ajax({
                        url: "api/?type=register",
                        type: "post",
                        async: true,
                        processData: false,
                        contentType: false,
                        data: formData,
                        xhr: function () {
                            var myXhr = $.ajaxSettings.xhr();
                            if (myXhr.upload) {
                                myXhr.upload.addEventListener('progress', function (e) {
                                    var loaded = e.loaded;
                                    var total = e.total;
                                    var percent = Math.floor(100 * loaded / total) + "%";
                                    $('#percent>div').css({
                                        "width": percent,
                                    });
                                }, false);
                            }
                            return myXhr;
                        },
                        success: function (data) {
                            var json = $.parseJSON(data);
                            switch (json['code']) {
                                case 100:
                                    alert(json['message']);
                                    window.location.replace("up.html");
                                    break;
                                default:
                                    alert(json['message']);
                                    window.location.replace("up.html");
                                    break;
                            }
                        },
                        error: function () {
                            alert("请重新选择文件进行上传！");
                            window.location.replace("register.html");
                        }
                    })
                }
            }
        }
    }
}

document.addEventListener('drop', function (e) {
    e.preventDefault()
}, false)
document.addEventListener('dragover', function (e) {
    e.preventDefault()
    e.stopPropagation();
}, false)

// 松开事件
var file = document.getElementById('upload')
file.addEventListener('drop', function (e) {
    e.preventDefault();
    e.stopPropagation();
    cssformat();
    upload(e.dataTransfer.files);
}, false);
// 拖到上方
file.addEventListener("dragover", function (e) {
    e.preventDefault();
    e.stopPropagation();
}, false);
// 拖进事件
file.addEventListener("dragenter", function (e) {
    e.preventDefault();
    e.stopPropagation();
    cssformat(1);
}, false);
// 离开事件
file.addEventListener("dragleave", function (e) {
    e.preventDefault();
    e.stopPropagation();
    cssformat();
}, false);

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


    $("body").on({
        change: function () {
            if ($("#file")[0].files.length == 0) {
                return;
            } else if ($("#file")[0].files.length == 1) {
                upload($("#file")[0].files);
                $("#file").val("");
            }
        }
    }, "#file");

    $(".main").on({
        click: function () {
            $("#file").click();
        }
    }, "#upload");
});