$(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })
    var uploader = new qq.FineUploader({
        debug: true,
        element: document.getElementById('fine-uploader'),
        request: {
            endpoint: APP_URL + '/imageUpload',
            customHeaders: {
                "X-CSRF-Token": $("meta[name='csrf-token']").attr("content")
            }, params: {
                temp_time: $("#temp_time").val()
            },
        },
        resume: {
            enabled: true
        },
        deleteFile: {
            enabled: false,
            endpoint: APP_URL + '/imageDelete',
            customHeaders: {
                "X-CSRF-Token": $("meta[name='csrf-token']").attr("content")
            },
            params: {
                tmpTime: $("#temp_time").val()
            },
        },
        thumbnails: {
            placeholders: {
                waitingPath: JS_URL + '/panel-assets/js/scripts/fine-uploader/placeholders/waiting-generic.png',
                notAvailablePath: JS_URL + '/panel-assets/js/scripts/fine-uploader/placeholders/not_available-generic.png'
            }
        },
        validation: {
            allowedExtensions: ['jpeg', 'jpg', 'png','mp4']
        },
        retry: {
            enableAuto: false
        }
    });

    var uploader = new qq.FineUploader({
        debug: true,
        element: document.getElementById('fine-uploader-1'),
        request: {
            endpoint: APP_URL + '/imageUpload',
            customHeaders: {
                "X-CSRF-Token": $("meta[name='csrf-token']").attr("content")
            }, params: {
                temp_time: $("#temp_time").val()
            },
        },
        resume: {
            enabled: true
        },
        deleteFile: {
            enabled: false,
            endpoint: APP_URL + '/imageDelete',
            customHeaders: {
                "X-CSRF-Token": $("meta[name='csrf-token']").attr("content")
            },
            params: {
                tmpTime: $("#temp_time").val()
            },
        },
        thumbnails: {
            placeholders: {
                waitingPath: JS_URL + '/panel-assets/js/scripts/fine-uploader/placeholders/waiting-generic.png',
                notAvailablePath: JS_URL + '/panel-assets/js/scripts/fine-uploader/placeholders/not_available-generic.png'
            }
        },
        validation: {
            allowedExtensions: ['jpeg', 'jpg', 'png','mp4']
        },
        retry: {
            enableAuto: false
        }
    });

});
