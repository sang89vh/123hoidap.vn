var media_add_form_html = null;
var media_add_image_link_form_html = null;
var media_add_video_link_form_html = null;
var mediaEdit = new Object();
mediaEdit.id = null;
$(document)
		.ready(
				function(evt) {
					media_add_form_html = $('#media-dir-add-form')[0].outerHTML;
					media_add_image_link_form_html = $('#media-image-link-add-form')[0].outerHTML;
					media_add_video_link_form_html = $('#media-video-link-add-form')[0].outerHTML;
					$('#media-dir-add-form').remove();
					$('#media-image-link-add-form').remove();
					$('#media-video-link-add-form').remove();
					$('img.edit-icon').popover({
						content : function() {
							var content = $('#media-edit').html();
							console.log($(this));
							return content;
						},
						delay : {
							hide : 2
						},
						html : true,
						trigger : 'click',
						placement : 'bottom'
					});

					$(document).on('mouseleave', '.popover', function(evt) {
						$('img.edit-icon').popover('hide');
					});

					$('[context="edit"]')
							.mouseenter(
									function(evt) {
										mediaEdit.id = null;
										$(this).find('img.edit-icon').show();
										mediaEdit.id = $(this).find(
												'div.media-item')[0]
												.getAttribute('item_id');

									});
					$('[context="edit"').mouseleave(function(evt) {
						$('[context="edit"] img.edit-icon').hide();
						$('img.edit-icon').popover('hide');
					});

					// add new media handler
					$('#media-add')
							.click(
									function(evt) {
										var action = $(this).attr('action');
										var media_type_text = "Thư mục";
										var media_form_html = "&nbsp;";
										if (action == 'image-file') {
											media_type_text = "Upload - File ảnh";
											file_type = "image";
											media_parent_id = $('#media-add')
													.attr('dir_id');
											uploadFile(file_type,
													media_type_text,
													media_parent_id);
											return;
										}
										if (action == 'video-file') {
											media_type_text = "Upload - File video";
											file_type = "video";
											media_parent_id = $('#media-add')
													.attr('dir_id');
											uploadFile(file_type,
													media_type_text,
													media_parent_id);
											return;
										}
										if (action == 'media-file') {
											media_type_text = "Upload - File";
											file_type = "file";
											media_parent_id = $('#media-add')
													.attr('dir_id');
											uploadFile(file_type,
													media_type_text,
													media_parent_id);
											return;
										}
										if (action == 'image-link')
											media_type_text = "Link ảnh";
										if (action == 'video-link')
											media_type_text = "Link video";
										media_type_text = "Thêm mới - "
												+ media_type_text;
										if (action == 'index')
											media_form_html = media_add_form_html;
										if (action == 'image-link')
											media_form_html = media_add_image_link_form_html;
										if (action == 'video-link')
											media_form_html = media_add_video_link_form_html;
										var media_parent_id = $('#media-add')
												.attr('dir_id');
										bootbox
												.dialog({
													message : media_form_html,
													title : media_type_text,
													buttons : {
														ok : {
															label : "Lưu",
															callback : function() {
																var isValid = true;
																var name = '';
																var link = '';
																if (action == 'index') {
																	isValid = validateit(
																			'media-dir-add-form',
																			'top',
																			false,
																			false);
																	name = $('.modal-dialog #media-dir-add-form')[0].name.value;
																}
																if (action == 'image-link') {
																	isValid = validateit(
																			'media-image-link-add-form',
																			'top',
																			false,
																			false);
																	name = $('#media-image-link-add-form')[0].name.value;
																	link = $('#media-image-link-add-form')[0].link.value;
																}
																if (action == 'video-link') {
																	isValid = validateit(
																			'media-video-link-add-form',
																			'top',
																			false,
																			false);
																	name = $('#media-video-link-add-form')[0].name.value;
																	link = $('#media-video-link-add-form')[0].link.value;
																}
																if (!isValid)
																	return false;

																$
																		.ajax(
																				{
																					url : '/media/add-media',
																					type : 'post',
																					data : {
																						name : name,
																						media_parent_id : media_parent_id,
																						link : link,
																						action : action
																					},
																					async : false
																				})
																		.done(
																				function(
																						data) {
																					console
																							.log(data);
																					if (data
																							.toString()
																							.trim() == 'saved') {
																						document.location.href = document.location.href;
																						isValid = true;
																					}
																					if (data
																							.toString()
																							.trim() == 'existed') {

																						bootbox
																								.alert("<span style='color:red'>Tên này đã tồn tại</span>");
																						isValid = false;
																					}
																					if (data
																							.toString()
																							.trim() == 'not_valid') {
																						bootbox
																								.alert("<span style='color:red'>Dữ liệu không hợp lệ</span>");
																						isValid = false;
																					}

																				});
																return isValid;
															}
														},
														cancel : {
															label : "Hủy bỏ!",
															callback : function() {

															}
														}

													}
												});
									});

					// media-select click handler
					$('#media-select')
							.click(
									function(evt) {
										var faq_media_file = window.parent.document
												.getElementById('faq_media_file');
										var event = new Event(
												'faq_select_media_file');
										var media_item_selected_list = $('.media-item-selected');
										var media_id_list = new Array();
										var media_id_list_string = '';
										if (!faq_media_file) {
											return;
										}
										$.each(media_item_selected_list,
												function(idx, val) {
													media_id_list.push($(this)
															.attr('item_id'));
												});
										media_id_list_string = media_id_list
												.join(';');
										faq_media_file.value = media_id_list_string;
										faq_media_file.dispatchEvent(event);
										// close fancybox
										parent.$.fancybox.close();

									});

					// hide media-item if is not a iframe
					if (!window.parent.document
							.getElementById('faq_media_file')) {
						$('#media-select').hide();
					}

					// media-item click handler
					$('.media-item').click(function(evt) {
						$(this).toggleClass('media-item-selected');
					});


						$.cookie('medianav' + faquser.userId, location.href);
						console.log("update medianav");


				});
// edit - add media
function editMedia() {
	if (mediaEdit.id) {

//		bootbox.dialog({
//			message : "Bạn có chắc chắn muốn xóa?",
//			title : "Xóa file media",
//			buttons : {
//				ok : {
//					label : "Có",
//					callback : function() {
//						$.ajax({
//							url : DOMAIN + '/media/update-media',
//							data : {
//								act : 'edit',
//								id : mediaEdit.id,
//								backlink : document.location.href
//							}
//						}).done(function(evt) {
//
//						});
//					}
//				},
//				cancel : {
//					label : "Không!",
//					callback : function() {
//
//					}
//				}
//
//			}
//		});

	}
}

// edit - delete media
function deleteMedia() {
	if (mediaEdit.id) {
		bootbox.dialog({
			message : "Bạn có chắc chắn muốn xóa?",
			title : "Xóa file media",
			buttons : {
				ok : {
					label : "Có",
					callback : function() {
						$.ajax({
							url : DOMAIN + '/media/deactive',
							type : "POST",
							dataType : "json",
							data : {
								act : 'edit',
								mediaId : mediaEdit.id,
								backlink : document.location.href
							}
						}).done(function(data) {
							console.log(data);
							console.log("#"+mediaEdit.id);
							if(data.status==1){
								$("#"+mediaEdit.id).remove();
							}

						});
					}
				},
				cancel : {
					label : "Không!",
					callback : function() {

					}
				}

			}
		});
	}
}
function changeVideoLink(src) {
	$('#media-video-link-add-form iframe').remove();
	$('#media-video-link-add-form').append('<div id=player ></div>');
	if (src != "") {
		id = src.split('=')[1];
		player = new YT.Player('player', {
			height : '150',
			width : '200',
			videoId : id,
			events : {

			}
		});
	}

}
function uploadFile(file_type, header_text, media_parent_id) {
	var back_link = document.location.href;
	var ifr_url = "/media/upload-file?file_type=" + file_type
			+ "&media_parent_id=" + media_parent_id + "&back_link=" + back_link;
	ifr_url = "<iframe width='100%' height='200px' style='border:none' src='"
			+ ifr_url + "' />";
	bootbox.dialog({
		message : ifr_url,
		title : header_text,
	});
}
