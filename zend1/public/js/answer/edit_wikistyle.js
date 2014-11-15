
$(document)
		.ready(
				function() {

					// add class css bootrap
					$("div.form-group label").addClass("control-label");
					$("form.form-horizontal div.form-group label").addClass(
							"col-lg-2 control-label");
					$("form").attr("role", "form");

					$(".faq_content_answer_tooltip").tooltip({
						'html' : 'true',
						'placement' : 'bottom'
					});

					setTinyEdittor();
					// INITIALIZE WIZARD
					$('#faq_answer_cancel').on('click',function() {
						bootbox.confirm("Bạn có chắc muốn không thay đổi câu trả lời?", function(result) {
							  if(result){
								  location.href=basePath+backUrl;
							  }
							});

					});
					$('#faq_answer_save').click(function() {

										var noteEdit = $("#faq_txt_note_edit").val();
										contentAnswer = FaqEdittor
												.getContent('TINY');

										if (contentAnswer.trim().length >= 10

												&& noteEdit.trim().length > 0
												) {
											console.log("2->3 line 2");
											var btn = $('#faq_answer_save');
							        btn.button('loading');
							        $.ajax({
														// async:false,
														url : basePath
																+ "/answer/save-wikistyle",
														type : "POST",
														dataType : "json",
														data : {
															contentAnswer : contentAnswer,
															noteEdit:noteEdit,
															answer:answerID,
															question:questionID,
														}, // The data your
															// sending
														// to page
														success : function(data) {
															if (data.status === 1) {
																btn.button('reset');
																bootbox
																.dialog({
																	message : "Cập nhật dữ liệu thành công!",
																	title : "Thông báo",
																	buttons : {
																		success : {
																			label : "Ok",
																			className : "btn-success",
																			callback : function() {
																				window.location.href = backUrl;
																			}
																		}

																	}
																});
															}else if (data.status === 2) {
																bootbox.alert("Bạn chưa được cấp quyền truy cập trang này!");
																btn.button('reset');
															}else if (data.status === 3) {
																bootbox.alert("Không tồn tại câu hỏi!");
																btn.button('reset');
															}else if (data.status === 4) {
																bootbox.alert("Không tồn tại câu trả lời!");
																btn.button('reset');
															}else if (data.status === 5) {
																bootbox.alert("Câu trả lời không thuộc cộng đồng Wiki, không thể sửa!");
																btn.button('reset');
															}else if (data.status === 6) {
																bootbox.alert("Cập nhật nội dung câu trả lời hoặc hủy bỏ sửa đổi!");
																btn.button('reset');
															}else{
																bootbox.alert("có lỗi xẩy ra!");
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

											$("#faq_help_content").css(
													"display", "none");


											if (contentAnswer.trim().length < 10) {
												$("#faq_help_content")
														.text(
																"Nội dung câu hỏi chứa tối thiểu 10 ký tự");
												$("#faq_help_content").css(
														"display", "block");
												$("#faq_help_content")
														.removeClass(
																"help-block");
												// errorMessage=errorMessage+"Nội
												// dung câu hỏi chứa tối thiểu
												// 10 ký tự<br/>";
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

