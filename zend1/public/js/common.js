function PopupCenter(pageURL, title, w, h) {
	var left = (screen.width / 2) - (w / 2);
	var top = (screen.height / 2) - (h / 2);
	var targetWin = window
			.open(
					pageURL,
					title,
					'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='
							+ w
							+ ', height='
							+ h
							+ ', top='
							+ top
							+ ', left='
							+ left);
}
(function($) {
	$.fn.faqFadeHighlight = function() {
		var faqelement = this;
		$(this).addClass("faq_highlight");
		$(this).hide().fadeIn(2000);
		setTimeout(function() {
			$(faqelement).removeClass('faq_highlight');
		}, 3000);
	};
})(jQuery);
var scrolltotop = {
	// startline: Integer. Number of pixels from top of doc scrollbar is
	// scrolled before showing control
	// scrollto: Keyword (Integer, or "Scroll_to_Element_ID"). How far to scroll
	// document up when control is clicked on (0=top).
	setting : {
		startline : 100,
		scrollto : 0,
		scrollduration : 1000,
		fadeduration : [ 500, 100 ]
	},
	controlHTML : '<img src="/img/navigate-up-icon.png" style="width:48px; height:48px" />', // HTML
																								// for
																								// control,
																								// which
																								// is
																								// auto
																								// wrapped
																								// in
																								// DIV
																								// w/
																								// ID="topcontrol"
	controlattrs : {
		offsetx : 5,
		offsety : 5
	}, // offset of control relative to right/ bottom of window corner
	anchorkeyword : '#top', // Enter href value of HTML anchors on the page that
							// should also act as "Scroll Up" links

	state : {
		isvisible : false,
		shouldvisible : false
	},

	scrollup : function() {
		if (!this.cssfixedsupport) // if control is positioned using JavaScript
			this.$control.css({
				opacity : 0
			}); // hide control immediately after clicking it
		var dest = isNaN(this.setting.scrollto) ? this.setting.scrollto
				: parseInt(this.setting.scrollto);
		if (typeof dest == "string" && jQuery('#' + dest).length == 1) // check
																		// element
																		// set
																		// by
																		// string
																		// exists
			dest = jQuery('#' + dest).offset().top;
		else
			dest = 0;
		this.$body.animate({
			scrollTop : dest
		}, this.setting.scrollduration);
	},

	keepfixed : function() {
		var $window = jQuery(window);
		var controlx = $window.scrollLeft() + $window.width()
				- this.$control.width() - this.controlattrs.offsetx;
		var controly = $window.scrollTop() + $window.height()
				- this.$control.height() - this.controlattrs.offsety;
		this.$control.css({
			left : controlx + 'px',
			top : controly + 'px'
		});
	},

	togglecontrol : function() {
		var scrolltop = jQuery(window).scrollTop();
		if (!this.cssfixedsupport)
			this.keepfixed();
		this.state.shouldvisible = (scrolltop >= this.setting.startline) ? true
				: false;
		if (this.state.shouldvisible && !this.state.isvisible) {
			this.$control.stop().animate({
				opacity : 1
			}, this.setting.fadeduration[0]);
			this.state.isvisible = true;
		} else if (this.state.shouldvisible == false && this.state.isvisible) {
			this.$control.stop().animate({
				opacity : 0
			}, this.setting.fadeduration[1]);
			this.state.isvisible = false;
		}
	},

	init : function() {
		jQuery(document)
				.ready(
						function($) {
							var mainobj = scrolltotop;
							var iebrws = document.all;
							mainobj.cssfixedsupport = !iebrws || iebrws
									&& document.compatMode == "CSS1Compat"
									&& window.XMLHttpRequest; // not IE or
																// IE7+ browsers
																// in standards
																// mode
							mainobj.$body = (window.opera) ? (document.compatMode == "CSS1Compat" ? $('html')
									: $('body'))
									: $('html,body');
							mainobj.$control = $(
									'<div id="topcontrol">'
											+ mainobj.controlHTML + '</div>')
									.css(
											{
												position : mainobj.cssfixedsupport ? 'fixed'
														: 'absolute',
												bottom : mainobj.controlattrs.offsety,
												right : mainobj.controlattrs.offsetx,
												opacity : 0,
												cursor : 'pointer'
											}).attr({
										title : 'Lên đầu trang'
									}).click(function() {
										mainobj.scrollup();
										return false;
									}).appendTo('body');
							if (document.all && !window.XMLHttpRequest
									&& mainobj.$control.text() != '') // loose
																		// check
																		// for
																		// IE6
																		// and
																		// below,
																		// plus
																		// whether
																		// control
																		// contains
																		// any
																		// text
								mainobj.$control.css({
									width : mainobj.$control.width()
								}); // IE6- seems to require an explicit width
									// on a DIV containing text
							mainobj.togglecontrol();
							$('a[href="' + mainobj.anchorkeyword + '"]').click(
									function() {
										mainobj.scrollup();
										return false;
									});
							$(window).bind('scroll resize', function(e) {
								mainobj.togglecontrol();
							});
						});
	}
};

var disabledConfirm_exit = false;
function confirm_exit(e) {
	if (disabledConfirm_exit)
		return;
	if (!e)
		e = window.event;

	e.cancelBubble = true;
	e.returnValue = 'Are you sure you want to leave?';

	if (e.stopPropagation) {
		e.stopPropagation();
		e.preventDefault();
	}
}

var login_back_url = window.location.pathname;
(function() {
	if (typeof Object.defineProperty === 'function') {
		try {
			Object.defineProperty(Array.prototype, 'sortBy', {
				value : sb
			});
		} catch (e) {
		}
	}
	if (!Array.prototype.sortBy)
		Array.prototype.sortBy = sb;

	function sb(f) {
		for ( var i = this.length; i;) {
			var o = this[--i];
			this[i] = [].concat(f.call(o, o, i), o);
		}
		this.sort(function(a, b) {
			for ( var i = 0, len = a.length; i < len; ++i) {
				if (a[i] != b[i])
					return a[i] < b[i] ? -1 : 1;
			}
			return 0;
		});
		for ( var i = this.length; i;) {
			this[--i] = this[i][this[i].length - 1];
		}
		return this;
	}
})();

// check null or empty
function isEmptyOrNull(variable) {
	if (variable == null) {
		return true;
	}
	if (variable == "") {
		return true;
	}
	return false;
}
/*
 * qapolo common script 1. show tooltip for user info
 */
// post to facebook
function postToFacebook(message, name, description, link, picture, caption) {
	var params = {};
	params['message'] = message;
	params['name'] = name;
	params['description'] = description;
	params['link'] = link;
	params['picture'] = picture;
	params['caption'] = caption;
	console.log(params);
	// FB.api('/me/feed', 'post', params, function(response) {
	// if (!response || response.error) {
	// console.log('Post to face: Error occured');
	// } else {
	// console.log('Post to face: Published to stream');
	// }
	// });
};
// redirect to login page
function redirectLogin() {
	if (!isAllowed) {
		$("#faq_link_login_fancy").click();
		$.ajax({
			url : basePath + "/user/basic-login",
			type : "POST",
			dataType : "html",
			data : {
				urlBack : login_back_url
			}, // The data your sending to page
			success : function(html) {
				$("#faq_inline_basic_login").html(html);

			},
			error : function() {
				console.log("AJAX request was a failure");
			}
		});
	}
	// bootbox.dialog({
	// message: "Bạn cần đăng nhập trước",
	// title: "Thông báo!",
	// buttons: {
	// login: {
	// label: "Đăng nhập",
	// className: "btn-login",
	// callback: function() {
	// window.open("/user/login", "_blank");
	// }
	// },
	// cancel: {
	// label: "Bỏ qua",
	// className: "btn-cancel",
	// callback: function() {
	//
	// }
	// }
	// }
	// });
}
// add new function convert time to string
function settimeUpdate() {
	$(".faq_post_time").text(
			function(index, text) {

				var datatime = parseInt($(this).attr('post-time'));
				$(this).html(
						'<small>' + moment(new Date(datatime)).fromNow()
								+ "</small>");

			});
}
// /mixed editor
var FaqEdittor = {
	tinyID : "mce_0",
	mardownID : "wmd-preview-answer",
	setContent : function(type, content) {

		if (type == 'TINY') {

			// tinyMCE.get(this.tinyID).setContent(content);
			tinyMCE.activeEditor.setContent(content);
		} else if (type = 'MARKDOWN') {
			$('#' + this.mardownID).html(content);
		}

	},
	getContent : function(type) {
		var content = null;
		if (type == 'TINY') {
			// content=tinyMCE.get(this.tinyID).getContent();
			content = tinyMCE.activeEditor.getContent();
		} else if (type = 'MARKDOWN') {
			content = $('#' + this.mardownID).html();
		}
		return content;
	}
};

function makeAjaxTip() {
	if ($.smallipop) {
		$('.avatar,.subjectavatar').attr('title', ' ');
		$('.avatar,.subjectavatar').smallipop(
				{
					preferredPosition : 'top',
					hideOnTriggerClick : false,
					hideOnPopupClick : false,
					invertAnimation : true,
					hideDelay : 300,
					theme : 'blue',
					onBeforeShow : function(evt) {
						var jTarget = $(evt[0]);
						var jContent = jTarget.attr("tooltip_content");
						var url = basePath + '/member/overview?user='
								+ jTarget.attr('user');
						if (jTarget.hasClass('subjectavatar')) {
							url = basePath + '/subject/overview?subject='
									+ jTarget.attr('subject');
						}
						if (!jContent || jContent.trim() == "") {
							$.ajax({
								url : url,
								async : false
							}).done(function(data) {
								jTarget.attr('tooltip_content', data);
							});
						}
					},
					onAfterShow : function(evt) {
						$.smallipop.setContent(evt, $(evt[0]).attr(
								'tooltip_content'));

					}
				});
	}
}
/* Mở cửa sổ chat */
function openChatBox(){
	$('#faq_email_inbox').popover('hide');
	// show only if it open before.
	if($('#chat-box-content').attr('room_id')){
		$('#chat-box').attr('status','normal');
		return;
	}

	chat_utils.socket.get('/chat/getListRoom',{room_id: null}, function(res){
		console.log('load all room:');
		console.log(res);
		for(i=0;i<res.lstRoom.length;i++){
			var nametext = '<span class=name>' + res.lstRoom[i].room_name + '</span><span class=unread></span>';
			if(i==0){
				$($('.room-1th')[0]).attr('room_id', res.lstRoom[i].room_id);
				$($('.room-1th')[0]).html(nametext);
				continue;
			}
			if(i==1){
				$($('.room-2th')[0]).attr('room_id', res.lstRoom[i].room_id);
				$($('.room-2th')[0]).html(nametext);
				continue;
			}
			if(i==2){
				$($('.room-3th')[0]).attr('room_id', res.lstRoom[i].room_id);
				$($('.room-3th')[0]).html(nametext);
				continue;
			}
			$('#chat-box .room-list').append('<li room_id="' + res.lstRoom[i].room_id + '"><a href=javascript:><span class=name>' + res.lstRoom[i].room_name + '</span><span class=unread></span></a></li>');
		}
		// refresh UI
	    $('.dropdown-toggle').dropdown();
	    // change status if need (invisible -> min);
	    $('#chat-box').attr('status','normal');
	});
}
function makeAjaxNotifyChat(){
	if($.smallipop){
		$('#faq_email_inbox_value').attr('title',' ');
		$('#faq_email_inbox_value').smallipop({
			preferredPosition: 'top',
			hideOnTriggerClick: false,
			hideOnPopupClick:false,
			invertAnimation: true,
			hideDelay: 300,
			theme: 'blue',
			onBeforeShow: function(evt){
					var jTarget = $(evt[0]);
					var jContent = jTarget.attr("tooltip_content");
					var url = basePath +'/member/overview?user='+ jTarget.attr('user');
					if(jTarget.hasClass('subjectavatar')){
						url = basePath +'/subject/overview?subject=' + jTarget.attr('subject');
					}
					if(!jContent || jContent.trim()==""){
						$.ajax({
							url: url,
							async: false
						}).done(function(data){
							jTarget.attr('tooltip_content',data);
						});
					}
			},
			onAfterShow: function(evt){
					$.smallipop.setContent(evt,$(evt[0]).attr('tooltip_content'));

			}
		});
	}



}
function zoomIMG(img) {

}

function showPreview() {
};
function makeAjaxTipQuestionInfo() {
	if ($.smallipop) {
		$(
				'.faq_total_comment_question[num!="0"],.faq_total_follow_question[num!="0"], .faq_total_share_question[num!="0"], .faq_total_spam_question[num!="0"]')
				.attr('title', ' ');
		$(
				'.faq_total_comment_question[num!="0"],.faq_total_follow_question[num!="0"], .faq_total_share_question[num!="0"], .faq_total_spam_question[num!="0"]')
				.smallipop(
						{
							preferredPosition : 'bottom',
							hideOnTriggerClick : false,
							hideOnPopupClick : false,
							invertAnimation : true,
							hideDelay : 300,
							theme : 'blue',
							onBeforeShow : function(evt) {
								var jTarget = $(evt[0]);
								var jContent = jTarget.attr("tooltip_content");
								var url_comment = basePath
										+ '/question/premember-answer';
								var url_follow = basePath
										+ '/question/premember-follow';
								var url_share = basePath
										+ '/question/premember-share';
								var url_spam = basePath
										+ '/question/premember-spam';
								if (jTarget
										.hasClass('faq_total_comment_question')) {
									url = url_comment;
								}
								if (jTarget
										.hasClass('faq_total_follow_question')) {
									url = url_follow;
								}
								if (jTarget
										.hasClass('faq_total_share_question')) {
									url = url_share;
								}
								if (jTarget.hasClass('faq_total_spam_question')) {
									url = url_spam;
								}
								if (!jContent || jContent.trim() == "") {
									$.ajax({
										url : url,
										async : false,
										type : "POST",
										dataType : "html",
										data : "question=" + jTarget.attr('id')
									}).done(function(data) {
										jTarget.attr('tooltip_content', data);
									});
								}
							},
							onAfterShow : function(evt) {
								$.smallipop.setContent(evt, $(evt[0]).attr(
										'tooltip_content'));
							}
						});
	}
}
// ready function
$(document)
		.ready(
				function() {
					makeAjaxTip();
					makeAjaxTipQuestionInfo();
					$(document)
							.on(
									"click",
									".faq_btn_follow_subject",
									function() {
										if (!isAllowed) {
											redirectLogin();
											return;
										}
										// Get the ID of the button that was
										// clicked on
										var subjectID = $(this).attr("subject");
										var actionCode = $(this).attr("action");
										var btn=this;
										console.log("subject :" + subjectID);
										console.log("action :" + actionCode);
										$.ajax({
													url : basePath
															+ "/subject/action-subject",
													type : "POST",
													dataType : "json",
													data : {
														subject : subjectID,
														action : actionCode
													}, // The data your sending
														// to page
													success : function(data) {

														// follow scuccess
														if (data.status === 1) {
															$(btn).val( statusFollow);
															$(btn).text( statusFollow);
															$(btn).removeClass("btn-default");
															$(btn).addClass("btn-warning");
															$(btn).attr("title",'Click để "'+actionUnfollow+'"');
															$(btn).attr("action",2);
															// unfollow scucess
														} else if (data.status === 2) {
															$(btn).val(statusUnfollow);
															$(btn).text(statusUnfollow);
															$(btn).removeClass("btn-warning");
															$(btn).addClass("btn-default");
															$(btn).attr('title','click để "'+actionFollow+'"');
															$(btn).attr("action",1);
														}
													},
													error : function() {
														console
																.log("AJAX request was a failure");
													}
												});
									});

					$(document)
							.on(
									"click",
									"div.faq_member_action ul.dropdown-menu li > a",
									function() {

										if (!isAllowed) {
											redirectLogin();
											return;
										}
										// Get the ID of the button that was
										// clicked on
										var userID = $(this).attr("user");
										var actionCode = $(this).attr("action");
										console.log("user :" + userID);
										console.log("action :" + actionCode);
										var spanData = $(this);
										$
												.ajax({
													url : basePath
															+ "/member/action-member",
													type : "POST",
													dataType : "json",
													data : {
														user : userID,
														action : actionCode
													},
													success : function(data) {
														var liParent = $(
																spanData)
																.parent();
														var ulParent = $(
																liParent)
																.parent();
														var divParent = $(
																ulParent)
																.parent();
														var labelAction = $(
																divParent)
																.children("a");
														console
																.log(labelAction);
														console.log(spanData);
														// follow scuccess
														if (data.status === 1) {

															$(labelAction)
																	.html(
																			statusFollow
																					+ '<b class="caret"></b>');
															$(labelAction)
																	.removeClass(
																			"btn-info");
															$(labelAction)
																	.addClass(
																			"btn-success");
															$(spanData)
																	.text(
																			actionUnfollow);
															$(spanData)
																	.attr(
																			"action",
																			2);
															// unfollow scucess
														} else if (data.status === 2) {
															$(labelAction)
																	.html(
																			statusUnfollow
																					+ '<b class="caret"></b>');
															$(labelAction)
																	.removeClass(
																			"btn-success");
															$(labelAction)
																	.addClass(
																			"btn-info");
															$(spanData)
																	.text(
																			actionFollow);
															$(spanData)
																	.attr(
																			"action",
																			1);
														}
													},
													error : function() {
														console
																.log("AJAX request was a failure");
													}
												});
									});

					!function($) {
						$(function() {
							window.prettyPrint && prettyPrint();
						});
					}(window.jQuery);


					// convert time to string
					$(".faq_post_time").text(function(index, text) {

						var datatime = parseInt($(this).attr('post-time'));
						$(this).text(moment(new Date(datatime)).fromNow());

					});

					// menu left user category
					$(document).on(
							'click',
							".faq_title_user_subcategory",
							function() {
								$(".faq_title_user_subcategory").removeClass(
										'faq_category_actvie');
								$(this).addClass('faq_category_actvie');
							});

					// menu subject category
					$(document).on(
							'click',
							".faq_title_subcategory",
							function() {
								$(".faq_title_subcategory").removeClass(
										'faq_category_actvie');
								$(this).addClass('faq_category_actvie');
							});
					// create account
					$(document).on('click', "#faq_btn_signup", function() {
						location.href = basePath + "/user/signup";
					});

					// execute tooltip
					$(".faq_tooltip").tooltip({
						'html' : 'true'
					});

					// hidden pop inbox,message, subject, member when wrap click
					$(document).on('click', '.container', function() {
						$('#faq_notify_inbox').popover('hide');
						$('#faq_email_inbox').popover('hide');
						$('.subjectavatar').popover('hide');
						$('.avatar').popover('hide');
					});
					// inbox message pop
					$('#faq_notify_inbox')
							.popover(
									{
										content : function() {
											$('#faq_email_inbox').popover(
													'hide');
											$
													.ajax(
															{
																url : basePath
																		+ '/message/notify',
															// async: false
															})
													.done(
															function(data) {
																$(
																		"#faq_notify_inbox_value")
																		.text(
																				"0");
																$(
																		'#faq_notify_inbox')
																		.popover(
																				'destroy');

																$(
																		'#faq_notify_inbox')
																		.popover(
																				{
																					content : data,
																					html : true,
																					trigger : 'click',
																					container : '#notify_tooltip_container',
																					placement : 'bottom'
																				});

																$(
																		'#faq_notify_inbox')
																		.popover(
																				'show');

															});
											return "<center><img src='/images/ajax-loader.gif'></center></center>";
											// return content;
										},
										delay : {
											hide : 10
										},
										html : true,
										trigger : 'click',
										container : '#notify_tooltip_container',
										placement : 'bottom'
									});
					$('#faq_notify_inbox').on(
							'hide.bs.popover',
							function(evt) {

								if ('yes' == $('#notify_tooltip_container')
										.attr('over_tooltip')) {
									evt.preventDefault();
								}

							});

					// inbox message
					// inbox message pop
					$('#faq_email_inbox').popover({
						content : function() {
							$('#faq_notify_inbox').popover('hide');
							var content = '';
							var avatar = $(this);
							if (avatar.attr('tooltip_content')) {
								return $(this).attr('tooltip_content');
							}
							$.ajax({
								url : basePath + '/message/inbox',
								async : false
							}).done(function(data) {
								// call reset notify
								chat_utils.resetNotify();
								avatar.attr('tooltip_content', data);
								content = data;
							});
							return content;
						},
						title:  function(){
												return "<div class='col-lg-12'><i>" +  $('#faq_email_inbox').title + "</i></div>";
											},
						delay : {
							hide : 10
						},
						html : true,
						trigger : 'click',
						container : '#notify_tooltip_container',
						placement : 'bottom'
					});
					$('#faq_email_inbox').on(
							'hide.bs.popover',
							function(evt) {
								if ('yes' == $('#notify_tooltip_container')
										.attr('over_tooltip')) {
									evt.preventDefault();
								}

							});

					$('#notify_tooltip_container').mouseenter(function() {
						$(this).attr('over_tooltip', 'yes');
					});

					$('#notify_tooltip_container').mouseleave(function() {
						$(this).attr('over_tooltip', 'no');
						$('#faq_notify_inbox').popover('hide');
					});

					// seach
					$('#faq_txt_key_search').autocomplete({
						serviceUrl : basePath + '/search/find',
						onSelect : function(suggestion) {
							$('#faq_txt_key_search').val(suggestion.value);
							$("#faq_search").click();
						}
					});

					// seach
					$('#faq_question_title').autocomplete(
							{
								serviceUrl : basePath + '/search/question',
								onSelect : function(suggestion) {
									location.href = "http://" + domain
											+ suggestion.data;
								}
							});


					// action creat question

					$("#faq_btn_create_question")
							.click(
									function() {
										if (!isAllowed) {
											redirectLogin();
											return;
										}
										if (!faquser
												.isPrivilegeByPoint(CREATE_POSTS)) {
											bootbox
													.alert("Số điểm câu hỏi của bạn không đủ để đặt câu hỏi");
											return;
										}
										window.location.href = basePath+"/";
									});

					// //auto click tag input focus out
					// $(document).on("focusout",".bootstrap-tagsinput",function(event){
					// console.log("out 1");
					// $(this).keypress( function(event) {
					// console.log("out 2");
					// event.which= 13;
					// return event.which;
					// });
					// $(this).keypress();
					// event.which=13;
					// return event.which;
					// });

					// toggle avatar subject
					$(".overview-subject-home-toggle").click(function() {
						$("#overview-subject-home").toggle("slow");
					});

					// add class prettyprint
					$("pre").addClass("prettyprint");
					$("pre").addClass("linenums");
					// $("code").addClass("prettyprint");

					$("#faq_link_login_fancy").fancybox({
						'titlePosition' : 'inside',
						'transitionIn' : 'none',
						'transitionOut' : 'none'
					});
					// show image to top
					scrolltotop.init();

					// latex
					$("#faq_btn_tool_latex").click(function() {
						$("#faq_tool_latex").toggle();
					});
					// login
					$(".faq_link_login").click(function() {
						redirectLogin();
					});

					$(document).on(
							"click",
							".faq_read_more_preview",
							function() {
								var question = $(this).attr("question");
								$(this).addClass("faq_display_none");
								$(
										".faq_read_less_preview[question='"
												+ question + "']").removeClass(
										"faq_display_none");
								$(
										".faq_preview_content_question[question='"
												+ question + "']").removeClass(
										"faq_max_height_preview");

							});
					$(document).on(
							"click",
							".faq_read_less_preview",
							function() {
								var question = $(this).attr("question");
								$(this).addClass("faq_display_none");
								$(
										".faq_read_more_preview[question='"
												+ question + "']").removeClass(
										"faq_display_none");
								$(
										".faq_preview_content_question[question='"
												+ question + "']").addClass(
										"faq_max_height_preview");
								$(
										".faq_go_content_question[question='"
												+ question + "']").click();

							});

					// fix nav
					$(".navbar-inverse").fixTo("#footer");





				});
