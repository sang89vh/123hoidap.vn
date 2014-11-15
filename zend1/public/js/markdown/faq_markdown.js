var editor=null;
$(document).ready(function(){
	 (function () {
         var converter = new Markdown.Converter();

         converter.hooks.chain("preConversion", function (text) {
             return text.replace(/\b(a\w*)/gi, "*$1*");
         });

         converter.hooks.chain("plainLinkText", function (url) {
             return "This is a link to " + url.replace(/^https?:\/\//, "");
         });
         converter.hooks.chain("preBlockGamut", function (text, runBlockGamut) {
             return text.replace(/^ {0,3}""" *\n((?:.*?\n)+?) {0,3}""" *$/gm, function (whole, inner) {
                 return "<blockquote>" + runBlockGamut(inner) + "</blockquote>\n";
             });
         });
         var help = function () {
        	 $(".faq_wrap_editor_help").toggle('slow');
        	 };
         var options = {
             helpButton: { handler: help },
             strings: { quoteexample: "whatever you're quoting, put it right here" }
         };
          editor = new Markdown.Editor(converter, "-answer", options);


// insert image with manager media file by faq
          editor.hooks.set("insertImageDialog", function (callback) {
        	  if(isAllowed){
        	  $("#faq_link_media_file").click();
        	  }else{
        		  redirectLogin();
        	  }
	       	 $('#faq_media_file').on('faq_select_media_file',function(evt){
	       		var imageIDs=$('#faq_media_file').val();
	       		console.log(imageIDs);
	       		if(imageIDs!=null&&imageIDs!=""){
					var faq_key_image=imageIDs.split(";");
					if(faq_key_image.length==0){
						faq_key_image=[imageIDs];
					}
					 var urlImage=null;
//					for (var i_faq_image in faq_key_image) {
//						var tempImage="";
//						$.ajax({
//							async:false,
//							url : basePath + "/media/get-media",
//							type : "GET",
//							dataType : "html",
//							data : {
//								media:faq_key_image[i_faq_image],
//								typeEditor:"MARDOWN"},
//							success : function(data) {
//
//								tempImage=data;
//
//							},
//							error : function() {
//								console.log("AJAX request was a failure");
//
//							}
//						});
//						urlImage=urlImage+tempImage;
//						callback(tempImage);
//					}
////					$("#wmd-preview-answer").append(urlImage);
////					$("#wmd-input-answer").append(urlImage);

					$.ajax({
						async:false,
						url : basePath + "/media/get-media",
						type : "GET",
						dataType : "html",
						data : {
							media:faq_key_image[0],
							typeEditor:"MARDOWN"},
						success : function(data) {

							urlImage=data;

						},
						error : function() {
							console.log("AJAX request was a failure");

						}
					});
					callback(urlImage);

				}else{
					callback(null);
				}

//	       		reset image list
	       		$('#faq_media_file').val("");


	     	 });


            return true; // tell the editor that we'll take care of getting the image url
        });


         editor.run();
     })();


	 //open help more page
	 $("#faq_editor_help_more").click(function(){
		 window.open('http://qapolo.com/support/help','_blank');
	 });


//	 var defaultContent=$("#faq_default_content").html();
//	 var contentMardown=toMarkdown(defaultContent);
//	 $("#wmd-input-answer").append(contentMardown);


});