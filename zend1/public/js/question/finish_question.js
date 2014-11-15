$(document).ready(function() {
		$(".spinner-up").click(function() {
		var currentPoint = parseInt($("#faq_txt_bonus_point").val());
		if(isNaN(currentPoint)){
			currentPoint=0;
			$("#faq_txt_bonus_point").val(0);
		};
		var changePoint=currentPoint+1;
		if(changePoint<=totalMoneyPoint){
		$("#faq_txt_bonus_point").val(changePoint);
		$("#faq_total_point").text(totalMoneyPoint-changePoint);
		}
	});
	$(".spinner-down").click(function() {
		var currentPoint = parseInt($("#faq_txt_bonus_point").val());
		if(isNaN(currentPoint)){
			currentPoint=totalMoneyPoint;
			$("#faq_txt_bonus_point").val(totalMoneyPoint);

		}
		var changePoint=currentPoint-1;
		if(changePoint>=0){
		$("#faq_txt_bonus_point").val(changePoint);
		$("#faq_total_point").text(totalMoneyPoint-changePoint);
		}
	});



	$(document).on("keypress", "#faq_txt_bonus_point", function(event) {

		if (event.which == 13) {
			var changePoint = parseInt($("#faq_txt_bonus_point").val());
//			console.log($("#faq_txt_bonus_point").val());
			if(isNaN(changePoint)){
				$("#faq_txt_bonus_point").val(0);
			}
			if(changePoint>=0&&changePoint<=totalMoneyPoint){
		   $("#faq_txt_bonus_point").val(changePoint);
			$("#faq_total_point").text(totalMoneyPoint-changePoint);
			}else if (changePoint<0) {
				bootbox.alert("Điểm thưởng phải lớn hơn 0");
				$("#faq_txt_bonus_point").val(0);
				$("#faq_total_point").text(totalMoneyPoint);
			}else if (changePoint>totalMoneyPoint) {
				bootbox.alert("Điểm thưởng tối ta phải nhỏ hơn hoặc bằng "+totalMoneyPoint);
				$("#faq_txt_bonus_point").val(totalMoneyPoint);
				$("#faq_total_point").text(0);
			}
		}
	});

});
