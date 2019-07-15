/*
导航
 */
function clickMenu(obj) {
    var li = $(obj).parent();
    var a = li.find('a');
    li.find('.activity').removeClass('activity');
    if (a.attr('_href') == undefined) {
        if (li.hasClass('activity')) {
            li.removeClass('activity');
        } else {
            if($(obj).parent().hasClass('layout-nav-list-item')){
                $('.layout-nav-list-item').removeClass('activity');
            }
            li.addClass('activity');
        }
    } else {
        var refresh = a.attr('_refresh');
        if (refresh == undefined) {
            refresh = 0;
        }
        openTab(a.find('cite').html(), a.attr('_href'), refresh);
    }
}

//标签页刷新
function tabPageRefresh() {
    var iframe = $('.layout-body-item-show').find('iframe').get(0);
    iframe.contentWindow.location.reload(true);
}

function clickTab(obj) {
    var title = $(obj).find('cite').html();
    if ($(obj).hasClass('layout-pagetabs-home')) {
        title = '首页';
    }
    var url = $(obj).attr('_href');
    var refresh = $(obj).attr('_refresh');
    if (refresh == undefined) {
        refresh = 0;
    }
    openTab(title, url, refresh);
}

function closeTab(obj) {
    var tab = $(obj).parents('.layout-pagetabs-item');
    if (tab.hasClass('activity')) {
        //当前页
        var nextTab = tab.next();
        if (nextTab.length > 0) {
            //显示下一个标签页
            var refresh = nextTab.attr('_refresh');
            if (refresh == undefined) {
                refresh = 0;
            }
            openTab(nextTab.find('cite').html(), nextTab.attr('_href'), refresh);
        } else {
            var prevTab = tab.prev();
            var title = prevTab.find('cite').html();
            if (prevTab.hasClass('layout-pagetabs-home')) {
                title = '首页';
            }
            var refresh = prevTab.attr('_refresh');
            if (refresh == undefined) {
                refresh = 0;
            }
            openTab(title, prevTab.attr('_href'), refresh);
        }
    }
    //关闭页面
    var url = tab.attr('_href');
    $('.layout-body').find('.layout-body-item[_href="' + url + '"]').remove();
    tab.remove();
    setPageTabsWidth();
}

function closeAllTab() {
    $('.layout-body').find('.layout-body-item').not('.layout-body-home').remove();
    $('#pagetabs').find('.layout-pagetabs-item').not('.layout-pagetabs-home').remove();
    $('.layout-body').find('.layout-body-home').addClass('layout-body-item-show');
    $('#pagetabs').find('.layout-pagetabs-home').addClass('activity');
    $("#pagetabs").animate({left: '0px'});
    setPageTabsWidth();
}

//打开标签页
function openTab(title, url, is_refresh) {
    is_refresh=is_refresh||0;
    if (title == '' || title == undefined) {
        console.log('title不能为空');
        return;
    }
    if (url == '' || url == undefined) {
        console.log('url不能为空');
        return;
    }
    var tabsArr = $('#pagetabs .layout-pagetabs-item');
    var tab = $('#pagetabs .layout-pagetabs-item[_href="' + url + '"]').get(0);
    $('#pagetabs .layout-pagetabs-item').removeClass('activity');
    $('.layout-body-item').removeClass('layout-body-item-show');

    //判断标签在列表中的位置
    var position = calculateLocation(tabsArr, tab);
    if (position.index < 0) {
        //新页面
        var newTab = '<div class="layout-pagetabs-item activity" _href="' + url + '">\n' +
            '           <cite title="' + title + '">' + title + '</cite>\n' +
            '           <i class="rhly-iconfont guanbi" onclick="closeTab(this)"></i>\n' +
            '       </div>';
        var newBody = '<div class="layout-body-item layout-body-item-show" _href="' + url + '">\n' +
            '            <iframe frameborder="0" src="' + url + '" class="layout-body-iframe" width="100%" height="100%"></iframe>\n' +
            '        </div>';
        $('#pagetabs').append(newTab);
        $('.layout-body').append(newBody);
        setPageTabsWidth();
    } else {
        //页面存在
        //显示页面
        $(tab).addClass('activity');
        $('.layout-body-item[_href="' + url + '"]').addClass('layout-body-item-show');
        if (is_refresh == 1) {
            var iframe = $('.layout-body-item[_href="' + url + '"]').find('iframe').get(0);
            iframe.contentWindow.location.reload(true);
        }
    }
    //移动标签栏
    $("#pagetabs").animate({left: position.left + 'px'}, "fast");
}

//计算标签页位置
function calculateLocation(tabsArr, tab) {
    var position = {'index': -1, left: 0};
    // var listWidth=$('#pagetabs').width();
    tabsArr.each(function (i) {
        if (tabsArr[i] == tab) {
            position.index = i;
            return false;
        }
    });
    if (position.index < 0) {
        //页面不存在，插入到最后
        var left = tabsArr.length * tabWidth + homeTab;
        if (left > pagetabWidth) {
            position.left += 0 - (left - pagetabWidth);
        }
    } else if (position.index == 0) {
        //首页
    } else {
        //页面存在，判断标签位置
        var listPosition = $('#pagetabs').position();
        var listPositionLeft = Math.abs(listPosition.left);
        var tabLocation = (position.index) * tabWidth + homeTab;
        if (tabLocation < listPositionLeft) {
            //标签位于左侧，被遮挡，需要往左移动
            position.left = 0 - (tabLocation - tabWidth);
        } else if (tabLocation - listPositionLeft < tabWidth) {
            //临界值，标签位于左侧边缘
            position.left = 0 - (tabLocation - tabWidth);
        } else if (tabLocation < pagetabWidth + listPositionLeft) {
            //标签位于中间，不需要移动
            position.left = 0 - listPositionLeft;
        } else if (tabLocation - pagetabWidth - listPositionLeft < tabWidth) {
            //临界值，标签位于右侧边缘
            position.left = 0 - (tabLocation - pagetabWidth);
        } else {
            //标签位于右侧，被遮挡，需要往右移动
            position.left = 0 - (tabLocation - pagetabWidth);
        }
    }
    return position;
}

/*
顶部导航栏切换
 */
function topTabClick(obj) {
    var li = $(obj);
    var href = li.attr('_href');
    $.get(href, function (data) {
        $('.layout-header-top-item').removeClass('activity');
        li.addClass('activity');
        $('#layout-nav-list').html(data);
        $('#layout-nav-list a').click(function () {
            clickMenu(this);
        });
    })
}

/*
用户菜单
 */
function showAdminList() {
    $('.admin-list').show();
}

function hideAdminList() {
    $('.admin-list').hide();
}

/*
标签栏移动
 */
function pageTabsListPrev() {
    if (prevFlag == 1) {
        return false;
    }
    prevFlag = 1;
    var position = $('#pagetabs').position();
    var left = position.left + 400;
    if (left >= 0) {
        left = 0;
    }
    $("#pagetabs").animate({left: left + 'px'}, "fast");
    prevFlag = 0;
}

function pageTabsListNext() {
    if (nextFlag == 1) {
        return false;
    }
    nextFlag = 1;
    var position = $('#pagetabs').position();
    var tabsWidth = ($('#pagetabs .layout-pagetabs-item').length - 1) * tabWidth + homeTab;
    if (tabsWidth >= (pagetabWidth + Math.abs(position.left))) {
        //当标签栏长度减去初始长度，大于或等于左位移，可以继续往右移动
        var right = position.left - 400;
        if (Math.abs(right) < tabsWidth) {
            $("#pagetabs").animate({left: right + 'px'}, "fast");
        } else {
            var right = 0 - (tabsWidth - pagetabWidth);
            $("#pagetabs").animate({left: right + 'px'}, "fast");
        }
    }
    nextFlag = 0;
}

function windowAddMouseWheel() {
    var scrollFunc = function (e) {
        if (mouseWheelFlag == 1) {
            return false;
        }
        mouseWheelFlag = 1;
        e = e || window.event;
        if (e.wheelDelta) {  //判断浏览器IE，谷歌滑轮事件
            if (e.wheelDelta > 0) { //当滑轮向上滚动时
                pageTabsListPrev();
            }
            if (e.wheelDelta < 0) { //当滑轮向下滚动时
                pageTabsListNext()
            }
        } else if (e.detail) {  //Firefox滑轮事件
            if (e.detail > 0) { //当滑轮向上滚动时
                pageTabsListPrev();
            }
            if (e.detail < 0) { //当滑轮向下滚动时
                pageTabsListNext();
            }
        }
        mouseWheelFlag = 0;
    };
    //给页面绑定滑轮滚动事件
    if (document.getElementById('layout-pagetabs').addEventListener) {
        document.getElementById('layout-pagetabs').addEventListener('DOMMouseScroll', scrollFunc, false);
    }
//滚动滑轮触发scrollFunc方法
    document.getElementById('layout-pagetabs').onmousewheel = document.getElementById('layout-pagetabs').onmousewheel = scrollFunc;
}

function setPageTabsWidth() {
    var width = ($('#pagetabs .layout-pagetabs-item').length - 1) * tabWidth + homeTab;
    $('#pagetabs').width(width);
}

/*
绑定事件
 */
var pagetabWidth = 0;//标签栏宽度
var tabWidth = 167;//标签宽度
var homeTab = 47;//主页标签宽度
var prevFlag = 0;
var nextFlag = 0;
// var mouseWheelFlag=0;
$(function () {
    //获取首页地址并绑定
    var url = $('.layout-nav-home a').attr('_href');
    $('.layout-pagetabs-home').attr('_href', url).addClass('activity');
    $('.layout-body-home').attr('_href', url).addClass('layout-body-item-show');
    $('.layout-body-item-show iframe').attr('src', url);

    topTabClick($('.layout-header-top-list .activity').get(0));
    // console.log($('.layout-header-top-list .activity'))

    //计算标签栏宽度
    //$('#prev').width();等于37
    //$('.layout-pagetabs-right').width();等于205
    pagetabWidth = $('.layout-pagetabs').width() - 37 - 205;

    //点击左侧菜单栏
    $('.layout-nav-list a').click(function () {
        clickMenu(this);
    });

    //点击标签事件
    $('#pagetabs').on('click', '.layout-pagetabs-item', function () {
        clickTab(this);
    });

    //页面标签栏左移和右移
    $('#prev').click(pageTabsListPrev);
    $('#next').click(pageTabsListNext);

    //管理员菜单显示与隐藏
    $('.welcome').mouseover(showAdminList);
    $('.welcome').mouseout(hideAdminList);

    //左侧菜单栏切换
    $('.layout-header-top-item').click(function () {
        topTabClick(this);
    });

    $(window).resize(function () {
        //$('#prev').width();等于37
        //$('.layout-pagetabs-right').width();等于205
        pagetabWidth = $('.layout-pagetabs').width() - 37 - 205;
    });
    setPageTabsWidth();
    // windowAddMouseWheel();
});

function openWindow(url, title, width, height) {
    title=title||"新窗口";
    width=width||"500px";
    height=height||"360px";
    var index = layui.layer.open({
        title: [title,'background:#fff'],
        type: 2,
        area: [width, height],
        content: url,
        shadeClose: true,
        maxmin: true,
        success: function (layero, index) {
        }
    });
}
