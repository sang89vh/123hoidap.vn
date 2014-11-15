

    /* @todo: load list questions with type of question
    */
function loadQuestion(){
    	totalLoad = 0;
    	$('#faq_postswrapper').html("<center><img  src='/images/ajax-loader.gif'></center>");


        console.log(subject_select);
        $.ajax({
            url: basePath  + faq_url_review,
            type: 'post',
            async: true,
            data: {
            	from:0,
            	to:16,
            	subject: subject_select,
            	type:type_search
            	}
        }).done(function(data){
            $('#faq_postswrapper').html(data);

			settimeUpdate();
			showPreview();
        });

 	}