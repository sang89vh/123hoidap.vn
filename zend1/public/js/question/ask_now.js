var tags = [];
$(document)
		.ready(
				function() {
					function resetForm(){
						 FaqEdittor.setContent('MARKDOWN', '');
			        	 $("#wmd-input-answer").val("");
					}

					$("#faq_btn_ask_now_full_cancel").click(function() {

						$("#faq_ask_now").toggle();
						$("#faq_ask_now_full").toggle();
						$("#header").toggle();
						$("#topcontrol").click();
					});
					$(document)
							.on(
									"click",
									".faq_btn_preview_delete",
									function() {
										var mediaId = $(this).attr("media");
										$
												.ajax({
													async : false,
													url : basePath
															+ "/media/delete",
													type : "POST",
													dataType : "json",
													data : {
														mediaId : mediaId
													},
													success : function(data) {

														if (data.status == 1) {
															$("#" + mediaId)
																	.remove();
															if ($(
																	".faq-preview-upfile")
																	.size() < 1) {
																var divTmp = $(
																		"#faq_drap_upload_tmp")
																		.html();
																$(
																		"#faq_drap_upload")
																		.html(
																				divTmp);
																$(".message")
																		.css(
																				"display",
																				"block");
															}

															$(".pekecontainer")
																	.html("");
														} else {
															bootbox
																	.alert("Xẩy ra lỗi!");
														}

													},
													error : function() {
														console
																.log("AJAX request was a failure");

													}
												});
									});
					// media default
					function updateHrefMediaLink() {

							var last_url = $.cookie('medianav');
							if (last_url != null && last_url.trim() != "") {
								$("#faq_link_media_file")
										.attr("href", last_url);
							}


					}
					updateHrefMediaLink();
					// $.fancybox.close(function() {
					// updateHrefMediaLink();
					// });

					$("#faq_btn_media_lib").click(function() {
						$("#wmd-image-button-answer").click();
					});
					$("#faq_btn_tool_image").click(function() {
						$("#faq_wrap_drap_upload").toggle();
					});

					$("#faq_btn_ask_now").click(function() {
						if (!isAllowed) {
							redirectLogin();
							return;
						} else {
							var questionTitle = $("#faq_question_title").val();
							var lastSubject=$.cookie('faqLastSubject');
							if(!isEmptyOrNull(lastSubject)){
							$("#faq_question_subject").val(lastSubject);
							}
							if(questionTitle.trim().length>=8){
								$("#faq_input_title").val(questionTitle);
								$("#header").toggle();

							$("#faq_ask_now").toggle();
							$("#faq_ask_now_full").toggle();
							}else{
								bootbox.alert("Câu hỏi chứa tối thiểu 8 ký tự!");
							}
						}



					});
					$(document).on("keypress", "#faq_question_title",
							function(event) {

								if (event.which == 13) {

									$("#faq_btn_ask_now").click();
								}
							});
					$(document).on("click", ".faq_tmp_tag",
							function() {
						var currentTag=$(this).attr("tag");
						tags.pop(currentTag);

					});

					$("#faq_input_bonus_range").change(function() {
						$("#faq_input_bonus").val($(this).val());
					});
					$("#faq_input_bonus").change(function() {
						$("#faq_input_bonus_range").val($(this).val());
					});


					function changeTags(newTag, tagId) {
						var tempTag = "";
						newTag = newTag.trim();
						newTag = newTag.toLowerCase();
						if (tags.indexOf(newTag) == -1) {
							$("#faq_key_word").val("");
							tags.push(newTag);
							tags.forEach(function(tag) {
								if(tempTag!=""){
								 tempTag=tempTag+","+tag;
								}else{
								 tempTag=tag;
								}
							});
							$("#faq_key_word").val("");
							$("#faq_key_word").val(tempTag);
						}
						
						
					}
					// seach
					$('#faq_key_word').autocomplete(
							{
								serviceUrl : basePath + '/search/find-tag',
								onSelect : function(suggestion) {
									changeTags(suggestion.value.split("::")[0]
											.trim(), suggestion.data);
								}
							});
					
					// submit
					$("#faq_btn_ask_now_full_submit")
							.click(
									function() {
										var btn = $(this);
										btn.button('loading');
										var title = $("#faq_input_title").val();
										var listImg = "";
										var btnDeletes = $(".faq_btn_preview_delete");
										btnDeletes.each(function(e) {
											listImg = listImg + ","
													+ $(this).attr("media");
										});
										var content = FaqEdittor.getContent();
										var subject = $("#faq_question_subject")
												.val();
										$.cookie('faqLastSubject', subject);
										var listTag = $("#faq_key_word").val();
										var bonus = $("#faq_input_bonus").val();
										if (title.trim().length >= 8
												&& content.trim().length >= 15
												&& listTag.trim().length > 0
												&& subject != "-1") {
											$
													.ajax({
														url : basePath
																+ "/question/save-ask-now",
														type : "POST",
														dataType : "html",
														data : {
															title : title,
															listImg : listImg,
															content : content,
															subject : subject,
															listTag : listTag,
															bonus : bonus
														}, // The data your
															// sending to page
														success : function(html) {
															$(
																	"#faq_postswrapper")
																	.prepend(
																			html);
															$(
																	"#faq_btn_ask_now_full_cancel")
																	.click();
															$(
																	$(".faq_question")[0])
																	.faqFadeHighlight();
															btn.button('reset');


															$("#faq_input_bonus_range").val(0);
															$("#faq_input_bonus").val(0);
															$("#faq_question_title").val("");
															$("#faq_input_title").val("");
															$(".faq_tmp_tag").remove();
															tags=[];
															var listImg = "";

															$("#wmd-input-answer").val("");
															$("#wmd-preview-answer").html("");
															$("#faq_question_subject")
																	.val("-1");
															$("#faq_key_word").html("");
															$(".faq-preview-upfile").remove();
															$("#topcontrol").click();


														},
														error : function() {
															console
																	.log("AJAX request was a failure");
														}
													});
											return;

										}
										$message="";
										if (title.trim().length < 8) {
											$message=$message+"Tiêu đề câu hỏi chứa tối thiểu 8 ký tự<br>";
											bootbox.alert($message);
											btn.button('reset');
											return;
										}

										if (content.trim().length < 15) {
											$message=$message+"Nội câu hỏi chứa tối thiểu 15 ký tự<br>";
											bootbox.alert($message);
											btn.button('reset');
											return;
										}


										if (subject == "-1") {
											$message=$message+"Chọn chủ đề cho câu hỏi<br>";
											bootbox.alert($message);
											btn.button('reset');
											return;

										}
										if (listTag.trim().length <= 0) {
											$message=$message+"Câu hỏi phải có tối thiểu 1 từ khóa<br>";
											bootbox.alert($message);
											btn.button('reset');
											return;
										}
									});
				});