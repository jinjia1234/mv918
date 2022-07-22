$(document).ready(function () {
    $(document).on('click', '.pass', function () {
        var that = $(this);
        var password = $(this).data('password');
        if (!password) {
            var url = $(this).attr('href');
            var ids = url.split('/').pop().split('-');
            if (ids.length == 3) {
                $.ajax({
                    type: 'GET',
                    url: "https://webapi.ctfile.com/passcode.php?file_id=" + ids[1] + "&folder_id=0&userid=" + ids[0] + "&passcode=mv918&r=" + Math.random(),
                    xhrFields: {
                        withCredentials: true
                    },
                    crossDomain: true,
                    dataType: 'json',
                    success: function (data) {
                        that.attr('data-password', 'mv918');
                    }
                });
            }
        }
    });
});