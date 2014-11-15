

    /* @todo: load list questions with type of question
    */
function loadQuestion(){
    	totalLoad = 0;
    	$('#faq_postswrapper').html("<center><img  src='/images/ajax-loader.gif'></center>");


        console.log(subject_select);
        $.ajax({
            url: basePath  + '/member/' + act_sub+'/'+user,
            type: 'post',
            async: true,
            data: {
            	from:0,
            	to:16,
            	subject: subject_select
            	}
        }).done(function(data){
            $('#faq_postswrapper').html(data);

			settimeUpdate();
			showPreview();
        });

 	}