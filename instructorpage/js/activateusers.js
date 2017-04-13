/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
function createErrorMessageHTML(errorMessage) {
    return '<div class="alert alert-danger">\n\
<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>\n\
' + errorMessage + '</div> ';
}
function createSuccessMessageHTML(successMessage) {
    return '<div class="alert alert-success">\n\
<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>\n\
' + successMessage + '</div> ';
}
$(document).ready(function () {

    if (errorObj['errors_dict']) {
        var errorObject = errorObj['errors_dict'];

        var errorGlyph = '<i class="fa fa-exclamation-triangle" aria-hidden="true" style="vertical-align:middle"></i>  ';

        if (errorObject['actSystem'] == 1) {
            $("#msgBox").html(createErrorMessageHTML('System Error. Please contact administrator.'));
        }
        if (errorObject['valUserId'] == 1) {
            $("#msgBox").html(createErrorMessageHTML('Invalid userid for activation!'));
        }
        if (errorObject['valUserActive'] == 1) {
            $("#msgBox").html(createErrorMessageHTML('Select proper activation action.'));
        }
        if (errorObject['actUserSuccess'] == 0) {
            $("#msgBox").html(createSuccessMessageHTML('Operation completed successfully.'));
        }

    }
    $("tr[_id]").on('click', function (e) {
        $("tr.selectedRow").removeClass('selectedRow');
        $(this).addClass('selectedRow');
    });

    $("#btnActivateUser").on('click', function (e) {
        var selectedRow = $("tr[_id].selectedRow");
        if (selectedRow.length > 0) {
            var toActUser = $(selectedRow).attr("_id");
            console.log(toActUser);
            $("#actUserId").val(toActUser);
            $("#actUserActivate").val(1);
            $("#btnActUser").click();
        } else {
            $("body").append('<div class="alert alert-warning autoClose" style="position:absolute; bottom:10px; right:10px; display:inline-block;">Please select user to activate.</div>');
            $("body .autoClose").fadeOut(4000, function () {
                $(this).remove();
            });
        }
    });
    $("#btnDeActivateUser").on('click', function (e) {
        var selectedRow = $("tr[_id].selectedRow");
        if (selectedRow.length > 0) {
            var toActUser = $(selectedRow).attr("_id");
            console.log(toActUser);
            $("#actUserId").val(toActUser);
            $("#actUserActivate").val(0);
            $("#btnActUser").click();
        } else {
            $("body").append('<div class="alert alert-warning autoClose" style="position:absolute; bottom:10px; right:10px; display:inline-block;">Please select user to de-activate.</div>');
            $("body .autoClose").fadeOut(4000, function () {
                $(this).remove();
            });
        }
    });
});

