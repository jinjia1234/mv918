$(document).ready(function () {
    // $(document).on('click', '.download-link', function () {
    //     var action = $(this).parent().attr("class");
    //     var postid = $(this).attr('id');
    //     var title = $(this).attr('title');
    //     $.ajax({
    //         type: 'POST',
    //         url: "?",
    //         dataType: 'html',
    //         data: {
    //             'download': action,
    //             'postid': postid,
    //             'title': title,
    //         },
    //         beforeSend: function () {
    //         },
    //         success: function (result) {
    //             window.location.href = result;
    //             // alert(result);
    //         },
    //         error: function (data) {
    //             var txt = '数据获取失败，请重试。'
    //         }
    //     });
    // });

    function torrent() {
        var a = $('#main a');
        var action = $('#main a').parent().attr("class");
        var title = $('#main a').attr('title');
        var success = false;
        $.ajax({
            type: 'POST',
            url: "?",
            dataType: 'json',
            data: {
                'action': 'ctwp_link',
                'download': action,
                'postid': $('#main a').attr('id'),
                'title': title,
            },
            timeout: 8000, //设置超时的时间6s
            success: function (result) {
                if (result.state == 1) {
                    // msg = result.password != undefined ? ' (解压密码:' + result.password + ')' : '';
                    msg = ' (密码:mv918)';
                    // $('.ctwp').html('&nbsp;&nbsp;<a href="' + result.url + '" target="_blank">torrent文件下载' + msg + '</a>');
                    $('.download-link').attr('href', result.url);
                    $('.download-link').attr('target', '_blank');
                    $('.download-link').html('torrent文件下载' + msg);
                    $('.download-link').off('click');
                    success = true;
                    // setPassword(result.url);
                    // } else if (result.state == 2) {
                    //     eval(result.script);
                    //     console.log(jschl_answer());
                    // torrent();
                } else {
                    if (a.data('value')) {
                        $('.download-link').attr('href', window.atob(a.data('value')));
                        $('.download-link').html('magnet磁力链接');
                        $('.download-link').removeClass('download-link');
                    }
                }
            },
            complete: function (XMLHttpRequest, textStatus) {
                if (!success) {
                    if (a.data('value')) {
                        $('.download-link').attr('href', window.atob(a.data('value')));
                        $('.download-link').html('magnet磁力链接');
                        $('.download-link').removeClass('download-link');
                    }
                }
            },
        });
    }

    function setPassword(url) {
        var ids = url.split('/').pop().split('-');
        $.ajax({
            type: 'GET',
            url: "https://webapi.ctfile.com/passcode.php?file_id=" + ids[1] + "&folder_id=0&userid=" + ids[0] + "&passcode=mv918&r=" + Math.random(),
            xhrFields: {
                withCredentials: true
            },
            crossDomain: true,
            dataType: 'json',
            success: function (data) {
                console.log(data);
            }
        });
    }

    torrent();

    $(document).on('click', '.download-link', function () {
        var password = $(this).data('password');
        if (!password) {
            var url = $(this).attr('href');
            var ids = url.split('/').pop().split('-');
            $.ajax({
                type: 'GET',
                url: "https://webapi.ctfile.com/passcode.php?file_id=" + ids[1] + "&folder_id=0&userid=" + ids[0] + "&passcode=mv918&r=" + Math.random(),
                xhrFields: {
                    withCredentials: true
                },
                crossDomain: true,
                dataType: 'json',
                success: function (data) {
                    $(this).attr('data-password', 'mv918');
                    console.log(data);
                }
            });
        }

    });
});