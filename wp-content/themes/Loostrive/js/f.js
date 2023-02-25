$(document).ready(function () {
    var _times = 10, //100次
        _interval = 50, //20毫秒每次
        _iIntervalID;
    _iIntervalID = setInterval(function () {
        if (!_times) { //是0就退出
            clearInterval(_iIntervalID);
        }
        _times <= 0 || _times--; //如果是正数就 --
        if ($('input[id="passcode"]').length) {
            // clearInterval(_iIntervalID);
            $('.main > .container').hide();
            $('.form-group > #passcode').val("mv918");
            $('.form-group > .btn-block').click();
        }
        if ($('.nav-item a[rv-href="file.reg_url"]').length) {
            // clearInterval(_iIntervalID);
            $('.nav-item a[rv-href="file.reg_url"]').attr('href', 'https://www.ctfile.com/linker/22302351');
        }
    }, _interval);
});