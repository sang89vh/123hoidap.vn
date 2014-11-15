var ispostToFace=true;
var answerIDEditWiki="";
function updateTotalLike(answerID,data){

	if($("span.faq_dislike_answer[value="+answerID+"]").attr('active')=='1'){
		$("span.faq_dislike_answer[value="+answerID+"]").attr('active','0');
	}
	//update total
	if(!faquser.isPrivilegeByPoint(ESTABLISHED_USER)){
	$("span.faq_total_point_answer[value="+answerID+"]").html(data.totalPoint>0?"+"+data.totalPoint:data.totalPoint);
	}else{
     $("#faq_total_point_like_answer[value="+answerID+"]").html("+"+data.toatlLike);
     $("#faq_total_point_dislike_answer[value="+answerID+"]").html("-"+data.totalDislike);
	}

}

function updateTotalDislike(answerID,data){

	if($("span.faq_like_answer[value="+answerID+"]").attr('active')=='1'){
		$("span.faq_like_answer[value="+answerID+"]").attr('active','0');
	}


	//update total
	if(!faquser.isPrivilegeByPoint(ESTABLISHED_USER)){
	$("span.faq_total_point_answer[value="+answerID+"]").html(data.totalPoint>0?"+"+data.totalPoint:data.totalPoint);
	}else{
	$("#faq_total_point_like_answer[value="+answerID+"]").html("+"+data.toatlLike);
	$("#faq_total_point_dislike_answer[value="+answerID+"]").html("-"+data.totalDislike);
	}

}




function addEditorAnswer(){
	faqAnswerEdittor=tinymce.init({
	    selector: ".faq_txt_content_answer",
	    plugins: [
	        "advlist autolink lists link image charmap print preview anchor",
	        "searchreplace visualblocks code fullscreen",
	        "insertdatetime media table contextmenu paste jbimages moxiemanager"


	    ],
		language : 'vi_VN',
		height : 300,
	    // ===========================================
	    // PUT PLUGIN'S BUTTON on the toolbar
	    // ===========================================
	    toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image jbimages",
	    // ===========================================
	    // SET RELATIVE_URLS to FALSE (This is required for images to display properly)
	    // ===========================================
	    relative_urls: false
	});
}

function alertQuestionClosed(){

		bootbox.alert("Câu hỏi đang đóng không thể thực hiện thao tác này!");


}
$(document).ready(function(){
	showPreview();
	if (typeof subject_select===undefined){
	$("ul.navbar-nav li[subject='"+subject_select+"']").addClass("active");
    }
	// convert time to string
	settimeUpdate();
	// add new reply
	var dataComment=null;
	var boxCommnet=$("#faq_box_reply_question").html();
	$(".faq_btn_reply_comment").click(function(){

			if(!isAllowed){ redirectLogin();  return;}
			if(isQuestionClosed){ alertQuestionClosed();  return;}
		$(".faq_box_reply").html("");
		$(".postReplies_temp").remove();
		dataComment=$(this).attr('faq-data-comment');
		//div class=action_links fsm
		var answerId=dataComment.split(",")[1];
		var typeAnswer=dataComment.split(",")[0];
		if(typeAnswer=="COMMENT"){
			$("#faq_show_hidden_comment2").click();
		}else if (typeAnswer=="REPLY1") {
			$("#faq_show_hidden_comment3").click();

		}
   	   $(".faq_feekback_replies[answer='"+answerId+"']").prepend('<div class="postReplies postReplies_temp"><div><ul class="faq_feekback_replies"><li class="faq_feekback"><div class="clearfix">'+boxCommnet+'</div></li></ul></div></div>');
	});
	// add new answer level 2
	$(document).on('click',".faq_btn_send_reply",function(){

			if(!isAllowed){ redirectLogin();  return;}
			if(isQuestionClosed){ alertQuestionClosed();  return;}
		if(!faquser.isPrivilegeByPoint(CREATE_POSTS)){
			bootbox.alert("Điểm câu hỏi yêu cầu 1đ!");
			return ;
		}else if (!faquser.isPrivilegeByPoint(REMOVE_NEW_USER_RESTRICTIONS&&isProtected)) {
			bootbox.alert("Điểm câu hỏi yêu cầu 10đ để trả lời câu hỏi [Bảo vệ]!");
			return ;
		}
		var textAreComment=$("textarea",$(".postReplies_temp"));;
		var contentComment= textAreComment.val();
		console.log(contentComment);
		console.log(contentComment.length);
		if(contentComment.trim().length<10){

			$(this).parent().before('<div class="alert alert-danger fade in col-md-12 col-lg-12 col-xs-12 col-sm-12"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Ý kiến phản hồi của bạn chứa tối thiểu 10 ký tự</div>');
			return ;
			}else{
				$(".alert-danger").remove();
			}
        textAreComment.val("Đang lưu...");
//	    var divdata=$(this).parent().parent().parent();//div class= clearfix
		 $.ajax({
	         url: basePath+"/answer/answer",
	         type: "POST",
	         dataType:"html",
	         data: {question:questionID,
	        	 dataComment:dataComment,
	        	 content:contentComment
	        	 },
	         success: function(data){
	           $(".postReplies_temp").remove();
	           var answerId=dataComment.split(",")[1];
	      	   $(".faq_feekback_replies[answer='"+answerId+"']").append(data);
	        	 settimeUpdate();
	        	 var spantotalcomment=$("#faq_total_comment_question");
	        	 $(spantotalcomment).text(1+parseInt($(spantotalcomment).text()));

	        	 if(ispostToFace){
		        	 postToFacebook('Đã bình luận câu hỏi',"123hoidap.vn-Mọi câu hỏi đều có câu trả lời.",$('#faq_question_info_title').text(),location.href,'http://'+domain+"/images/logo/logo_qapolo.png",'chủ đề: '+$('.faq_label_subject').children().children().text());
		        	 }
	        	//node.js notify
	        	 var dataNotfy =JSON.stringify({replyId:answerId , html:data});
	        	chat_utils.noticeChangePageContent(dataNotfy);

	         },
	         error:function(){
	             console.log("AJAX request was a failure");
	         }
	       });
		});
	$(document).on('click',".faq_post_to_face",function(){
		ispostToFace=$(this).is(':checked');
		console.log("===>ispostToFace"+ispostToFace);
	});
// add new answer level 1
	$(document).on('click',".faq_btn_send_comment",function(){

			if(!isAllowed){ redirectLogin();  return;}
			if(isQuestionClosed){ alertQuestionClosed();  return;}
		if(!faquser.isPrivilegeByPoint(CREATE_POSTS)){
			bootbox.alert("Điểm câu hỏi yêu cầu 1đ!");
			return ;
		}else if (!faquser.isPrivilegeByPoint(REMOVE_NEW_USER_RESTRICTIONS&&isProtected)) {
			bootbox.alert("Điểm câu hỏi yêu cầu 10đ để trả lời câu hỏi [Bảo vệ]!");
			return ;
		}
		dataComment='ANSWER,';
		var textAreComment=null;
		if(type_editor=='TINY'){
		 textAreComment=$(this).parent().parent().children().children().children().children();
		}
		var contentComment=FaqEdittor.getContent(type_editor);
		if(contentComment.trim().length<10){

			$(this).parent().before('<div class="alert alert-danger fade in col-md-12 col-lg-12 col-xs-12 col-sm-12"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Ý kiến phản hồi của bạn chứa tối thiểu 10 ký tự</div>');
			return ;
			}else{
				$(".alert-danger").remove();
			}
		var isWikiPost=false;
		if(faquser.isPrivilegeByPoint(CREATE_WIKI_POSTS)){
			isWikiPost=$(".faq_wiki_post").is(':checked');
		}
        var divdata=$("#faq_comment_question");
	    $(".faq_btn_send_comment").button('loading');
	    console.log("Đang lưu...");
	    FaqEdittor.setContent(type_editor, 'Đang lưu...');
	    var dataPost= {
	    			          	 question:questionID,
	    			          	 dataComment:dataComment,
	    			          	 content:contentComment,
	    						wikiPost:isWikiPost
	    			          	 };
		 $.ajax({
	         url: basePath+"/answer/answer",
	         type: "POST",
	         dataType:"html",
	         data:dataPost, //The data your sending to page
	          success: function(data){
	      	   $(divdata).append(data);
	        	 settimeUpdate();
	        	 var spantotalcomment=$("#faq_total_comment_question");
	        	 $(spantotalcomment).text(1+parseInt($(spantotalcomment).text()));
	        	 if(type_editor=='TINY'){
	 	     	    textAreComment.val("");
	 	     	   tinyMCE.remove(".faq_txt_content_answer");
	        		}
	        	 FaqEdittor.setContent(type_editor, '');
	        	 $(".faq_btn_send_comment").button('reset');
	        	 $("#wmd-input-answer").val("");
                 //post comment to face


	        	 if(ispostToFace){
	        		 postToFacebook('Đã bình luận câu hỏi',"123hoidap.vn-Mọi câu hỏi đều có câu trả lời.",$('#faq_question_info_title').text(),location.href,'http://'+domain+"/images/logo/logo_qapolo.png",'chủ đề: '+$('.faq_label_subject').children().children().text());
	        	 }
	        	//node.js notify
	        	 	        	 var dataNotfy =JSON.stringify({html:data});
	        	 	        	 chat_utils.noticeChangePageContent(dataNotfy);

	         },
	         error:function(){
	             console.log("AJAX request was a failure");
	         }
	       });
		});
//show hidden comment
	$("#faq_show_hidden_comment2").click(function(){
		$('.comment_level2').toggle();
		$("#faq_show_hidden_comment3").toggle();
	});
	$('.comment_level3').toggle();
	$("#faq_show_hidden_comment3").click(function(){

		$('.comment_level3').toggle();
	});

	// follow question

	$("#faq_question_icon_follow").click(function(){
		$("#faq_question_follow").click();
	});
	$("#faq_question_follow").click(function(){

			if(!isAllowed){ redirectLogin();  return;}
			if(isQuestionClosed){ alertQuestionClosed();  return;}
		if($("#faq_question_follow").attr('action')=="UNFOLLOW"&&($("#faq_question_follow").text()!="Đang lưu...")){
			console.log("huy theo doi");
			$("#faq_question_follow").text("Đang lưu...");
			$("#faq_question_icon_follow").removeClass("faq_active_follow");
			$.ajax({
		         url: basePath+"/question/unfollow",
		         type: "POST",
		         dataType:"json",
		         data: "question=" + questionID, //The data your sending to page
		         success: function(data){
		        	 if(data.status==1){
		        		 $("#faq_question_follow").text("#Theo dõi");
		        		 $("#faq_question_follow").attr('action',"FOLLOW");
		        		var faq_total_follow_question=$("#faq_total_follow_question").text();
		        		$("#faq_total_follow_question").text(--faq_total_follow_question);

		        	 }
		         },
		         error:function(){
		        	 $("#faq_question_follow").text("Lỗi!");
		             console.log("AJAX request was a failure");
		             $("#faq_question_icon_follow").addClass("faq_active_follow");
		         }
		       });

		}else if($("#faq_question_follow").attr('action')=="FOLLOW"&&($("#faq_question_follow").text()!="Đang lưu...")){
			console.log("theo doi");
			$("#faq_question_follow").text("Đang lưu...");
			$("#faq_question_icon_follow").addClass("faq_active_follow");
			$.ajax({
		         url: basePath+"/question/follow",
		         type: "POST",
		         dataType:"json",
		         data: "question=" + questionID, //The data your sending to page
		         success: function(data){
		        	 if(data.status==1){
		        		 $("#faq_question_follow").text("#Hủy theo dõi");
		        		 $("#faq_question_follow").attr('action',"UNFOLLOW");
		        		var faq_total_follow_question=$("#faq_total_follow_question").text();
		        		$("#faq_total_follow_question").text(++faq_total_follow_question);

		        	 }else{
							$("#faq_question_icon_follow").removeClass("faq_active_follow");
							bootbox.alert("Có lỗi hệ thống!");
						}
		         },
		         error:function(){
		        	 $("#faq_question_follow").text("Lỗi!");
		             console.log("AJAX request was a failure");
		             $("#faq_question_icon_follow").removeClass("faq_active_follow");
		         }
		       });
		}

	});

	// add spam for question
	// add spam answer
	$(document).on("click",".faq_btn_answer_spam",function(){
		var answerSpamID=$(this).attr("answer");
		var typeAnswerSpam=$('input[name=faq_option_answer_spam]:checked').val();
		console.log(typeAnswerSpam);
		btn=this;
		$(btn).button("loading");
//		console.log(answerSpamID);
//		console.log(questionID);
		$.ajax({
	         url: basePath+"/answer/spam",
	         type: "POST",
	         dataType:"json",
	         data: {
	        	 question:questionID,
	        	 answer:answerSpamID,
	        	 typespam:typeAnswerSpam
	        	 },
	         success: function(data){
	        	 if(data.status==1){
	        		 $(".faq_btn_spam_answer[answer="+answerSpamID+"]").smallipop('destroy');
		        	 $(".faq_btn_spam_answer[answer="+answerSpamID+"]").html('<span class="glyphicon glyphicon-warning-sign"></span>Đã báo');
	        	 }else if (data.status==0) {
						bootbox.alert("có lỗi xẩy ra!");
						 $(".faq_btn_spam_answer[answer="+answerSpamID+"]").smallipop('hide');
			        	 $(".faq_btn_spam_answer[answer="+answerSpamID+"]").html('<span class="glyphicon glyphicon-warning-sign"></span>Lỗi!');
					}else if (data.status==5) {
						bootbox.alert("Điểm câu hỏi yêu cầu 15đ!");
						 $(".faq_btn_spam_answer[answer="+answerSpamID+"]").smallipop('hide');
				}else if (data.status==3) {
					bootbox.alert("Bạn đã báo vi phạm trước đó!");
					 $(".faq_btn_spam_answer[answer="+answerSpamID+"]").smallipop('hide');
			}
	        	 $(btn).button('reset');


	         },
	         error:function(){
	             console.log("AJAX request was a failure");
	             $(btn).button('reset');
	             $(".faq_btn_spam_answer[answer="+answerSpamID+"]").smallipop('hide');
	        	 $(".faq_btn_spam_answer[answer="+answerSpamID+"]").html('<span class="glyphicon glyphicon-warning-sign"></span>Lỗi!');
	         }
	       });
	});
$(document).on("click","#faq_btn_question_spam",function(){

			if(!isAllowed){ redirectLogin();  return;}
			if(isQuestionClosed){ alertQuestionClosed();  return;}
		if(!faquser.isPrivilegeByPoint(FLAG_POSTS)){
			bootbox.alert("Điểm câu hỏi yêu cầu 15đ");
			return;
		}
		var btn=$(this);
//		if($("#faq_question_spam").attr('action')=="UNSPAM"&&($("#faq_question_spam").text()!="Đang lưu...")){
//			console.log("bao k vi pham");
//			$("#faq_question_spam").text("Đang lưu...");
//			btn.button("loading");
//			$.ajax({
//		         url: basePath+"/question/unspam",
//		         type: "POST",
//		         dataType:"json",
//		         data: "question=" + questionID, //The data your sending to page
//		         success: function(data){
//		        	 if(data.status==1){
//		        		 $("#faq_question_spam").text("#Báo vi phạm");
//		        		 btn.button("reset");
//		        		 $("#faq_question_spam").attr('action',"SPAM");
//		        		var faq_total_spam_question=$("#faq_total_spam_question").text();
//		        		$("#faq_total_spam_question").text(--faq_total_spam_question);
//		        	 }
//		         },
//		         error:function(){
//		        	 $("#faq_question_spam").text("Lỗi!");
//		        	 btn.button("reset");
//		             console.log("AJAX request was a failure");
//		         }
//		       });
//
//		}else
			if($("#faq_question_spam").attr('action')=="SPAM"&&($("#faq_question_spam").text()!="Đang lưu...")){
			console.log("bao vi phạm");
			$("#faq_question_spam").text("Đang lưu...");
			 btn.button("loading");
			 var typespam=$('input[name=faq_option_spam]:checked').val();
			 console.log("tyoe: spam:=>"+typespam);
			$.ajax({
		         url: basePath+"/question/spam",
		         type: "POST",
		         dataType:"json",
		         data: {
		        	   question: questionID,
		        	   typespam:typespam
		        	   }, //The data your sending to page
		         success: function(data){
		        	 if(data.status==1){
		        		 $("#faq_question_spam").text("#Đã báo vi phạm");
		        		 btn.button("reset");
		        		 $("#faq_question_spam").attr('action',"UNSPAM");
		        		var faq_total_spam_question=$("#faq_total_spam_question").text();
		        		$("#faq_total_spam_question").text(++faq_total_spam_question);
		        		$("#faq_question_spam").smallipop('destroy');
		        	 }else if (data.status==5) {
		        		 $("#faq_question_spam").text("Lỗi!");
			        	 btn.button("reset");
							bootbox.alert("Bạn đã hết lượt báo vi phạm trong ngày!");
						}else if (data.status==4) {
							$("#faq_question_spam").text("Lỗi!");
				        	 btn.button("reset");
								bootbox.alert("Điểm câu hỏi yêu cầu 15đ");
							}
		         },
		         error:function(){
		        	 $("#faq_question_spam").text("Lỗi!");
		        	 btn.button("reset");
		             console.log("AJAX request was a failure");
		         }
		       });
		}

	});

	$(".faq_txt_content_answer").click(function(){
		addEditorAnswer();
	});

	$(".faq_like_answer").click(function(){

			if(!isAllowed){ redirectLogin();  return;}
			if(isQuestionClosed){ alertQuestionClosed();  return;}
		if(!faquser.isPrivilegeByPoint(VOTE_UP)){
			bootbox.alert("Điểm câu hỏi yêu cầu 15đ!");
			return ;
		}
		var faq_like_answer=this;
		var active=$(this).attr('active');
		if(active==1){
			return;
		}
		var answerID=$(this).attr('value');
		$(this).addClass('faq_active_like_dislike');
		$("span.faq_dislike_answer[value="+answerID+"]").removeClass('faq_active_like_dislike');

		$.ajax({
			url : basePath + "/answer/like",
			type : "POST",
			dataType : "json",
			data : {question:questionID,
	                answer:answerID
				},
			success : function(data) {
				if(data.status==1){
					updateTotalLike(answerID,data);
				}else if (data.status==2) {
					$(faq_like_answer).removeClass('faq_active_like_dislike');
					bootbox.alert("Bạn cần đăng nhập trước!");
		       }else if (data.status==3) {
		    	   $(faq_like_answer).removeClass('faq_active_like_dislike');
					bootbox.alert("Điểm câu hỏi yêu cầu 15đ!");
				}else if (data.status==4) {
					$(faq_like_answer).removeClass('faq_active_like_dislike');
					bootbox.alert("Không thể vote cho chính câu trả lời của bạn!");
				}else if (data.status==5) {
					$(faq_like_answer).removeClass('faq_active_like_dislike');
					bootbox.alert("Bạn đã hết lượt vote trong ngày!");
				}else {
					$(faq_like_answer).removeClass('faq_active_like_dislike');
			}


				$(faq_like_answer).attr('active','1');


			},
			error : function() {
				$(faq_like_answer).removeClass('faq_active_like_dislike');
				console.log("AJAX request was a failure");

			}
		});
		});
	$(".faq_dislike_answer").click(function(){

			if(!isAllowed){ redirectLogin();  return;}
			if(isQuestionClosed){ alertQuestionClosed();  return;}
		if(!faquser.isPrivilegeByPoint(VOTE_DOWN)){
			bootbox.alert("Điểm câu hỏi yêu cầu 125đ!");
			return ;
		}
		var faq_dislike_answer=this;
		var active=$(this).attr('active');
		if(active==1){
			return;
		}
		$(this).addClass('faq_active_like_dislike');
		var answerID=$(this).attr('value');
		$("span.faq_like_answer[value="+answerID+"]").removeClass('faq_active_like_dislike');
		$.ajax({
			url : basePath + "/answer/dislike",
			type : "POST",
			dataType : "json",
			data : {question:questionID,
	                answer:answerID
				},
			success : function(data) {
				if(data.status==1){
					updateTotalDislike(answerID,data);
				}else if (data.status==2) {
					$(faq_dislike_answer).removeClass('faq_active_like_dislike');
					bootbox.alert("Bạn cần đăng nhập trước!");
		       }else if (data.status==3) {
		    	   $(faq_dislike_answer).removeClass('faq_active_like_dislike');
					bootbox.alert("Điểm câu hỏi yêu cầu 125đ!");
				}else if (data.status==4) {
					$(faq_dislike_answer).removeClass('faq_active_like_dislike');
					bootbox.alert("Không thể vote cho chính câu hỏi của bạn!");
				}else if (data.status==5) {
					$(faq_dislike_answer).removeClass('faq_active_like_dislike');
					bootbox.alert("Bạn đã hết lượt vote trong ngày!");
				}else {
					$(faq_dislike_answer).removeClass('faq_active_like_dislike');
			}
				$(faq_dislike_answer).attr('active','1');


			},
			error : function() {
				$(faq_dislike_answer).removeClass('faq_active_like_dislike');
				console.log("AJAX request was a failure");

			}
		});


	});

	$("#faq_like_question").click(function(){

			if(!isAllowed){ redirectLogin();  return;}
			if(isQuestionClosed){ alertQuestionClosed();  return;}
		if(!faquser.isPrivilegeByPoint(VOTE_UP)){
			bootbox.alert("Điểm câu hỏi yêu cầu 15đ!");
			return ;
		}
		var faq_like_question=this;
		var active=$(this).attr('active');
		if(active==1){
			return;
		}
		$(this).addClass('faq_active_like_dislike');


		$.ajax({
			url : basePath + "/question/like",
			type : "POST",
			dataType : "json",
			data : {
					question:questionID
				},
			success : function(data) {
				if(data.status==1&&!faquser.isPrivilegeByPoint(ESTABLISHED_USER)){


					 if($("#faq_dislike_question").attr('active')==1){
							$("#faq_dislike_question").attr('active',0);
						}
							$("#faq_total_point_question").text(data.totalPoint>0?"+"+data.totalPoint:data.totalPoint);

						$(faq_like_question).attr('active','1');
						$("#faq_dislike_question").removeClass('faq_active_like_dislike');
				  }else if (data.status==1&&faquser.isPrivilegeByPoint(ESTABLISHED_USER)){
					  if($("#faq_dislike_question").attr('active')==1){
							$("#faq_dislike_question").attr('active',0);
							}

					        $("#faq_total_point_like_question").text("+"+data.toatlLike);
					        $("#faq_total_point_dislike_question").text("-"+data.totalDislike);

							$(faq_like_question).attr('active','1');
							$("#faq_dislike_question").removeClass('faq_active_like_dislike');
				  }else if (data.status==2) {
						$(faq_like_question).removeClass('faq_active_like_dislike');
						bootbox.alert("Bạn cần đăng nhập trước!");
			       }else if (data.status==3) {
						$(faq_like_question).removeClass('faq_active_like_dislike');
						bootbox.alert("Điểm câu hỏi yêu cầu 15đ!");
					}else if (data.status==4) {
						$(faq_like_question).removeClass('faq_active_like_dislike');
						bootbox.alert("Không thể vote cho chính câu hỏi của bạn!");
					}else if (data.status==5) {
						$(faq_like_question).removeClass('faq_active_like_dislike');
						bootbox.alert("Bạn đã hết lượt vote trong ngày!");
					}else {
						$(faq_like_question).removeClass('faq_active_like_dislike');
				}



			},
			error : function() {
				$(faq_like_question).removeClass('faq_active_like_dislike');
				console.log("AJAX request was a failure");

			}
		});
		});
	$("#faq_dislike_question").click(function(){

			if(!isAllowed){ redirectLogin();  return;}
			if(isQuestionClosed){ alertQuestionClosed();  return;}
		if(!faquser.isPrivilegeByPoint(VOTE_DOWN)){
			bootbox.alert("Điểm câu hỏi yêu cầu 125đ!");
			return ;
		}
		var faq_dislike_question=this;
		var active=$(this).attr('active');
		if(active==1){
			return;
		}
		$(this).addClass('faq_active_like_dislike');


		$.ajax({
			url : basePath + "/question/dislike",
			type : "POST",
			dataType : "json",
			data : {
				question:questionID
			},
			success : function(data) {
				if(data.status==1&&!faquser.isPrivilegeByPoint(ESTABLISHED_USER)){

				if($("#faq_like_question").attr('active')==1){
					$("#faq_like_question").attr('active',0);
				}
				$("#faq_total_point_question").text(data.totalPoint>0?"+"+data.totalPoint:data.totalPoint);

					$(faq_dislike_question).attr('active','1');
					$("#faq_like_question").removeClass('faq_active_like_dislike');
				}else if (data.status==1&&faquser.isPrivilegeByPoint(ESTABLISHED_USER)){
					if($("#faq_like_question").attr('active')==1){
						$("#faq_like_question").attr('active',0);
					}

					        $("#faq_total_point_like_question").text("+"+data.toatlLike);
					        $("#faq_total_point_dislike_question").text("-"+data.totalDislike);

					        $(faq_dislike_question).attr('active','1');
							$("#faq_like_question").removeClass('faq_active_like_dislike');
				  }else if (data.status==2) {
					$(faq_dislike_question).removeClass('faq_active_like_dislike');
					bootbox.alert("Bạn cần đăng nhập trước!");
		       }else if (data.status==3) {
		    	   $(faq_dislike_question).removeClass('faq_active_like_dislike');
					bootbox.alert("Điểm câu hỏi yêu cầu 125đ!");
				}else if (data.status==4) {
					$(faq_dislike_question).removeClass('faq_active_like_dislike');
					bootbox.alert("Không thể vote cho chính câu hỏi của bạn!");
				}else if (data.status==5) {
					$(faq_dislike_question).removeClass('faq_active_like_dislike');
					bootbox.alert("Bạn đã hết lượt vote trong ngày!");
				}else {
					$(faq_dislike_question).removeClass('faq_active_like_dislike');
				}



			},
			error : function() {
				$(faq_dislike_question).removeClass('faq_active_like_dislike');
				console.log("AJAX request was a failure");

			}
		});
	});

$(".faq_is_best_answer").click(function(){


			if(!isAllowed){ redirectLogin();  return;}
			if(isQuestionClosed){ alertQuestionClosed();  return;}
if($(this).attr('isbest')=='false'){

		$(this).addClass('faq_best_answer');
		var context=this;
		var answerID=$(this).attr('value');
		$.ajax({
			url : basePath + "/answer/best",
			type : "POST",
			dataType : "json",
			data : {question:questionID,
	                answer:answerID
				},
			success : function(data) {
				if(data.status==1){
					$(".faq_is_best_answer").removeClass('faq_best_answer');
					$(".faq_is_best_answer").attr('isbest','false');
					$(context).addClass('faq_best_answer');
					$(context).attr('isbest','true');
					$("#back2best").attr('href',"#"+answerID);

				}else if (data.status==2) {
					bootbox.alert("Không thể tự bình chọn câu trả lời của chính bạn là tốt nhất!");
					$(context).removeClass('faq_best_answer');
				}else if (data.status==3) {
					console.log("it is best before");
				}else if (data.status==4) {
					bootbox.alert("Không thể bình chọn lại câu trả lời tốt nhất!(Đã quá 15 ngày kể từ ngày chọn câu trả lời tốt nhất đầu tiên!)");
					$(context).removeClass('faq_best_answer');
				}


			},
			error : function() {
				$(this).removeClass('faq_best_answer');
				console.log("AJAX request was a failure");

			}
		});

	}else{
		console.log("it is best before");
	}
});

	//action delete, update question

	$(document).on('click','.faq_option_question li.dropdown ul.dropdown-menu li',function(){

	questionID=$(this).attr("value");
	actionID=$(this).attr("action");
	console.log(questionID);
	console.log(actionID);
	if(actionID=='DELETE'){
	bootbox.confirm("Bạn có muốn xóa?", function(result) {
		  if(result){
		  $.ajax({
		         url: basePath+"/question/delete",
		         type: "POST",
		         dataType:"json",
		         data: {question:questionID},
		         success: function(data){
		        	 if(data.status==1){
		        		 console.log("sucess delete question");
		        		 window.location = basePath+"/question/index";
		        	 }

		         },
		         error:function(){
		             console.log("AJAX request was a failure");
		         }
		       });
		  }

		});
	}else if(actionID=="EDIT_WIKISTYLE"){
		window.location =basePath+"/question/edit-wikistyle/"+questionID;
	}else if(actionID=="CLOSE_QUESTION"){
		bootbox.confirm("Bạn có muốn đóng câu hỏi?", function(result) {
			  if(result){
			  $.ajax({
			         url: basePath+"/question/close",
			         type: "POST",
			         dataType:"json",
			         data: {question:questionID},
			         success: function(data){
			        	 if(data.status==1){
			        	 $(".faq_status_question[value="+questionID+"]").text("[Đã đóng]");
			        	 }

			         },
			         error:function(){
			             console.log("AJAX request was a failure");
			         }
			       });
			  }

			});
	}

	});

	//hover faq_content show option
	$(document).on("mouseenter","#faq_question",function(){
		$('.faq_option_question_icon').addClass('faq_option_question_icon_hover');

	});
	$(document).on("mouseleave","#faq_question",function(){
		$('.faq_option_question_icon').removeClass('faq_option_question_icon_hover');

	});
	//TODO share popup
	if($.smallipop){
	$('#faq_question_share').attr('title','Đang xử lý....');
	$('#faq_question_share').smallipop({
		preferredPosition: 'top',
		hideOnTriggerClick: false,
		hideOnPopupClick:false,
		invertAnimation: true,
		hideDelay: 300,
		theme: 'blue',
		onBeforeShow: function(evt){
				var url = basePath  +'/question/share';
				var jTarget = $(evt[0]);
				var jContent = jTarget.attr("tooltip_content");
				if(!jContent || jContent.trim()==""){
					$.ajax({
						url: url,
						async: false,
					}).done(function(data){
						jTarget.attr('tooltip_content',data);
					});
				}
		},
		onAfterShow: function(evt){
				$.smallipop.setContent(evt,$(evt[0]).attr('tooltip_content'));
		}
	});
	}
	//TODO spam popup
	if($.smallipop){

		$('#faq_question_spam').attr('title','Đang xử lý.... ');
		$('#faq_question_spam').smallipop({
			preferredPosition: 'top',
			hideOnTriggerClick: false,
			hideOnPopupClick:false,
			invertAnimation: true,
			hideDelay: 300,
			theme: 'blue',
			onBeforeShow: function(evt){
				var jTarget = $(evt[0]);
			if(!isQuestionClosed){
				var url = basePath  +'/question/form-spam';
				var jContent = jTarget.attr("tooltip_content");
				if(!jContent || jContent.trim()==""){
					$.ajax({
						url: url,
						async: false,
					}).done(function(data){
						jTarget.attr('tooltip_content',data);
					});
				}
			}else{

				jTarget.attr('tooltip_content','Câu hỏi đã đóng');
			}
			},
			onAfterShow: function(evt){
				$.smallipop.setContent(evt,$(evt[0]).attr('tooltip_content'));
			}
		});

	}
	//TODO spam answer
	if($.smallipop){

		$('.faq_btn_spam_answer').attr('title','Đang xử lý....');
		$('.faq_btn_spam_answer').smallipop({
			preferredPosition: 'top',
			hideOnTriggerClick: false,
			hideOnPopupClick:false,
			invertAnimation: true,
			hideDelay: 300,
			theme: 'blue',
			onBeforeShow: function(evt){
				var jTarget = $(evt[0]);
				if(!isQuestionClosed){
				var url = basePath  +'/answer/form-spam';

				var answerID=jTarget.attr("answer");
				console.log(answerID);
//				var jContent=jTarget.attr("tooltip_content");
//				if(!jContent || jContent.trim()==""){
					$.ajax({
						url: url,
						async: false,
						data:{question:questionID,
							answer:answerID
						},
						type: "POST"
					}).done(function(data){
						jTarget.attr('tooltip_content',data);
					});
//				}
			}else{

				jTarget.attr('tooltip_content','Câu hỏi đã đóng');
			}
			},
			onAfterShow: function(evt){
				$.smallipop.setContent(evt,$(evt[0]).attr('tooltip_content'));
			}
		});


	}
// wiki edit
	$("#faq_question_wikiedit").click(function(){
		if(!isAllowed){
			redirectLogin();
            return ;
		}else{
	    if(isQuestionClosed){ alertQuestionClosed();  return;}
	    if(!faquser.isPrivilegeByPoint(EDIT_COMMUNITY_WIKI)){
	    	bootbox.alert("Điểm câu hỏi yêu cầu "+EDIT_COMMUNITY_WIKI+"đ");

	    	return;}
		location.href=basePath+"/question/edit-wikistyle/"+questionID;
		}
	});
//show highlight best answer
	$("#faq_link_best_answer").click(function(){
		var answerID=$(this).attr("href");
		$("li"+answerID).faqFadeHighlight();
	});
$("#back2best").click(function(){
	$("#faq_link_best_answer").click();
});
	//fix nav scroll
	$('#top-subject').fixTo('#right-content');

	//protect question
	$("#faq_question_protect_unprotect").click(function(){
		var statusProtect=$(this).attr("status");
		var btn=this;
		$(btn).button('loading');
		if(statusProtect=="PROTECTED"){
			$.ajax({
		         url: basePath+"/question/protect-question",
		         type: "POST",
		         dataType:"json",
		         data: {question:questionID},
		         success: function(data){
		        	 if(data.status==1){
		        	 $(".faq_status_question[value="+questionID+"]").text("[Bảo vệ]");
		        	 $(btn).button('reset');
		        	 $(btn).attr('status','UNPROTECTED');
		        	 $(btn).html('<span class="glyphicon glyphicon-lock" ></span>Bỏ bảo vệ');
		        	 }else if (data.status==0) {
							bootbox.alert("có lỗi xẩy ra!");
						}else if (data.status==3) {
							bootbox.alert("Điểm câu hỏi yêu cầu 15,000đ!");
						}

		         },
		         error:function(){
		             console.log("AJAX request was a failure");
		             $(btn).button('reset');
		         }
		       });
		}else if (statusProtect=="UNPROTECTED") {
			$.ajax({
		         url: basePath+"/question/unprotect-question",
		         type: "POST",
		         dataType:"json",
		         data: {question:questionID},
		         success: function(data){
		        	 if(data.status==1){
		        	 $(".faq_status_question[value="+questionID+"]").text("");
		        	 $(btn).button('reset');
		        	 $(btn).attr('status','PROTECTED');
		        	 $(btn).html('<span class="glyphicon glyphicon-lock" ></span>Bảo vệ');
		        	 }else if (data.status==0) {
						bootbox.alert("có lỗi xẩy ra!");
					}else if (data.status==3) {
						bootbox.alert("Điểm câu hỏi yêu cầu 15,000đ!");
					}else if (data.status==4) {
						bootbox.alert("Bạn không phải là người [Bảo vệ] câu hỏi này!");
					}

		         },
		         error:function(){
		             console.log("AJAX request was a failure");
		             $(btn).button('reset');
		         }
		       });

		}
	});

	//CLOSE AND REOPEN QUESTION
	$("#faq_question_close_reopen").click(function(){
		var statusProtect=$(this).attr("status");
		var btn=this;
		$(btn).button('loading');
		if(statusProtect=="CLOSE"){
			$.ajax({
				url: basePath+"/question/close-question",
				type: "POST",
				dataType:"json",
				data: {question:questionID},
				success: function(data){
					$(btn).button('reset');
					if(data.status==1){
						$(".faq_status_question[value="+questionID+"]").text("[Đã đóng]");

						$(btn).attr('status','REOPEN');
						$("#faq_question_close_reopen").html('<span class="glyphicon glyphicon-trash" ></span>Mở câu hỏi');
						isQuestionClosed=true;
					}else if (data.status==0) {
						bootbox.alert("có lỗi xẩy ra!");
					}else if (data.status==3) {
						bootbox.alert("Điểm câu hỏi yêu cầu 3,000đ!");
					}else if (data.status==4) {
						bootbox.alert("Đã đóng câu hỏi trước đó");
					}


				},
				error:function(){
					console.log("AJAX request was a failure");
					$(btn).button('reset');
				}
			});
		}else if (statusProtect=="REOPEN") {
			$.ajax({
				url: basePath+"/question/reopen-question",
				type: "POST",
				dataType:"json",
				data: {question:questionID},
				success: function(data){
					$(btn).button('reset');
					if(data.status==1){
						$(".faq_status_question[value="+questionID+"]").text("");

						$(btn).attr('status','CLOSE');
						$("#faq_question_close_reopen").html('<span class="glyphicon glyphicon-trash" ></span>Đóng câu hỏi');
						isQuestionClosed=false;
					}else if (data.status==0) {
						bootbox.alert("có lỗi xẩy ra!");
					}else if (data.status==3) {
						bootbox.alert("Điểm câu hỏi yêu cầu 3,000đ!");
					}else if (data.status==4) {
						bootbox.alert("Bạn không phải là người [Đã đóng] câu hỏi này!");
					}


				},
				error:function(){
					console.log("AJAX request was a failure");
					$(btn).button('reset');
				}
			});

		}
	});
	//highlight QUESTION
	$("#faq_btn_highlight").click(function(){
		var statusProtect=$(this).attr("status");
		var btn=this;
		$(btn).button('loading');
		if(statusProtect=="HIGHLIGHT"){
			$.ajax({
				url: basePath+"/question/highlight-question",
				type: "POST",
				dataType:"json",
				data: {question:questionID},
				success: function(data){
					$(btn).button('reset');
					if(data.status==1){


						$(btn).attr('status','UNHIGHLIGHT');
						$("#faq_btn_highlight").html('<span class="glyphicon glyphicon-flash" ></span>Bỏ nổi bật');
						isQuestionClosed=true;
					}else if (data.status==3) {
						bootbox.alert("Điểm câu hỏi yêu cầu 18,000đ!");
					}else if (data.status==4) {
						bootbox.alert("Câu hỏi đã đóng");
					}else {
						bootbox.alert("có lỗi xẩy ra!");
					}


				},
				error:function(){
					console.log("AJAX request was a failure");
					$(btn).button('reset');
				}
			});
		}else if (statusProtect=="UNHIGHLIGHT") {
			$.ajax({
				url: basePath+"/question/unhighlight-question",
				type: "POST",
				dataType:"json",
				data: {question:questionID},
				success: function(data){
					$(btn).button('reset');
					if(data.status==1){


						$(btn).attr('status','HIGHLIGHT');
						$("#faq_btn_highlight").html('<span class="glyphicon glyphicon-flash" ></span>Nổi bật');
						isQuestionClosed=false;
					}else if (data.status==3) {
						bootbox.alert("Điểm câu hỏi yêu cầu 18,000đ!");
					}else{
						bootbox.alert("có lỗi xẩy ra!");
					}


				},
				error:function(){
					console.log("AJAX request was a failure");
					$(btn).button('reset');
				}
			});

		}
	});
	//TOP QUESTION
	$("#faq_btn_pololar").click(function(){
		var statusProtect=$(this).attr("status");
		var btn=this;
		$(btn).button('loading');
		if(statusProtect=="TOP"){
			$.ajax({
				url: basePath+"/question/top-question",
				type: "POST",
				dataType:"json",
				data: {question:questionID},
				success: function(data){
					$(btn).button('reset');
					if(data.status==1){


						$(btn).attr('status','UNTOP');
						$("#faq_btn_pololar").html('<span class="glyphicon glyphicon-fire" ></span>Bỏ tiêu điểm');
						isQuestionClosed=true;
					}else if (data.status==3) {
						bootbox.alert("Điểm câu hỏi yêu cầu 18,000đ!");
					}else if (data.status==4) {
						bootbox.alert("Câu hỏi đã đóng");
					}else {
						bootbox.alert("có lỗi xẩy ra!");
					}


				},
				error:function(){
					console.log("AJAX request was a failure");
					$(btn).button('reset');
				}
			});
		}else if (statusProtect=="UNTOP") {
			$.ajax({
				url: basePath+"/question/untop-question",
				type: "POST",
				dataType:"json",
				data: {question:questionID},
				success: function(data){
					$(btn).button('reset');
					if(data.status==1){


						$(btn).attr('status','TOP');
						$("#faq_btn_pololar").html('<span class="glyphicon glyphicon-fire" ></span>Tiêu điểm');
						isQuestionClosed=false;
					}else if (data.status==3) {
						bootbox.alert("Điểm câu hỏi yêu cầu 18,000đ!");
					}else{
						bootbox.alert("có lỗi xẩy ra!");
					}


				},
				error:function(){
					console.log("AJAX request was a failure");
					$(btn).button('reset');
				}
			});

		}
	});


	//spinner

	$(".spinner-up").click(function() {
		var currentPoint = parseInt($("#faq_txt_bonus_point").val());
		if(isNaN(currentPoint)){
			currentPoint=0;
			$("#faq_txt_bonus_point").val(0);
		};
		var changePoint=currentPoint+1;
		if(changePoint<=totalMoneyPoint){
		$("#faq_txt_bonus_point").val(changePoint);
		$("#faq_total_point").text(totalMoneyPoint-changePoint);
		}
	});
	$(".spinner-down").click(function() {
		var currentPoint = parseInt($("#faq_txt_bonus_point").val());
		if(isNaN(currentPoint)){
			currentPoint=totalMoneyPoint;
			$("#faq_txt_bonus_point").val(totalMoneyPoint);

		}
		var changePoint=currentPoint-1;
		if(changePoint>=0){
		$("#faq_txt_bonus_point").val(changePoint);
		$("#faq_total_point").text(totalMoneyPoint-changePoint);
		}
	});



	$(document).on("keypress", "#faq_txt_bonus_point", function(event) {

		if (event.which == 13) {
			var changePoint = parseInt($("#faq_txt_bonus_point").val());
//			console.log($("#faq_txt_bonus_point").val());
			if(isNaN(changePoint)){
				$("#faq_txt_bonus_point").val(0);
			}
			if(changePoint>=0&&changePoint<=totalMoneyPoint){
		   $("#faq_txt_bonus_point").val(changePoint);
			$("#faq_total_point").text(totalMoneyPoint-changePoint);
			}else if (changePoint<0) {
				bootbox.alert("Điểm thưởng phải lớn hơn 0");
				$("#faq_txt_bonus_point").val(0);
				$("#faq_total_point").text(totalMoneyPoint);
			}else if (changePoint>totalMoneyPoint) {
				bootbox.alert("Điểm thưởng tối ta phải nhỏ hơn hoặc bằng "+totalMoneyPoint);
				$("#faq_txt_bonus_point").val(totalMoneyPoint);
				$("#faq_total_point").text(0);
			}
		}

	});
	//end spinner
	$("#faq_link_bonus_point").click(function(){
		$("#faq_form_bonus_point").toggle("slow");
	});
// tooltip
	$(".faq_content_question_tooltip").tooltip({
		'html' : 'true',
		'placement' : 'bottom'
	});
	//save point
	$("#faq_btn_bonus_point_save").click(function(){
		var btn = $(this);
        btn.button('loading');

		var bonusPoint=parseInt($("#faq_txt_bonus_point").val());

		if(bonusPoint>=0&&bonusPoint<=totalMoneyPoint){
			$.ajax({
				// async:false,
				url : basePath
						+ "/question/bonusPoint",
				type : "POST",
				dataType : "json",
				data : {
					question:questionID,
					bonusPoint:bonusPoint,
					noteEdit:"Tặng thêm "+bonusPoint+" điểm"

				}, // The data your
					// sending
				// to page
				success : function(data) {
					if (data.status === 1) {
						var currentBonusPoint=parseInt($("#faq_total_bonus_point_question").text());
						$("#faq_total_bonus_point_question").text(currentBonusPoint+bonusPoint);
						btn.button('reset');
						$("#faq_form_bonus_point").toggle("slow");
					}else if (data.status === 0) {
						bootbox.alert("có lỗi xẩy ra!");
						btn.button('reset');
					}else if (data.status === 2) {
						bootbox.alert("Câu hỏi đã đóng, không tặng thêm điểm!");
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
		}else{
			bootbox
			.alert("Số điểm thưởng không hợp lệ!");
			btn.button('reset');
		}
	});
	$("#faq_btn_bonus_point_cancel").click(function(){
		$("#faq_form_bonus_point").toggle("slow");
	});

	//edit answer wiki style
	$(".faq_btn_edit_answer_wiki").click(function(){
		answerIDEditWiki=$(this).attr('answer');
		console.log(answerIDEditWiki);

	});

	//show revison
	$("#faq_show_revision").click(function(){
		$(".faq_wrap_revision").toggle();
	});
}); //end ready