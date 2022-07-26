$(document).ready(function () {
    var _times = 10, //100次
        _interval = 50, //20毫秒每次
        _iIntervalID;
    _iIntervalID = setInterval(function () {
        if (!_times) { //是0就退出
            clearInterval(_iIntervalID);
        }
        _times <= 0 || _times--; //如果是正数就 --
        // console.log(_times + '=>' + $('input[id="passcode"]').length);
        if ($('input[id="passcode"]').length) {
            clearInterval(_iIntervalID);
            $('.main > .container').hide();
            $('.form-group > #passcode').val("mv918");
            $('.form-group > .btn-block').click();
        }
    }, _interval);
});