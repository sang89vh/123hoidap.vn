

$(document).ready(function() {
// pagging scroll

    $(window).scroll(function(){
	    if($(window).scrollTop() == $(document).height() - $(window).height()){
	    	if(faq_to_paging<totalRow){
	    		$('div#faq_loadmoreajaxloader').show();

	    		totalLoad=totalLoad+1;
				 faq_from_paging=totalLoad*step;
				 faq_to_paging=(totalLoad+1)*step;
				 faq_data_paging={
							from:faq_from_paging,
							to:faq_to_paging,
							subject:subject_select,
							user:user,
							 type:type_search
							};
				 console.log("from:"+faq_from_paging);
				 console.log("to:"+faq_to_paging);
				 console.log("subject_select:"+subject_select);
				 console.log("user:"+user);


			$.ajax({
				async:false,
				url: faq_url_paging,
				type: "POST",
		        dataType:"html",
		        data: faq_data_paging, //The data your sending to page
				success: function(html){
					if(html){
						$("#faq_postswrapper").append(html);
						$('div#faq_loadmoreajaxloader').hide();
					}else{
						$('div#faq_loadmoreajaxloader').html('<center>==End==</center>');
					}

					settimeUpdate();
					showPreview();
				}
			});
	    }
	    }
	});

});