const funTooltip = () => {
    $('[data-toggle="tooltip"]').tooltip()
}

// const notificationToast = (message, type) => {
//     console.log(message, type);
//     if (type === 'success') {
//         toastr['success'](message, 'Success!', {
//             closeButton: true,
//             tapToDismiss: false,
//         });
//     } else if (type === 'warning') {
//         toastr['warning'](message, 'Warning!', {
//             closeButton: true,
//             tapToDismiss: false,
//         });
//     }
// }

function notificationToast(message, type) {
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "hideMethod": "fadeOut"
    };

    switch (type) {
        case 'success':
            toastr.success(message, 'Success');
            break;
        case 'warning':
            toastr.warning(message, 'Warning');
            break;
        case 'error':
            toastr.error(message, 'Error');
            break;
        case 'info':
            toastr.info(message, 'Info');
            break;
        default:
            toastr.info(message, 'Info');
            break;
    }
}


const floatOnly = () => {
    $('.float').keypress(function (event) {
        if ((event.which !== 46 || $(this).val().indexOf('.') !== -1) && (event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
    });
}

const integerOnly = () => {
    $('.integer').keypress(function (event) {
        if (event.which !== 8 && event.which !== 0 && (event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
    });
}

$('.integer').keypress(function (event) {
    if (event.which !== 8 && event.which !== 0 && (event.which < 48 || event.which > 57)) {
        event.preventDefault();
    }
});


const loaderView = () => {
    $.blockUI({
        message: '<div class="spinner-border text-info" role="status"><span class="visually-hidden">Loading...</span></div>',
        css: {
            padding: 0,
            margin: 0,
            width: "25%",
            top: "40%",
            left: "35%",
            textAlign: "center",
            color: "#000",
            border: "none",
            backgroundColor: "transparent",
            cursor: "wait",
            "z-index": "99999999"
        }
    });
    $(".blockOverlay").css('z-index', 99999999999)
}

const loaderHide = () => {
    setTimeout(function () {
        $.unblockUI();
    }, 100)
}

$(document).on('click', '.edit-button', function () {
    window.location.href = $(this).data('href');
})
