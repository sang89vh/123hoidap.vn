k=1;
$(document)
		.ready(
				function() {

					// add class css bootrap
					$("div.form-group label").addClass("control-label");
					$("form.form-horizontal div.form-group label").addClass(
							"col-lg-2 control-label");
					$("form").attr("role", "form");

					$(".faq_content_question_tooltip").tooltip({
						'html' : 'true',
						'placement' : 'bottom'
					});
					$("#faq_subject_selected").attr("href","#"+subject);
					$("#faq_subject_selected").text($("#faq_nav_subject li[subject="+subject+"]>a").text());
					$("#faq_subject_selected").click(function(){
						$("#faq_nav_subject").removeClass("faq_nav_subject_scroll");
						$k=2;
					});

					$("#faq_nav_subject li").click(function() {
						subject = $(this).attr("subject");
						$("#faq_subject_selected").attr("href","#"+subject);
						$("#faq_nav_subject li").removeClass("active");
						$(this).addClass("active");

					});

					$("#faq_nav_subject li>a").click(function() {
						title=$(this).text();
						$("#faq_subject_selected").text(title);

					});

						if (!isEmptyOrNull(tags)) {
							var faq_key_word = tags.split(",");
							$(faq_key_word).each(function( index ) {
								$('#faq_txt_key_word').tagsinput('add',
										faq_key_word[index]);
							});
						}
					setTinyEdittor();
					$("input,select,textarea").not("[type=submit]")
							.jqBootstrapValidation();
					// suggestion question
					$('div.bootstrap-tagsinput input')
							.autocomplete(
									{
										serviceUrl : basePath
												+ '/search/find-tag?title=false&user=false',
										onSelect : function(suggestion) {
											var q = suggestion.value;
											$(this).val(q);
										}
									});
					// INITIALIZE WIZARD
					$('#faq_question_cancel').on('click',function() {
						bootbox.confirm("Bạn có chắc muốn không thay đổi câu hỏi?", function(result) {
							  if(result){
								  location.href=basePath+backUrl;
							  }
							});

					});
					$('#faq_question_save').on('click',function() {
										tags = $('#faq_txt_key_word').val();
										title = $("#faq_txt_title").val();
										var noteEdit = $("#faq_txt_note_edit").val();
										contentQuestion = FaqEdittor
												.getContent('TINY');
										var bonusPoint=$("#faq_txt_bonus_point").val();
										var btn = $('#faq_question_save');
										if (title.trim().length >= 8
												&& contentQuestion.trim().length >= 15
												&& tags.trim().length > 0
												&& noteEdit.trim().length > 0
												) {
											console.log("2->3 line 2");
							        btn.button('loading');
							        $.ajax({
														// async:false,
														url : basePath
																+ "/question/save-wikistyle",
														type : "POST",
														dataType : "json",
														data : {

															tags : tags,
															subject : subject,
															title : title,
															contentQuestion : contentQuestion,
															noteEdit:noteEdit,
															bonusPoint:bonusPoint,
															question:questionID

														}, // The data your
															// sending
														// to page
														success : function(data) {
															if (data.status === 1) {
																btn.button('reset');
																window.location.href = backUrl;
//																bootbox
//																.dialog({
//																	message : "Cập nhật dữ liệu thành công!",
//																	title : "Thông báo",
//																	buttons : {
//																		success : {
//																			label : "Ok",
//																			className : "btn-success",
//																			callback : function() {
//																				window.location.href = backUrl;
//																			}
//																		}
//
//																	}
//																});
															}else if (data.status === 0) {
																bootbox.alert("có lỗi xẩy ra!");
																btn.button('reset');
															}else if (data.status === 2) {
																bootbox.alert("Câu hỏi đã đóng, không thể sửa!");
																btn.button('reset');
															}else if (data.status === 3) {
																bootbox.alert("Chưa có nội dung nào được sửa đổi!");
																btn.button('reset');
															}


														},
														error : function() {
															bootbox
																	.alert("Lưu không thành công!");
															btn.button('reset');
															console
																	.log("AJAX request was a failure");
														}
													});
										} else {
											console.log("2->3 line 1");
											// var errorMessage="";
											$("#faq_help_title").css("display",
													"none");
											$("#faq_help_content").css(
													"display", "none");
											$("#faq_help_tag").css("display",
													"none");
											if (title.trim().length < 8) {
												// errorMessage=errorMessage+"Tiêu
												// đề câu hỏi chứa tối thiểu 8
												// ký tự<br/>";
												$("#faq_help_title")
														.text(
																"Tiêu đề câu hỏi chứa tối thiểu 8 ký tự");
												$("#faq_help_title").css(
														"display", "block");
												$("#faq_help_title")
														.removeClass(
																"help-block");
											}
											if (contentQuestion.trim().length < 15) {
												$("#faq_help_content")
														.text(
																"Nội dung câu hỏi chứa tối thiểu 15 ký tự");
												$("#faq_help_content").css(
														"display", "block");
												$("#faq_help_content")
														.removeClass(
																"help-block");
												// errorMessage=errorMessage+"Nội
												// dung câu hỏi chứa tối thiểu
												// 15 ký tự<br/>";
											}
											if (tags.trim().length < 1) {
												$("#faq_help_tag")
														.text(
																"Câu hỏi phải chứa tối thiểu 1 từ khóa");
												$("#faq_help_tag").css(
														"display", "block");
												$("#faq_help_tag").removeClass(
														"help-block");
												// errorMessage=errorMessage+"Câu
												// hỏi phải chứa tối thiểu 1 từ
												// khóa";
											}
											if (noteEdit.trim().length < 1) {
												$("#faq_help_note_edit")
												.text(
												"Hẫy điền lý do sửa đổi của bạn");
												$("#faq_help_note_edit").css(
														"display", "block");
												$("#faq_help_note_edit").removeClass(
												"help-block");
												// errorMessage=errorMessage+"Câu
												// hỏi phải chứa tối thiểu 1 từ
												// khóa";
											}
											btn.button('reset');
										}

										// $("#faq_btn_next").click();

									});

					//
//					$("#faq_header_btn_create_basic").fixTo("#center-container");

				});// end document.ready

