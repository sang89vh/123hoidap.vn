
//-------------------------------

$(document).ready(
		function() {
		    // menu item click event
		    $('#faq_question_index_nav li').click(function(evt){
		        // data handler (also check loaded too)
		        var loaded = true;
		    	if(!$(this).hasClass('active'))
		       	   loaded = false;
		        // style handler
		        $('#faq_question_index_nav li').removeClass('active');
		        $(this).addClass('active');
		        if(!loaded) loadQuestion();
		    });

			// menu item click event
			$('#faq_question_index_nav li').click(function(evt) {
				// data handler (also check loaded too)
				var loaded = true;
				if (!$(this).hasClass('active'))
					loaded = false;
				// style handler
				$('#faq_question_index_nav li').removeClass('active');
				$(this).addClass('active');
				if (!loaded)
					loadQuestion();
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
					        	 $("#"+questionID).remove();
					        	 }

					         },
					         error:function(){
					             console.log("AJAX request was a failure");
					         }
					       });
					  }

					});
				}else if(actionID=="EDIT_WIKISTYLE"){
					questionID=$(this).attr("value");
					location.href=basePath+"/question/edit-wikistyle/"+questionID;
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
				//filter subject
				$(document).on('click','ul#faq_question_index_options li',function(){

					subject_select=$(this).attr("value");
					console.log("select subject");
					loadQuestion();

					});

                //filter type search
				$(document).on('click','#faq_type_search_follow',function(){
					$("#faq_type_search li").removeClass("active");
					$(this).addClass("active");
					type_search=0;
					console.log("type_search:".type_search);
					loadQuestion();

					});
				$(document).on('click','#faq_type_search_bonus',function(){
					$("#faq_type_search li").removeClass("active");
					$(this).addClass("active");
					type_search=1;
					console.log("type_search:".type_search);
					loadQuestion();

				});
				$(document).on('click','#faq_type_search_need_answer',function(){
					$("#faq_type_search li").removeClass("active");
					$(this).addClass("active");
					type_search=2;
					console.log("type_search:".type_search);
					loadQuestion();

				});
				$(document).on('click','#faq_type_search_vote',function(){
					$("#faq_type_search li").removeClass("active");
					$(this).addClass("active");
					type_search=3;
					console.log("type_search:".type_search);
					loadQuestion();

				});


				//show preview action
				showPreview();

				//hover faq_content show option
				$(document).on("mouseenter",".faq_question",function(){
					$(this).children().children().children().children().children('.faq_option_question_icon').addClass('faq_option_question_icon_hover');

				});
				$(document).on("mouseleave",".faq_question",function(){
					$('.faq_option_question_icon').removeClass('faq_option_question_icon_hover');

				});
		});






