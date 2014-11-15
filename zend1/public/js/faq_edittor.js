function setTinyEdittor(){

faqTinyEdittor=	tinymce.init({
	    selector: ".faq_txt_content",
	    plugins: [
	        "advlist autolink lists link image charmap print preview anchor",
	        "searchreplace visualblocks code fullscreen",
	        "insertdatetime media table contextmenu paste  moxiemanager faqimage"


	    ],
		language : 'vi_VN',
		height : 150,
		theme_advanced_buttons1 : 'faqimage' ,
	    // ===========================================
	    // PUT PLUGIN'S BUTTON on the toolbar
	    // ===========================================
	    toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image faqimage",
	    // ===========================================
	    // SET RELATIVE_URLS to FALSE (This is required for images to display properly)
	    // ===========================================
	    relative_urls: false
	});
return faqTinyEdittor;
};