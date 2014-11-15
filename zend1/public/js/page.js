
/* Thực hiện validate form
	* vitri_notice_error('top' - show error at top, 'beside' - show error beside it)
*/
function validateit(form_id, vitri_notice_error, valid_callback, invalid_callback){ 
		$('#' + form_id + ' input').keydown(function(evt){
			$('#' + form_id + ' .notice_text').remove();
			$('#' + form_id + ' .ui-state-error').removeClass('ui-state-error');
		});
		$('#' + form_id + ' textarea').keydown(function(evt){
			$('#' + form_id + ' .notice_text').remove();
			$('#' + form_id + ' .ui-state-error').removeClass('ui-state-error');
		});
		
		var isValid = true; 
		var error_text = '';
		var thisform = $('#' + form_id);
		$('#' + form_id + ' .notice_text').remove();
		$('#' + form_id + ' .ui-state-error').removeClass('ui-state-error');
		var listInput = $('#' + form_id + ' [valid_type]');
		var input_error; 
		$.each(listInput,function(idx, obj){
			var type = $(obj).attr('valid_type'); 
			var checkexist = $(obj).attr('checkexist');
				if(checkexist=='0') checkexist = false; else checkexist = true;
			// hợp lệ theo độ lớn
			if(type.indexOf('length')>-1){ 
				var label = $(obj).attr('valid_label');
				var dodai = $(obj).val().length;
				var min = $(obj).attr('valid_min');
				var max = $(obj).attr('valid_max');
					if(min && max){
						if(min>dodai || max<dodai){
						error_text = label + ' phải lớn hơn ' + min + ' và nhở hơn ' + max + ' ký tự'; input_error=$(obj); isValid = false;
						return false;
						}
					}else if(min){
						if(min>dodai){
						error_text = label + ' phải lớn hơn ' + min + ' ký tự' ;input_error=$(obj); isValid = false;
						return false;
						}
					}else if(max){
						if(max<dodai){
						error_text = label + ' phải nhỏ hơn ' + max + ' ký tự'; input_error=$(obj); isValid = false;
						return false;
						}
					}
			}
			// bắt buộc nhập
			if(type.indexOf('require')>-1){
				var gt = $(obj).val();
				var label = $(obj).attr('valid_label');
				if(gt==""){
					isValid = false;
					error_text = label + " là bắt buộc"; input_error=$(obj); return false;
				}

			}
			// hợp lệ theo email
			if(type.indexOf('email')>-1){
				isValid = checkRegexp($(obj),/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
				if(!isValid) {error_text = 'Địa chỉ Email không hợp lệ'; input_error=$(obj); return false;}
			}
			
			// hợp lệ theo phone
			if(type.indexOf('phone')>-1){
				isValid = checkRegexp($(obj),/^(\+84){0,1}[0-9]{10,11}$/);
				if(!isValid) { error_text = 'Số Phone không phải việt nam'; input_error=$(obj); return false; }
			}
			// hợp lệ theo Number
			if(type.indexOf('number')>-1){
				var label = $(obj).attr('valid_label');
				var min = $(obj).attr('valid_min'); min = new Number(min);
				var max = $(obj).attr('valid_max'); max = new Number(max);
				var gt = $(obj).val().trim(); gt = new Number(gt);
						var isNum = checkRegexp($(obj),/^[0-9]+$/);
							if(!isNum) { error_text = label + ' phải là số' ; if(min) error_text = error_text + ' >= ' + min; if(max) error_text = error_text + ' và <= ' + max; isValid = false; input_error=$(obj);return; }
						if(min && max){ 
							if(min>gt || gt>max) { error_text = label + ' phải >= ' + min + ' và <= ' + max; isValid = false; input_error=$(obj);return }
						}else if(min) {
							if(min>gt) { error_text = label + ' phải >= ' + min; isValid = false; input_error=$(obj);return; }
						} else if(max) {
							if(max<gt) { error_text = label + ' phải <= ' + max; isValid = false; input_error=$(obj); return; }
						}
			}
			// hợp lệ theo định dạng
			if(type.indexOf('regrex')>-1){ 
				var rg = $(obj).attr('valid_regrex'); // note: chuoi String nay ko chua / va / o 2 dau
				var label = $(obj).attr('valid_label');
				var ex = $(obj).attr('valid_example');
					if(rg){
						var bt = new RegExp(rg);
						var hople = bt.test($(obj).val());
							if(!hople){ 
								isValid = false; error_text = label + ' không hợp lệ '; if(ex) error_text = error_text + '. ví dụ: ' + ex; input_error = $(obj); return ;
							}
					}
			}
			if(type.indexOf('exist')>-1 && checkexist){ 
				var label = $(obj).attr('valid_label');
				var sql_name = $(obj).attr('sql_name');
				var field = $(obj).attr('field');
				var ajaxurl = $(obj).attr('ajaxurl');
				var gt = $(obj).val();
				var izzi_meta = new Object();
					izzi_meta.sql_name = sql_name;
					izzi_meta.field = field;
					izzi_meta.gt = gt;
				var izzi_meta_json = JSON.stringify(izzi_meta);
				$.ajax({
				url: ajaxurl,
				type: 'post',
				data: {'izzi_type': 'exist', izzi_meta: izzi_meta_json },
				async: false
				}).done(function(data){ 
					if(data.trim()=="y"){ // đã được sử dụng
						isValid = false; error_text = label + ' đã được sử dụng'; input_error = $(obj); return ;
					}
				}).fail(function(data){
					isValid = false; error_text = label + ' - có lỗi khi không thể biết là đã được dùng chưa'; input_error = $(obj); return ;
				});
			}
			
		}); // end each
		izzi_common_notice = error_text;
		izzi_common_id = form_id;
		if(valid_callback) eval(valid_callback);
		if(invalid_callback) eval(invalid_callback);
		if(!isValid){ 
			if(vitri_notice_error=='beside') input_error.after("<span style='list-style:none' class='notice_error_text notice_text'>* " + error_text + '</span>');
			if(vitri_notice_error=='top') thisform.prepend("<li style='list-style:none' class='notice_error_text notice_text'>* " + error_text + '</li>');
			input_error.addClass('ui-state-error');
			return false;
		} 
		return true;
}

/* o - jquery element object
*  regexp - biểu thức regrex
*/
function checkRegexp( o, regexp) {
            if ( !( regexp.test( o.val() ) ) ) {
                return false;
            } else {
                return true;
            }
}

/*
 * String to hashCode
 */
String.prototype.hashCode = function(){
    var hash = 0, i, char;
    if (this.length == 0) return hash;
    for (i = 0, l = this.length; i < l; i++) {
        char  = this.charCodeAt(i);
        hash  = ((hash<<5)-hash)+char;
        hash |= 0; // Convert to 32bit integer
    }
    return hash;
};

String.prototype.trim=function(){return this.replace(/^\s+|\s+$/g, '');};