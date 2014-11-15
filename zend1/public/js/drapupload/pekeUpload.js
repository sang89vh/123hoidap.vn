/*
 *  PekeUpload 1.0.6 - jQuery plugin
 *  written by Pedro Molina
 *  http://www.pekebyte.com/
 *
 *  Copyright (c) 2013 Pedro Molina (http://pekebyte.com)
 *  Dual licensed under the MIT (MIT-LICENSE.txt)
 *  and GPL (GPL-LICENSE.txt) licenses.
 *
 *  Built for jQuery library
 *  http://jquery.com
 *
 */
(function($) {

	$.fn.pekeUpload = function(options) {

		// default configuration properties
		var defaults = {
			onSubmit : false,
			btnText : "  Chọn ảnh từ máy tính",
			url : 'http://' + domain + '/media/drap-upload',
			theme : "bootstrap",
			field : "file",
			data : null,
			multi : true,
			showFilename : true,
			showPercent : true,
			showErrorAlerts : true,
			allowedExtensions : "",
			invalidExtError : "Invalid File Type",
			maxSize : 50,
			sizeError : "Size of the file is greather than allowed",
			onFileError : function(file, error) {
			},
			onFileSuccess : function(file, data) {
			}
		};

		var options = $.extend(defaults, options);

		// Main function
		var obj;
		var file = new Object();
		var fileinput = this;
		this
				.each(function() {
					obj = $(this);
					// HTML code depends of theme
					if (options.theme == "bootstrap") {
						var html = '<a href="javascript:void(0)" class="btn btn-default btn-upload"> <span class="glyphicon glyphicon-camera"></span> '
								+ options.btnText
								+ '</a><div class="pekecontainer"></div>';
					}
					if (options.theme == "custom") {
						var html = '<a href="javascript:void(0)" class="btn-pekeupload">'
								+ options.btnText
								+ '</a><div class="pekecontainer"></div>';
					}
					obj.after(html);
					obj.hide();
					// Event when clicked the newly created link
					obj.next('a').click(function() {
						obj.click();
					});
					// Event when user select a file
					obj.change(function() {
						file.name = obj.val().split('\\').pop();
						file.size = (obj[0].files[0].size / 1024) / 1024;
						if (validateresult() == true) {
							if (options.onSubmit == false) {
								UploadFile();
							} else {
								obj.next('a').next('div').prepend(
										'<br /><span class="filename">'
												+ file.name + '</span>');
								obj.parent('form').bind('submit', function() {
									obj.next('a').next('div').html('');
									UploadFile();
								});
							}
						}
					});
				});
		// Function that uploads a file
		function UploadFile() {
			var error = true;
			if (options.theme == "bootstrap") {
				var htmlprogress = '<div class="file"><div class="filename"></div><div class="progress progress-striped"><div class="progress-bar"  role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 100%;"><span class="badge badge-info"></span></div></div></div>';
			}
			if (options.theme == "custom") {
				var htmlprogress = '<div class="file"><div class="filename"></div><div class="progress-pekeupload"><div class="bar-pekeupload pekeup-progress-bar" style="width: 0%;"><span></span></div></div></div>';
			}
			obj.next('a').next('div').prepend(htmlprogress);
			var formData = new FormData();
			formData.append(options.field, obj[0].files[0]);
			formData.append('data', options.data);
			$
					.ajax({
						url : options.url,
						type : 'POST',
						data : formData,
						dataType : 'json',
						success : function(response) {
							data = response.status;
							var percent = 100;
							obj.next('a').next('div').find(
									'.pekeup-progress-bar:first').width(
									percent + '%');
							obj.next('a').next('div').find(
									'.pekeup-progress-bar:first').text(
									percent + "%");

							$("div.progress-striped .progress-bar").attr("aria-valuenow",percent);
							if (data == 1) {
								if (options.multi == false) {
									obj.attr('disabled', 'disabled');
								}
								options.onFileSuccess(file, data);
								$("#faq-respond").css("display", "none");
								$(".message").css("display", "none");
								$(".faq-upload-file-name").html(
										response.file_name);
								var template = '<div class="preview faq-preview-upfile" id="'+response.mediaId+'">'
										+ '<div><img /></div>'
										+ '<div class="faq_preview_delete"><button class="faq_btn_preview_delete btn btn-default btn-xs" role="button" ><span  class="glyphicon glyphicon-trash"></span> xóa ảnh</button></div>'
										+ '</div>';
								var preview = $(template), image = $('img',
										preview),btn = $('button',preview);
								var dropbox = $('#faq_drap_upload');
								image.attr('src', response.mediaUrl);
								$(btn).attr('media',
										response.mediaId);
								preview.appendTo(dropbox);
							} else {
								$(".faq-preview-upfile").remove();
								$("#faq-respond-message")
										.html(response.message);
								$("#faq-respond").css("display", "block");
								$("#faq_drap_upload").attr("media", "");
								data = response.message;
								options.onFileError(file, data);
								obj.next('a').next('div').find('.file:first')
										.remove();
								if ((options.theme == "bootstrap")
										&& (options.showErrorAlerts == true)) {
									obj
											.next('a')
											.next('div')
											.prepend(
													'<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button> '
															+ data + '</div>');
									bootstrapclosenotification();
								}
								if ((options.theme == "custom")
										&& (options.showErrorAlerts == true)) {

									obj
											.next('a')
											.next('div')
											.prepend(
													'<div class="alert-pekeupload"><button type="button" class="close" data-dismiss="alert">&times;</button> '
															+ data + '</div>');
									customclosenotification();
								}
								error = false;
							}
						},
						xhr : function() { // custom xhr
							myXhr = $.ajaxSettings.xhr();
							if (myXhr.upload) { // check if upload property
												// exists
								myXhr.upload.addEventListener('progress',
										progressHandlingFunction, false); // for
																			// handling
																			// the
																			// progress
																			// of
																			// the
																			// upload
							}
							return myXhr;
						},
						cache : false,
						contentType : false,
						processData : false
					});
			return error;
		}
		// Function that updates bars progress
		function progressHandlingFunction(e) {
			if (e.lengthComputable) {
				var total = e.total;
				var loaded = e.loaded;
				if (options.showFilename == true) {
					obj.next('a').next('div').find('.file').first().find(
							'.filename:first').text(file.name);
				}
				if (options.showPercent == true) {
					var percent = Number(((e.loaded * 100) / e.total)
							.toFixed(2));
					obj.next('a').next('div').find('.file').first().find(
							'.pekeup-progress-bar:first').width(percent + '%');
				}
				obj.next('a').next('div').find('.file').first().find(
						'.pekeup-progress-bar:first').html(
						'<center>' + percent + "%</center>");
			}
		}
		// Validate master
		function validateresult() {
			var canUpload = true;
			if (options.allowedExtensions != "") {
				var validationresult = validateExtension();
				if (validationresult == false) {
					canUpload = false;
					if ((options.theme == "bootstrap")
							&& (options.showErrorAlerts == true)) {
						obj
								.next('a')
								.next('div')
								.prepend(
										'<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button> '
												+ options.invalidExtError
												+ '</div>');
						bootstrapclosenotification();
					}
					if ((options.theme == "custom")
							&& (options.showErrorAlerts == true)) {
						obj
								.next('a')
								.next('div')
								.prepend(
										'<div class="alert-pekeupload"><button type="button" class="close">&times;</button> '
												+ options.invalidExtError
												+ '</div>');
						customclosenotification();
					}
					options.onFileError(file, options.invalidExtError);
				} else {
					canUpload = true;
				}
			}
			if (options.maxSize > 0) {
				var validationresult = validateSize();
				if (validationresult == false) {
					canUpload = false;
					if ((options.theme == "bootstrap")
							&& (options.showErrorAlerts == true)) {
						obj
								.next('a')
								.next('div')
								.prepend(
										'<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button> '
												+ options.sizeError + '</div>');
						bootstrapclosenotification();
					}
					if ((options.theme == "custom")
							&& (options.showErrorAlerts == true)) {
						obj
								.next('a')
								.next('div')
								.prepend(
										'<div class="alert-pekeupload"><button type="button" class="close" data-dismiss="alert">&times;</button> '
												+ options.sizeError + '</div>');
						customclosenotification();
					}
					options.onFileError(file, options.sizeError);
				} else {
					canUpload = true;
				}
			}
			return canUpload
		}
		// Validate extension of file
		function validateExtension() {
			var ext = obj.val().split('.').pop().toLowerCase();
			var allowed = options.allowedExtensions.split("|");
			if ($.inArray(ext, allowed) == -1) {
				return false;
			} else {
				return true;
			}
		}
		// Validate Size of the file
		function validateSize() {
			if (file.size > options.maxSize) {
				return false;
			} else {
				return true;
			}
		}
		// Function that allows close alerts of bootstap
		function bootstrapclosenotification() {
			obj.next('a').next('div').find('.alert-error').click(function() {
				$(this).remove();
			});
		}
		function customclosenotification() {
			obj.next('a').next('div').find('.alert-pekeupload').click(
					function() {
						$(this).remove();
					});
		}
	};

})(jQuery);