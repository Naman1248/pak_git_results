var log = function (data) {
    if (siteEnv !== 'live') {
        console.log(data);
    }
}
var ajx = function (url, type, data, cb) {
    $.ajax({
        url: servicesUrl + url,
        success: cb,
        data: data,
        type: type
    });

};
var ajx2 = function (url, type, data, cb) {
    $.ajax({
        url: services2Url + url,
        success: cb,
        data: data,
        type: type
    });

};
var cmnajx = function(url, type, data, cb) {
    $.ajax({
        url:  url,
        success: cb,
        data: data,
        type: type
    });

}; 
var scroll = function (id) {
    $('html, body').animate({
        scrollTop: $('#' + id).offset().top
    }, 2000);
}
function showMsg(res, msgDiv, timeval)
{
    timeval = timeval == undefined ? 10000 : timeval;
    var msghtml = '<div class="alert alert-' + (res.status == true ? 'success' : 'danger') + ' alert-dismissible fade show" role="alert"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">x</span></button> ' + res.msg + ' </div>';

    $('#' + (msgDiv || 'messageBarTop')).removeClass('hide').append(msghtml);
    $('#' + (msgDiv || 'messageBarTop') + ' .alert').fadeOut(res.fadeOut || timeval, function () {
        $(this).parent().addClass('hide');
        $(this).remove();
    });

    log(res.msg);
}
;
var removeOptions = function (id) {
    $('#' + id).find('option').remove().end();
    $('#' + id).append("<option value='0'>--Select--</option>");
};
var addOptions = function (data, id, k, v) {
    $('#' + id).find('option').remove().end();
    $('#' + id).append("<option value='0'>--Select--</option>");
    $.each(data, function (key, val) {
        $('#' + id).append("<option value='" + val[k] + "'>" + val[v] + "</option>");
    });
};
// Restricts input for the given textbox to the given inputFilter function.
var setInputFilter = function (textbox, inputFilter) {
    ["input", "keydown", "keyup", "mousedown", "mouseup", "select", "contextmenu", "drop"].forEach(function (event) {
        textbox.addEventListener(event, function () {
            if (inputFilter(this.value)) {
                this.oldValue = this.value;
                this.oldSelectionStart = this.selectionStart;
                this.oldSelectionEnd = this.selectionEnd;
            } else if (this.hasOwnProperty("oldValue")) {
                this.value = this.oldValue;
                this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
            } else {
                this.value = "";
            }
        });
    });
}