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
    $('#studentsContainer div.btn-group[data-toggle-name]').each(function () {
        console.log(this);
        var group = $(this);
        var form = group.parents('div').eq(0);
        var name = group.attr('data-toggle-name');
        var hidden = $('input[name="' + name + '"]', form);
        $('button', group).each(function () {
            var button = $(this);
            button.on('click', function () {
                hidden.val($(this).val());
                $('#studentsContainer div.btn-group[data-toggle-name] button.active').removeClass('active');

            });
            if (button.val() === hidden.val()) {
                button.addClass('active');
            }
        });
    });
    $("tr[_id]").on('click', function (e) {
        $("#dvCreateStudent").hide();
        $("#dvSelectedStudent").show();
        $("tr.selectedRow").removeClass('selectedRow');
        $(this).addClass('selectedRow');
        $("#lblFirstName").html($(this).children('[data-attr="FirstName"]').html());
        $("#lblLastname").html($(this).children('[data-attr="Lastname"]').html());
        $("#lblSpiritualName").html($(this).children('[data-attr="SpiritualName"]').html());
        $("#lblSex").html($(this).children('[data-attr="Sex"]').html());
        $("#lblPhone").html($(this).children('[data-attr="Phone"]').html());
        $("#lblPhone-day").html($(this).children('[data-attr="Phone-day"]').html());
        $("#lblEmail").html($(this).children('[data-attr="Email"]').html());
        $("#lblStreetAddress").html($(this).children('[data-attr="StreetAddress"]').html());
        $("#lblAddress2").html($(this).children('[data-attr="Address2"]').html());
        $("#lblCity").html($(this).children('[data-attr="City"]').html());
        $("#lblState").html($(this).children('[data-attr="State"]').html());
        $("#lblCountry").html($(this).children('[data-attr="Country"]').html());
        $("#lblZipCode").html($(this).children('[data-attr="ZipCode"]').html());
    });

    $("#btnAddStudentContainer").click(function (e) {
        console.log("Showing create form...");
        $("#dvCreateStudent").show();
        $("#dvSelectedStudent").hide();
    });
    $("#btnCancelStudentContainer").click(function (e) {
        console.log("Hiding create form...");
        $("#dvCreateStudent").hide();
        $("#dvSelectedStudent").show();
    });

    var cities = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        limit: 100,
        remote: {
            cache: true,
            url: 'location.php?query=%QUERY',
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
    $('#valLocation').typeahead(null, {
        name: 'valLocation',
        display: 'label',
        source: cities
    });

    $('#valLocation').on('typeahead:select', function (ev, suggestion) {
        $('#valCity').val(suggestion.value.city);
        $("#valState").val(suggestion.value.state);
        $("#valCountry").val(suggestion.value.country);
        console.log($('[name="valCity"]').val());
    });
    $("[name='valFlStudentFile']").on('change', function (e) {
        var result = /[^\\]*$/.exec($(e.target).val())[0];
        $(this).parents(".input-group").children("input.lblFile").val(result);
    });

    if (errorObj['errors_dict']) {
        var errorObject = errorObj['errors_dict'];
        $("#blkUploadErrorMsgs").html('');
        var errorGlyph = '<i class="fa fa-exclamation-triangle" aria-hidden="true" style="vertical-align:middle"></i>  ';
        if (errorObject['invalidFile'] == 1) {
            $("#blkUploadErrorMsgs").append(createErrorMessageHTML('Please select one CSV file.'));
        }
        if (errorObject['invalidFileSize'] == 1) {
            $("#blkUploadErrorMsgs").append(createErrorMessageHTML('Upload file size too large. File must be smaller than 20MB.'));
        }
        if (errorObject['invalidFileExtension'] == 1) {
            $("#blkUploadErrorMsgs").append(createErrorMessageHTML('Please upload CSV file. Use the template for reference.'));
        }
        if (errorObject['blkSystem'] == 1) {
            $("#blkUploadErrorMsgs").append(createErrorMessageHTML('Couldnot process file. Please contact administrator.'));
        }
        if (errorObject['blksuccess'] == 0) {
            $("#blkUploadErrorMsgs").append(createSuccessMessageHTML('File processed successfully.'));
        }
        if (errorObject['delStudentSuccess'] == 0) {
            $("#msgBox").append(createSuccessMessageHTML('Student deleted successfully.'));
        }
        if (errorObject['delStudentSystem'] == 1) {
            $("#msgBox").html(createErrorMessageHTML('Couldnot delete student. Please contact administrator.'));
        }
        if (errorObject['txtDelStudentId'] == 1) {
            $("#msgBox").html(createErrorMessageHTML('Please select valid student Id for deleting.'));
        }
        if (errorObject['addStudentSystem'] == 1) {
            $("#msgBox").html(createErrorMessageHTML('Couldnot add student. Please contact administrator.'));
        }

        if (errorObject['addStudentSuccess'] == 0) {
            $("#msgBox").append(createSuccessMessageHTML('Student added successfully.'));
        }
    }
    $("#frmBulkStudentUpload").on('submit', function () {
        $("#blkOverlay").show();
    });
    $("#deleteStudent").on('click', function (e) {


        bootbox.confirm({
            message: "Are you sure you want to delete the student?",
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
                if(result){
                    var selectedRow = $("tr[_id].selectedRow");
                    if (selectedRow.length > 0) {
                        var toDeleteId = $(selectedRow).attr("_id");
                        console.log(toDeleteId);
                        $("#valDelStudentId").val(toDeleteId);
                        $("#btnFrmDeleteStudent").click();
                    } else {
                        $("body").append('<div class="alert alert-warning autoClose" style="position:absolute; bottom:10px; right:10px; display:inline-block;">Please select student to delete.</div>');
                        $("body .autoClose").fadeOut(4000, function () {
                            $(this).remove();
                        });
                    }
                }

            }
        });


    });

});