$(document)
		.ready(
				function() {
					$(document).on("keypress", "#faq_form_login",
							function(event) {

								if (event.which == 13) {
									$("#faq_btn_login").click();
								}
							});
					$('#faq_btn_login')
							.click(
									function() {
										var btn = $(this);
										btn.button('loading');
										var email = $("input[name='email']")
												.val();
										var password = $(
												"input[name='password']").val();
										var isValid = validateit('faq_login',
												'top', false, false);
										if (!isValid) {
											btn.button('reset');
											return;
										}
										// authenticate user
										$
												.ajax(
														{
															url : basePath
																	+ '/user/auth',
															method : 'post',
															data : {
																email : email,
																password : password,
																urlBack : login_back_url
															}
														})
												.done(
														function(data) {
															if (data)
																if (data.trim() == 'success') {
																	location.href = login_back_url;
																} else if (data
																		.trim() == 'success_first') {
																	location.href = login_first_url;
																}
															if (data
																	&& data
																			.trim() == "not_user,not_password") {
																$(
																		'#faq_login .notice_text')
																		.remove();
																$('#faq_login')
																		.prepend(
																				"<li style='list-style:none' class='notice_error_text notice_text'>* "
																						+ 'Tên đăng nhập hoặc mật khẩu không đúng!'
																						+ '</li>');
																$(
																		'#faq_login [name="email"]')
																		.addClass(
																				'ui-state-error');
																isValid = false;
																btn
																		.button('reset');
															}
														})
												.fail(
														function() {
															$(
																	'#faq_login .notice_text')
																	.remove();
															$('#faq_login')
																	.prepend(
																			"<li style='list-style:none' class='notice_error_text notice_text'>* "
																					+ 'Không thể kết nối tới 123hoidap.vn'
																					+ '</li>');

															isValid = false;
															btn.button('reset');
														});
									});

					$('#form-login')
							.submit(
									function(evt) {
										evt.preventDefault();
										$("#login-submit")
												.text("Đang xử lý...");
										var email = this.email.value;
										var password = this.password.value;
										var isValid = validateit('form-login',
												'top', false, false);
										if (!isValid) {
											$("#login-submit")
													.text("Đăng nhập");
											return;
										}
										// authenticate user
										$
												.ajax(
														{
															url : basePath
																	+ '/user/auth',
															method : 'post',
															data : {
																email : email,
																password : password,
																urlBack : login_back_url
															}
														})
												.done(
														function(data) {
															if (data)
																if (data.trim() == 'success') {
																	location.href = login_back_url;
																} else if (data
																		.trim() == 'success_first') {
																	location.href = login_first_url;
																}
															if (data)
																if (data.trim() == 'not_user') {
																	$(
																			'#form-login .notice_text')
																			.remove();
																	$(
																			'#form-login')
																			.prepend(
																					"<li style='list-style:none' class='notice_error_text notice_text'>* "
																							+ 'Địa chỉ Email không đúng'
																							+ '</li>');
																	$(
																			'#form-login [name="email"]')
																			.addClass(
																					'ui-state-error');
																	isValid = false;
																	$(
																			"#login-submit")
																			.text(
																					"Đăng nhập");
																}
															if (data)
																if (data.trim() == 'not_user,not_password') {
																	$(
																	'#form-login .notice_text')
																	.remove();
																	$(
																	'#form-login')
																	.prepend(
																			"<li style='list-style:none' class='notice_error_text notice_text'>* "
																			+ 'Địa chỉ Email hoặc mật khẩu không đúng'
																			+ '</li>');
																	$(
																	'#form-login [name="email"]')
																	.addClass(
																	'ui-state-error');
																	isValid = false;
																	$(
																	"#login-submit")
																	.text(
																	"Đăng nhập");
																}
															if (data)
																if (data.trim() == 'not_password') {
																	$(
																			'#form-login .notice_text')
																			.remove();
																	$(
																			'#form-login')
																			.prepend(
																					"<li style='list-style:none' class='notice_error_text notice_text'>* "
																							+ 'Mật khẩu không đúng'
																							+ '</li>');
																	$(
																			'#form-login [name="password"]')
																			.addClass(
																					'ui-state-error');
																	isValid = false;
																	$(
																			"#login-submit")
																			.text(
																					"Đăng nhập");
																}
														})
												.fail(
														function() {
															$(
																	'#form-login .notice_text')
																	.remove();
															$('#form-login')
																	.prepend(
																			"<li style='list-style:none' class='notice_error_text notice_text'>* "
																					+ 'Không thể kết nối tới 123hoidap.vn'
																					+ '</li>');

															isValid = false;
															$("#login-submit")
																	.text(
																			"Đăng nhập");
														});
									});
				});