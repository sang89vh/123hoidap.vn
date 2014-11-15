$(function() {

	var dropbox = $('#faq_drap_upload'), message = $('.message', dropbox);

	dropbox
			.filedrop({
				// The name of the $_FILES entry:
				paramname : 'file',

				maxfiles : 2,
				maxfilesize : 50,
				url : 'http://' + domain + '/media/drap-upload',

				uploadFinished : function(i, file, response) {
					$.data(file).addClass('done');
					if (response.status == 1) {
						$("#faq-respond").css("display", "none");
						$(".faq-upload-file-name").html(response.file_name);
						var btnDeletes=$(".faq_btn_preview_delete");
						var lastBtnDelete=btnDeletes.last();
//						alert(lastBtnDelete);
						$(lastBtnDelete).attr("media",response.mediaId);
						var previewUpfiles=$(".faq-preview-upfile");
						var previewUpfile=previewUpfiles.last();
//						alert(lastBtnDelete);
						$(previewUpfile).attr("id",response.mediaId);
						$(".message").css("display", "none");
					} else {
						$(".faq-preview-upfile").remove();
						$("#faq-respond-message").html(response.message);
						$("#faq-respond").css("display", "block");
//						$("#faq_drap_upload").attr("media", "");
						$(".message").css("display", "block");
					}

					// response is the JSON object that post_file.php returns
				},

				error : function(err, file) {
					switch (err) {
					case 'BrowserNotSupported':
						showMessage('Trình duyệt của bạn không hỗ trợ HTML5,tham khảo trình duyệt chrome: https://www.google.com/intl/en/chrome/browser/!');
						break;
					case 'TooManyFiles':
						bootbox
								.alert('Quá nhiều file! Chọn 1 file duy nhất để chia sẻ!');
						break;
					case 'FileTooLarge':
						bootbox
								.alert(file.name
										+ ' dung lượng file quá lớn! File chia sẻ phải nhỏ hơn 50mb.');
						break;
					default:
						break;
					}
				},

				// Called before each upload is started
				beforeEach : function(file) {
					if (!file.type.match(/^image\//)) {
						bootbox.alert('Chỉ chấp nhận tải lên file ảnh!');
						return false;
					}
				},

				uploadStarted : function(i, file, len) {
					createImage(file);
				},

				progressUpdated : function(i, file, progress) {
					$.data(file).find('.progress-bar').width(progress);
					// console.log(progress);
					var processBar = $(".progress-bar");
					processBar.css('width', progress + "%");
					processBar.html(progress + "%");
					processBar.attr('aria-valuenow', progress);
				}

			});

	var template = '<div class="preview faq-preview-upfile">'
			+ '<span class="faq-upload-file-name text-info" ></span>'
			+ '<div class="progressHolder">'
			+ '<div class="progress">'
			+ '<div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">'
			+ '0%'
			+ '</div>'
			+ '</div>'
			+ '</div>'
			+ '<div><img /></div>'
			+'<div class="faq_preview_delete"><button class="faq_btn_preview_delete btn btn-default btn-xs" role="button" ><span  class="glyphicon glyphicon-trash"></span> xóa ảnh</button></div>'
			+ '</div>';

	function createImage(file) {

		var preview = $(template), image = $('img', preview);

		var reader = new FileReader();

		image.width = 100;
		image.height = 100;
		reader.onload = function(e) {

			// e.target.result holds the DataURL which
			// can be used as a source of the image:

			image.attr('src', e.target.result);
		};

		// Reading the file as a DataURL. When finished,
		// this will trigger the onload function above:
		reader.readAsDataURL(file);

		message.hide();
		preview.appendTo(dropbox);

		// Associating a preview container
		// with the file, using jQuery's $.data():

		$.data(file, preview);
	}


	function showMessage(msg) {
		message.html(msg);
	}

	$("#faq_input_upload_image").pekeUpload({
		theme : 'bootstrap',
		allowedExtensions : "jpeg|jpg|png|gif",
		onFileError : function(file, error) {
			alert("error on file: " + file.name + " error: " + error + "");
		}
	});
});
;