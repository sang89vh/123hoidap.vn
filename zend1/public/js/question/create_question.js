
$(document).ready(
		function() {

			// add class css bootrap
			$("div.form-group label").addClass("control-label");
			$("form.form-horizontal div.form-group label").addClass(
					"col-lg-2 control-label");
			$("form").attr("role", "form");
			// load list subject when init page question/create
			$.ajax({
				url : basePath + "/question/select-subject",
				type : "POST",
				dataType : "html",
				success : function(data) {
					$("#faq_step_select_subject").html(data);
				},
				error : function() {
					console.log("AJAX request was a failure");
				}
			});
			// add dropdown button
			$(function() {
				$('.dropdown-toggle').dropdown();
			});
			// action select subject

			$(document).on("click", ".faq_img_subject", function(e) {
				subject = $(this).attr("subject");
				console.log("subject=>" + subject);
				$('#faq_create_question_wizard').wizard("next", function(e) {
					e.preventDefault();

				});

			});

			// INITIALIZE WIZARD
			$('#faq_create_question_wizard').on('change', function(e, data) {
				console.log('faq_create_question_wizard click');
				if (data.step === 1 && data.direction == 'next') {
					// 1->2
					console.log("1->2");

					if (!isEmptyOrNull(subject)) {
						$.ajax({
//							async:false,
							url : basePath + "/question/content-question",
							type : "POST",
							dataType : "html",
							data : {subject:subject},
							success : function(data) {

								$("#faq_step_content_question").html(data);
//								$(".faq_txt_content_question").html(contentQuestion);
								setTinyEdittor();
								if(tags!=null){
									var faq_key_word=tags.split(",");
									for (var i_faq_tag in faq_key_word) {
										$('#faq_txt_key_word').tagsinput('add',faq_key_word[i_faq_tag]);
									}

//								 $("#faq_txt_title").val(title);

//								 tinymce.activeEditor.setContent(contentQuestion);
//								 tinyMCE.get('.faq_txt_content_question').setContent(contentQuestion);
								}


							},
							error : function() {
								console.log("AJAX request was a failure");

							}
						});
					} else {
						bootbox.alert("Hãy chọn chủ đề của câu hỏi!");

						e.preventDefault();

					}

				} else if (data.step === 2 && data.direction == 'next') {
					// 2->3
					console.log("2->3 line 1");
					 tags = $('#faq_txt_key_word').val();
					 title=$("#faq_txt_title").val();
//					 contentQuestion=tinyMCE.get('faq_txt_content_question').getContent();
					 contentQuestion=FaqEdittor.getContent('TINY');
//					if (tags.length != 0&&title.length>=50&&contentQuestion.length>=50) {
						if (title.trim().length>=20&&contentQuestion.trim().length>=50) {
							console.log("2->3 line 2");
					$.ajax({
//						async:false,
						url : basePath + "/question/finish-question",
						type : "POST",
						dataType : "html",
						data : {
							subject : subject,
							title:title,
							tags:tags,
							contentQuestion:contentQuestion

						},
						success : function(data) {
							console.log("2->3 line 3");

							$("#faq_step_finish_question").html(data);
						},
						error : function() {
							console.log("AJAX request was a failure");
						}
					});
					} else {
						console.log("2->3 line 1");
//						var errorMessage="";
						$("#faq_help_title").css("display","none");
						$("#faq_help_content").css("display","none");
						$("#faq_help_tag").css("display","none");
						if(title.trim().length<20){
//							errorMessage=errorMessage+"Tiêu đề câu hỏi chứa tối thiểu 20 ký tự<br/>";
							$("#faq_help_title").text("Tiêu đề câu hỏi chứa tối thiểu 20 ký tự");
							$("#faq_help_title").css("display","block");
							$("#faq_help_title").removeClass("help-block");
						}
						if(contentQuestion.trim().length<50){
							$("#faq_help_content").text("Nội dung câu hỏi chứa tối thiểu 50 ký tự");
							$("#faq_help_content").css("display","block");
							$("#faq_help_content").removeClass("help-block");
//							errorMessage=errorMessage+"Nội dung câu hỏi chứa tối thiểu 50 ký tự<br/>";
						}
						if(tags.trim().length<1){
							$("#faq_help_tag").text("Câu hỏi phải chứa tối thiểu 1 từ khóa");
							$("#faq_help_tag").css("display","block");
							$("#faq_help_tag").removeClass("help-block");
//							errorMessage=errorMessage+"Câu hỏi phải chứa tối thiểu 1 từ khóa";
						}
						//bootbox.alert(errorMessage);
						e.preventDefault();

					}

				} else if (data.step === 3 && data.direction == 'previous') {
					// 3->2

				} else if (data.step === 2 && data.direction == 'previous') {
					// 2->1
				}
			});
			//
			$('#faq_create_question_wizard').on('finished', function(e, data) {
				  tags=$('#faq_label_key_word').val();
				  var bonusPoint=$("#faq_txt_bonus_point").val();
			        var btn = $('#faq_btn_next');
			        btn.button('loading');
			        $.ajax({
//			        	async:false,
						url : basePath + "/question/save-question",
						type : "POST",
						dataType : "json",
						data : {bonusPoint:bonusPoint,tags:tags}, // The data your sending
						// to page
						success : function(data) {
							if(data.status===1){
								btn.button('reset');
							}
							bootbox.dialog({
								  message: "Cập nhật dữ liệu thành công!",
								  title: "Thông báo",
								  buttons: {
								    success: {
								      label: "Ok",
								      className: "btn-success",
								      callback: function() {
								        window.location.href=basePath + "/question/#overview";
								      }
								    }

								  }
								});
							$('#faq_create_question_wizard').wizard("previous", function(e) {

							});

						},
						error : function() {
							bootbox.alert("Lưu không thành công!");
							btn.button('reset');
							console.log("AJAX request was a failure");
						}
					});



//				$("#faq_btn_next").click();

			});
			//
			$('#faq_create_question_wizard').on('stepclick', function(e, data) {
				console.log('step' + data.step + ' clicked');
				if (data.step === 1) {
					// return e.preventDefault();
				}
			});



		});// end document.ready

