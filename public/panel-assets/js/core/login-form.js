$(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });
    let $form = $("#addEditForm");
    $form.on("submit", function (e) {
        e.preventDefault();
        $form.parsley().validate();
        if ($form.parsley().isValid()) {
            loaderView();
            let formData = new FormData($form[0]);
            axios
                .post(APP_URL + "/" + form_url, formData)
                .then(function (response) {
                    if ($("#form-method").val() === "add") {
                        $form[0].reset();
                        pond.removeFiles();
                    }
                    setTimeout(function () {
                        window.location.href = APP_URL + "/" + redirect_url;
                        loaderHide();
                    }, 1000);
                    notificationToast(response.data.message, "success");
                })
                .catch(function (error) {
                    notificationToast(error.response.data.message, "warning");
                    loaderHide();
                });
        }
    });
    integerOnly();
});
