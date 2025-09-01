// ============================================================================
// General Functions
// ============================================================================



// ============================================================================
// Page Load (jQuery)
// ============================================================================

$(() => {

    // Autofocus
    $('form :input:not(button):first').focus();
    $('.err:first').prev().focus();
    $('.err:first').prev().find(':input:first').focus();

    // Confirmation message
    $('[data-confirm]').on('click', e => {
        const text = e.target.dataset.confirm || 'Are you sure?';
        if (!confirm(text)) {
            e.preventDefault();
            e.stopImmediatePropagation();
        }
    });

    // Initiate GET request
    $(document).on('click', '[data-get]', e => {
        e.preventDefault();
        const url = e.currentTarget.dataset.get;
        location = url || location;
    });

    // Initiate POST request
    $('[data-post]').on('click', e => {
        e.preventDefault();
        const url = e.target.dataset.post;
        const f = $('<form>').appendTo(document.body)[0];
        f.method = 'POST';
        f.action = url || location;
        f.submit();
    });

    // Reset form
    $('[type=reset]').on('click', e => {
        e.preventDefault();
        location = location;
    });

    // Auto uppercase
    $('[data-upper]').on('input', e => {
        const a = e.target.selectionStart;
        const b = e.target.selectionEnd;
        e.target.value = e.target.value.toUpperCase();
        e.target.setSelectionRange(a, b);
    });

    // Photo preview
    $('input[type=file]').on('change', e => {
        const f = e.target.files[0];
        const img = $("#upload-preview img")[0];

        if (!img) return;

        img.dataset.src ??= img.src;

        if (f?.type.startsWith('image/')) {
            img.src = URL.createObjectURL(f);
        }
        else {
            img.src = img.dataset.src;
            e.target.value = '';
        }
    });

    $(".minus-number").click(function () {
        $no = $(".product-number").val();
        if ($no != 1) {
            $(".product-number").val(--$no);
        }
    })

    $(".add-number").click(function () {
        $no = $(".product-number").val();
        if ($no < 20) {
            $(".product-number").val(++$no);
        }
    })

    $("#checkAll").change(function () {
        if (this.checked) {
            $("input[name='checkboxItem[]']").each(function () {
                this.checked = true;
            });
        } else {
            $("input[name='checkboxItem[]']").each(function () {
                this.checked = false;
            });
        }
    })

    $('.checkboxes').click(function () {
        if ($(this).is(":checked")) {
            var isAllChecked = 0;
            $(".checkboxes").each(function () {
                if (!this.checked)
                    isAllChecked = 1;
            });

            if (isAllChecked == 0) {
                $("#checkAll").prop("checked", true);
            }
        }
        else {
            $("#checkAll").prop("checked", false);
        }
    });

    // check field is empty
    $(".required + input, .password-field > input").blur(function () {
        if (!$(this).val()) {
            $(this).nextAll().remove();
            $(this).after("<p class='inputError'>Required field</p>");
        }
    });

    $(".required + input, .password-field > input").keyup(function () {
        if ($(this).val()) {
            $(this).nextAll().remove();
        }
    });

    $("input:has(+p.inputError):first").focus();

    $('.disabled-edit input').prop("disabled", true);

    $('.disabled-edit .show-edit').prop("hidden", true);


    $('#enable-edit').click(function () {
        e = $('.disabled-edit:has(+ #enable-edit)')[0];
        $('.disabled-edit input').prop("disabled", false);
        $('.disabled-edit .show-edit').prop("hidden", false);
        $(".hide-edit").prop("hidden", true);
        $(e).removeClass("disabled-edit");
        $(e).addClass("active");
    });

    $("#close-modal").click(function () {
        console.log('hi')
        $(this).closest('.overlay').remove();
    });

});

function showpw(id) {
    var x = document.getElementById(id);
    if (x.type == "password") {
        x.type = "text";
    }
    else {
        x.type = "password";
    }
}

function showpwButton(id, e = null) {
    var x = document.getElementById(id);
    if (x.type == "password") {
        x.type = "text";
        console.log(e);
        $(e).html('<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#1e1717" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d = "M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" ></path >        <circle cx="12" cy="12" r="3"></circle></svg > ');
    }
    else {
        x.type = "password";

        $(e).html('<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 64 64"><g id="Layer_85" data-name="Layer 85"><path d="M61.59,30.79C61.06,30.08,48.26,13.5,32,13.5a24.84,24.84,0,0,0-4.6.44,2,2,0,1,0,.74,3.93A20.89,20.89,0,0,1,32,17.5c11.9,0,22.23,10.82,25.41,14.5a55.56,55.56,0,0,1-6.71,6.55,2,2,0,1,0,2.54,3.09,56.15,56.15,0,0,0,8.35-8.43A2,2,0,0,0,61.59,30.79Z"></path><path d="M48.4,42.29l-8.8-8.8L30.51,24.4l-7.82-7.82L12.25,6.14A2,2,0,0,0,9.43,9l8.49,8.49A55.12,55.12,0,0,0,2.41,30.79a2,2,0,0,0,0,2.42C2.94,33.92,15.74,50.5,32,50.5a29.57,29.57,0,0,0,14.67-4.29L55,54.57a2,2,0,0,0,2.83-2.83ZM28.82,28.36l6.83,6.83a4.84,4.84,0,1,1-6.83-6.83ZM32,46.5C20.1,46.5,9.77,35.68,6.59,32A50.36,50.36,0,0,1,20.87,20.41L26,25.53A8.83,8.83,0,1,0,38.47,38l5.27,5.27A25,25,0,0,1,32,46.5Z"></path></g></svg>');
    }
}

function reloadJS() {
    $('script[src="../js/app.js"]').remove();
    $("head").append("<script type='text/javascript' src='../js/app.js'>");
}

$.urlParam = function (name) {
    var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
    if (results == null) {
        return null;
    }
    return decodeURI(results[1]) || 0;
}
