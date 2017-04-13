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
$(document).ready(function () {
    var cities = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        limit: 100,
        remote: {
            cache: true,
            url: 'city.php?query=%QUERY',
            wildcard: '%QUERY',
            filter: function (data) {
                return data;
            }
        },
        updater: function (item) {
            // do what you want with the item here
            return item;
        }
    });
    cities.initialize();
    $('#txtCityState').typeahead(null, {
        name: 'txtCityState',
        display: 'label',
        source: cities
    });

    $('#txtCityState').on('typeahead:select', function (ev, suggestion) {
        $("#txtCity").val(suggestion.value.city);
        $("#txtState").val(suggestion.value.state);
    });

    if (errorObj['errors_dict']) {
        var errorObject = errorObj['errors_dict'];
        if (errorObject['txtCourseName'] == 1 ||
            errorObject['dtCourseDate'] == 1 ||
            errorObject['sltIAMVersion'] == 1 ||
            errorObject['txtCity'] == 1 ||
            errorObject['txtState'] == 1) {
            $("#addCourseModal").modal();
        }
        var errorGlyph = '<i class="fa fa-exclamation-triangle" aria-hidden="true" style="vertical-align:middle"></i>  ';
        if (errorObject['txtCourseName'] == 1) {
            $("#txtCourseNameError").html(errorGlyph + 'Please enter course name.');
        }
        if (errorObject['txtOrganization'] == 1) {
            $("#txtOrganizationError").html(errorGlyph + 'Please enter organization.');
        }
        if (errorObject['dtCourseDate'] == 1) {
            $("#dtCourseDateError").html(errorGlyph + 'Please enter course date.');
        }
        if (errorObject['sltIAMVersion'] == 1) {
            $("#sltIAMVersionError").html(errorGlyph + 'Please enter course version.');
        }
        if (errorObject['txtCity'] == 1 || errorObject['txtState'] == 1) {
            $("#txtCityStateError").html(errorGlyph + 'Please enter course location.');
        }
        if (errorObject['system'] == 1) {
            $("#msgBox").html(createErrorMessageHTML('System Error. Please contact administrator.'));
        }
        if (errorObject['valNonEmptyCourse'] == 1) {
            $("#msgBox").html(createErrorMessageHTML('Cannot delete course. The course has students enrolled.'));
        }
        if (errorObject['txtDelCourseId'] == 1) {
            $("#msgBox").html(createErrorMessageHTML('Cannot delete course. Invalid or no course selected.'));
        }


    }
    $("[course-field]").on('change', function (event) {
        console.log($(event.target).siblings("span.alert-danger-text"));
        $(event.target).siblings("span.alert-danger-text").html('');
    });
    $("tr[_id]").on('click', function (e) {
        $("tr.selectedRow").removeClass('selectedRow');
        $(this).addClass('selectedRow');
    });
    $("#btnDeleteCourse").on('click', function (e) {

        bootbox.confirm({
            message: "Are you sure you want to delete the course?",
            buttons: {
                confirm: {
                    label: 'Yes',
                    className: 'btn-success'
                },
                cancel: {
                    label: 'No',
                    className: 'btn-danger'
                }
            },
            callback: function (result) {
                if (result) {
                    var selectedRow = $("tr[_id].selectedRow");
                    if (selectedRow.length > 0) {
                        var toDeleteId = $(selectedRow).attr("_id");
                        console.log(toDeleteId);
                        $("#txtDelCourseId").val(toDeleteId);
                        $("#btnSubmitDeleteCourse").click();
                    } else {
                        $("body").append('<div class="alert alert-warning autoClose" style="position:absolute; bottom:10px; right:10px; display:inline-block;">Please select course to remove.</div>');
                        $("body .autoClose").fadeOut(4000, function () {
                            $(this).remove();
                        });
                    }
                }
            }
        });

    });
    $("#btnManageStudents").on('click', function (e) {
        var selectedRow = $("tr[_id].selectedRow");
        if (selectedRow.length > 0) {
            var toManageStudents = $(selectedRow).attr("_id");
            console.log(toManageStudents);
            window.location.href = 'students.php?cid=' + toManageStudents;
        } else {
            $("body").append('<div class="alert alert-warning autoClose" style="position:absolute; bottom:10px; right:10px; display:inline-block;">Please select course to manage students.</div>');
            $("body .autoClose").fadeOut(4000, function () {
                $(this).remove();
            });
        }
    });
});

