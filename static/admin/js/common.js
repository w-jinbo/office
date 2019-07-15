function openTab(title,url,refresh){
    parent.openTab(title,url,refresh);
}

function popupShow(title, url, width, height){
    if (title == null || title == '') {
        title=false;
    }
    if (url == null || url == '') {
        url="404.html";
    }
    if (width == null || width == '') {
        width=($(window).width()*0.9);
    }
    if (height == null || height == '') {
        height=($(window).height() - 50);
    }
    layer.open({
        type: 2,
        area: [width+'px', height+'px'],
        fix: false, //不固定
        maxmin: true,
        shadeClose: true,
        shade:0.4,
        title: title,
        content: url
    });
}

function popupClose(){
    var index = parent.layer.getFrameIndex(window.name);
    parent.layer.close(index);
}


function msgOK(msg) {
    layer.msg(msg, {
        icon: 1,
        shade: [0.5, '#000'],
        shadeClose: true
    });
}

function msgFaild(msg) {
    layer.msg(msg, {
        icon: 2,
        shade: [0.5, '#000'],
        shadeClose: true
    });
}

function loading(msg) {
    if(!msg){
        msg="处理中...";
    }
    layer.msg(msg, {
        icon: 16,
        time: 0,
        shade: [0.5, '#000']
    });
}
/**
 * 跳转
 * @param {Object} url
 * @param {Object} time
 */
function hrefTo(url, time) {
    if (time == 0) {
        window.location.href = url;
    } else {
        setTimeout("hrefTo('" + url + "',0)", time);
    }
}
/**
 * 关闭弹窗
 */
function closeAll(){
    layer.closeAll();
}

$(function(){
    layui.use(['form', 'layer'], function () {
        var form = layui.form;
        form.render();
    });
});

