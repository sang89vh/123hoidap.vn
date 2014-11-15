$(document).ready(function() {

	$(document).on("click", "#faq_btn_search_subject", function() {
		 totalLoad=0;
		 step=16;
	     faq_from_paging=totalLoad*step;
		 faq_to_paging=(totalLoad+1)*step;
		var keySearch = $("#faq_txt_search_subject").val();

		faq_data_paging={keyword:keySearch,
				 from:faq_from_paging,
				 to:faq_to_paging,
				 actionRequest:faq_action};
		 console.log('faq_data_paging: '+faq_data_paging);
		$.ajax({
			url : basePath + "/subject/list-subject",
			type : "POST",
			dataType : "html",
			data : faq_data_paging, // The data your sending to page
			success : function(data) {

				$("#faq_postswrapper").html(imgAjaxLoad);
				$("#faq_postswrapper").html(data);
				totalLoad=totalLoad+1;
				faq_from_paging=totalLoad*step;
				 faq_to_paging=(totalLoad+1)*step;
				 faq_data_paging={keyword:keySearch,
						 from:faq_from_paging,
						 to:faq_to_paging,
						 actionRequest:faq_action};

			},
			error : function() {
				console.log("AJAX request was a failure");
			}
		});
	});


	// pagging scroll
    $(window).scroll(function(){
	    if($(window).scrollTop() == $(document).height() - $(window).height()){

	    	if(faq_to_paging<totalSubject){
	    		$('div#faq_loadmoreajaxloader').show();
			$.ajax({
				url: faq_url_paging,
				type: "POST",
		        dataType:"html",
		        data: faq_data_paging, //The data your sending to page
				success: function(html){
					totalLoad=totalLoad+1;

					 faq_from_paging=totalLoad*step;
					 faq_to_paging=(totalLoad+1)*step;
					 keySearch = $("#faq_txt_search_subject").val();
					 faq_data_paging={keyword:keySearch,
							 from:faq_from_paging,
							 to:faq_to_paging,
							 actionRequest:faq_action};
					 console.log("====> faq_data_paging: "+faq_data_paging);
					if(html){
						$("#faq_postswrapper").append(html);
						$('div#faq_loadmoreajaxloader').hide();
					}else{
						$('div#faq_loadmoreajaxloader').html('<center>==End==</center>');
					}
				}
			});
	    }
	    }
	});

	$(document).on("keypress", "#faq_txt_search_subject", function(event) {

		if (event.which == 13) {
			$("#faq_btn_search_subject").click();
		}
	});

	//seach subject autosugest
	$('#faq_txt_search_subject').autocomplete({
	    serviceUrl: basePath+'/search/subject',
	    onSelect: function (suggestion) {
	        $('#faq_txt_search_subject').val(suggestion.value);
	        $("#faq_btn_search_subject").click();
	    }
	});

});
