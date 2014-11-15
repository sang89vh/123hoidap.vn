$(document).ready(function(){



	$(document).on("click", "#faq_btn_search_member", function() {
		 totalLoad=0;
		 step=16;
	     faq_from_paging=totalLoad*step;
		 faq_to_paging=(totalLoad+1)*step;
		var keySearch = $("#faq_txt_search_member").val();
		faq_data_paging={
				keyword:keySearch,
				from:faq_from_paging,
				to:faq_to_paging
				};

		 console.log('faq_data_paging: '+faq_data_paging);
		$.ajax({
			url : basePath + "/member/list-member",
			type : "POST",
			 async: "false",
			dataType : "html",
			data : faq_data_paging, // The data your sending to page
			success : function(data) {

				$("#faq_postswrapper").html(imgAjaxLoad);
				$("#faq_postswrapper").html(data);

				totalLoad=totalLoad+1;
				faq_from_paging=totalLoad*step;
				 faq_to_paging=(totalLoad+1)*step;
				 faq_data_paging={
							keyword:keySearch,
							from:faq_from_paging,
							to:faq_to_paging
							};

			},
			error : function() {
				console.log("AJAX request was a failure");
			}
		});
	});


	// pagging scroll
    $(window).scroll(function(){
	    if($(window).scrollTop() == $(document).height() - $(window).height()){

	    	if(faq_to_paging<totalmember){
	    		totalLoad=totalLoad+1;
				 faq_from_paging=totalLoad*step;
				 faq_to_paging=(totalLoad+1)*step;
				 keySearch = $("#faq_txt_search_member").val();
				 faq_data_paging={
							keyword:keySearch,
							from:faq_from_paging,
							to:faq_to_paging
							};

				 console.log("====> faq_data_paging: from: "+faq_data_paging.from);
				 console.log("====> faq_data_paging: to: "+faq_data_paging.to);
	    		$('div#faq_loadmoreajaxloader').show();
			$.ajax({
				url: faq_url_paging,
				type: "POST",
				 async: "false",
		        dataType:"html",
		        data: faq_data_paging, //The data your sending to page
				success: function(html){

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

	$(document).on("keypress", "#faq_txt_search_member", function(event) {

		if (event.which == 13) {
			$("#faq_btn_search_member").click();
		}
	});

	//seach member autosugest
	$('#faq_txt_search_member').autocomplete({
	    serviceUrl: basePath+'/search/member',
	    onSelect: function (suggestion) {
	        $('#faq_txt_search_member').val(suggestion.value);
	        $("#faq_btn_search_member").click();
	    }
	});

});