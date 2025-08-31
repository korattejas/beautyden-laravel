$(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    FilePond.registerPlugin(FilePondPluginImagePreview);

    let pond, thumbnailPond;

    if (
        typeof is_one_image_and_multiple_image_status !== "undefined" &&
        is_one_image_and_multiple_image_status === "is_one_image"
    ) {
        const imageInputElement = document.querySelector('input[name="icon"]');
        pond = FilePond.create(imageInputElement, {
            allowMultiple: false,
            instantUpload: false,
            allowProcess: false,
        });

        const thumbnailInputElement = document.querySelector(
            'input[name="thumbnail_image_path"]'
        );
        if (thumbnailInputElement) {
            thumbnailPond = FilePond.create(thumbnailInputElement, {
                allowMultiple: false,
                instantUpload: false,
                allowProcess: false,
            });
        }
    } else {
        const imageInputElement = document.querySelector(
            'input[name="photos[]"]'
        );
        const thumbnailInputElement = document.querySelector(
            'input[name="thumbnail_image_path[]"]'
        );

        pond = FilePond.create(imageInputElement, {
            allowMultiple: true,
            instantUpload: false,
            allowProcess: false,
        });

        thumbnailPond = FilePond.create(thumbnailInputElement, {
            allowMultiple: true,
            instantUpload: false,
            allowProcess: false,
        });
    }

    let $addPoseImagesForm = $("#addEditForm");
    $addPoseImagesForm.on("submit", function (e) {
        e.preventDefault();
        $addPoseImagesForm.parsley().validate();
        if ($addPoseImagesForm.parsley().isValid()) {
            loaderView();
            let formData = new FormData($addPoseImagesForm[0]);

            if (
                typeof is_one_image_and_multiple_image_status !== "undefined" &&
                is_one_image_and_multiple_image_status == "is_one_image"
            ) {
                if (pond.getFiles().length > 0) {
                    formData.append("icon", pond.getFiles()[0].file);
                }
                if (thumbnailPond) {
                    if (thumbnailPond.getFiles().length > 0) {
                        formData.append(
                            "thumbnail_image_path",
                            thumbnailPond.getFiles()[0].file
                        );
                    }
                }
            } else {
                pond.getFiles().forEach((file) => {
                    formData.append("photos[]", file.file);
                });

                thumbnailPond.getFiles().forEach((file) => {
                    formData.append("thumbnail_image_path[]", file.file);
                });
            }

            axios
                .post(APP_URL + "/" + form_url, formData)
                .then(function (response) {
                    if ($("#form-method").val() === "add") {
                        $addPoseImagesForm[0].reset();
                        pond.removeFiles();
                        if (thumbnailPond) {
                            thumbnailPond.removeFiles();
                        }
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
