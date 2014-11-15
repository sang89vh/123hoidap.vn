

    /* @todo: load list questions with type of question
    */
    function loadQuestion(){
    	totalLoad = 0;
    	$('#faq_question_index_content').html("<center><img  src='/images/ajax-loader.gif'></center>");
        var type_question;

        type_question = $('#faq_question_index_nav li.active').attr('value');

        console.log(subject_select);
        $.ajax({
            url: basePath  + '/question/' + type_question,
            type: 'post',
            async: true,
            data: {subject: subject_select }
        }).done(function(data){
            $('#faq_question_index_content').html(data);
			settimeUpdate();
			showPreview();
			makeAjaxTip();
			makeAjaxTipQuestionInfo();
        });

 	}

    $(document).ready(function(){
    	//load question
			    loadQuestion();
    });


