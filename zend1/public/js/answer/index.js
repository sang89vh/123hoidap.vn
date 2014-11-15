
    function loadQuestion(){
    	totalLoad = 0;
    	$('#faq_question_index_content').html("<center><img  src='/images/ajax-loader.gif'></center>");
//    	reset paging
    	totalLoad=0;
    	 faq_from_paging=totalLoad*step;
    	 faq_to_paging=(totalLoad+1)*step;
    	 $('#faq_postswrapper').html(imgAjaxLoad);
        console.log(subject_select);
        $.ajax({
            url: basePath  + '/answer/' + type_answer,
            type: 'post',
            async: true,
            data: {
            	from:faq_from_paging,
            	to:faq_to_paging,
            	subject: subject_select
            	}
        }).done(function(data){
            $('#faq_postswrapper').html(data);

			settimeUpdate();
			showPreview();
        });

 	}