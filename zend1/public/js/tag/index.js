function validateTags(tagVal) {
  // http://stackoverflow.com/a/46181/11236

    var re = /^[A-Za-z0-9_.-#]+$/i;
    return re.test(tagVal);
}

$(document).ready(function(){
	$("#faq_btn_create_tag").click(function(){
		location.href="/tag/create";
	});
	$("#faq_btn_submit_create_tag").click(function(event){
		var last_tag_id="";
		var tags=$(".faq_tag_remove");
		var lengthTag=tags.length;
    	if(lengthTag>0){
		for( var j=0;j<lengthTag;j++){
			last_tag_id=last_tag_id+$(tags[j]).attr("id");

		}
    	}
		$('#faq_tag_relationship').val(last_tag_id);
		var tagname=$("#faq_tag_name").val();
		tagname=tagname.trim();
		var tagDesc=$("#faq_tag_desc").val();
		tagDesc=tagDesc.trim();
		var isValidTag=validateTags(tagname);
		var lengtagName=tagname.length;
		var lengtagDesc=tagDesc.length;

		if((isValidTag==false||lengtagName>25)&&lengtagDesc<20){
			bootbox.alert("Tên tag phải nhỏ hơn 25 ký tự và chỉ chấp nhận chứa các ký tự a-z 0-9 + # - .<br>Mô tả tag yêu cầu lớn hơn 20 ký tự");
			event.preventDefault();
		}else if(isValidTag==false||lengtagName>25){
			bootbox.alert("Tên tag phải nhỏ hơn 25 ký tự và chỉ chấp nhận chứa các ký tự a-z 0-9 + # - .");
			event.preventDefault();
		}else if(lengtagDesc<20){
			bootbox.alert("Mô tả tag yêu cầu lớn hơn 20 ký tự");
			event.preventDefault();
		}
		});


	// suggestion question
	$('#faq_tag_relationship').autocomplete({
	    serviceUrl: basePath+'/search/find-tag',
	    onSelect: function (suggestion) {
	    	var label=suggestion.value;
	    	var id=suggestion.data;
	    	$('#faq_tag_relationship').val("");
	    	var iscontain=false;
	    	var tags=$(".faq_tag_remove");
	    	var lengthTag=tags.length;
	    	if(lengthTag>0){
			for( var j=0;j<lengthTag;j++){
				var currentId=$(tags[j]).attr("id");
				console.log(currentId);
				if(id==currentId){
					iscontain=true;

				}
          	}
	    	}
			if(iscontain==false){
		    	var data='<span id="'+id+'" class="tag label label-info">'+label+'<span class="faq_tag_remove glyphicon glyphicon-remove" id="'+id+'" ></span></span>';
		    	$("#faq_data_tag").append(data);
			}
	    }
     });

	$(".faq_tag_remove").click(function(){

		var idtag=$(this).attr("id");
		console.log(idtag);
		$("#"+idtag).remove();
	});
	//init tag
	if(!isEmptyOrNull(tags)){
		var faq_key_word=tags.split(",");
		for (var i_faq_tag in faq_key_word) {
			$('#faq_txt_tag_relationship').tagsinput('add',faq_key_word[i_faq_tag]);
		}
	}
//delete tag
	$(".faq_btn_delete_tag").click(function(){
		var tagID=$(this).attr("tagid");
		bootbox.confirm("Bạn có muốn xóa?", function(result) {
			 if(result){
				 $.ajax({
				        url: basePath+"/tag/delete",
				        type: "POST",
				        dataType:"json",
				        data: {
				        	tag:tagID
				        	},
				        success: function(data){
				       if(data.status==1){
				    	   window.location.reload();
				       }else{
				    	   bootbox.alert("có lỗi xẩy ra!");
				       }

				        },
				        error:function(){
				            console.log("AJAX request was a failure");
				        }
				      });
			 }
			});
	});


});