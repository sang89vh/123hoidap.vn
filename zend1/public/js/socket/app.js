/**
 * app.js
 *
 * This file contains some conventional defaults for working with Socket.io + Sails.
 * It is designed to get you up and running fast, but is by no means anything special.
 *
 * Feel free to change none, some, or ALL of this file to fit your needs!
 */
CHAT_DOMAIN = '123hoidap.vn:1337';
CHAT_AUTH_LINK = 'http://123hoidap.vn:1337';
Array.prototype.remove = function(idx){
	var len = this.length;
	if(idx==len-1){
		this.pop(); return;
	}
	if(idx>=0 && idx<=len-2){
		for(i=idx;i<=len-2;i++){
			this[i] = this[i+1];
		}
		this.pop();
	}
}
function Chat_name(code, value){
	this.code = code;
	this.value = value;
}

function updateQuestionAnswerLevel1(html){
	 $("#faq_comment_question").append(html);
}


function updateQuestionAnswerLevel2(reply_id, html){
	$(".faq_feekback_replies[answer='"+reply_id+"']").append(html);
}
function Chat_Utils(){
	this.chat_names = new Array();
	this.rooms = new Array();
	this.user_id = null;
	this.socket = null;
	this.readNotify = function(){
		socket.get('/chat/getNumsNotify', function(res){
			var nums = 0;
			if(res){
				nums = res.nums;
			}
			nums = res.nums;
			console.log('num of notify chat:' + nums);
		});
	}
	this.noticeChangePageContent = function(data){
		socket.get('/chat/changePageContent',{data:data},function(res){
			var func_code = res.func_code;
			var code_1 = res.code_1;
			var data = res.data;
		});
	}
	this.getListRoom = function(){
		socket.get('/chat/getListRoom', function(res){
			console.log(res);
			if(res.lstRoom){
				for(i=0;i<res.lstRoom.length;i++){
					var room_id = res.lstRoom[i].room_id;
					var room_name = res.lstRoom[i].room_name;
					var room_type = res.lstRoom[i].type;
					var user_id = res.lstRoom[i].user_id;
					if(room_type==0){ console.log(room_name);
						if($('#chat-users li[user_id="' + user_id + '"]').length==0){
							// user chua co trong list. can add them
							var li_user = '<li draggable=true ondragstart="chat_utils.ondragstart(event)" class=user-online user_id=' + user_id + ' aroom_id="' + room_id + '" >' +
				    		'<img src="/media/get-avatar/images/' + user_id + '" /><span class="user">' +
				    		room_name + '</span><span class="unread"></span></li>';
							$('#chat-users').append(li_user);
						}
					}
					if(room_type==1){
						if($('#chat-users li[room_id="' + room_id + '"]').length==0){
							// room chua co trong list. can add them
							var li_room = '<li draggable=true ondragstart="chat_utils.ondragstart(event)" class=user-online room_id=' + room_id + '><span>' +
				    		room_name + '</span><span class="unread"></span></li>';
							$('#chat-users').append(li_room);
						}
					}

					// room default.
					if(typeof chat_room_id !='undefined' && chat_room_id){
						$('.user-online[aroom_id="' + chat_room_id + '"]').click();
						$('.user-online[room_id="' + chat_room_id + '"]').click();
					}else{
						$('.user-online:first').click();
					}
				}
			}
		});
	}

	// reset notify to zero
	this.resetNotify = function(){
		chat_utils.socket.get('/chat/resetNotify', function(res){
			console.log('reset notify:ok');
			console.log(res);
		});
	}

	/*
	 * add room or user
	 * type: 0{user}, 1{room}
	 */
	this.addRoom = function(type, room_id, user_id, name){
		if(type==0){
			if($('#chat-users li[user_id="' + user_id + '"]').length==0){
				var li_user = '<li draggable=true ondragstart="chat_utils.ondragstart(event)" class=user-online user_id=' + user_id + ' aroom_id="' + room_id + '" >' +
	    		'<img src="/media/get-avatar/images/' + user_id + '" /><span  class="user">' +
	    		name + '</span><span class="unread"></span></li>';
				$('#chat-users').append(li_user);
			}
		}
		if(type==1){
			if($('#chat-users li[room_id="' + room_id + '"]').length==0){
				var li_room = '<li draggable=true ondragstart="chat_utils.ondragstart(event)" class=user-online room_id=' + room_id + '><span  class="user">' +
	    		name + '</span><span class="unread"></span></li>';
				$('#chat-users').append(li_room);
			}
		}
	}

	// type: 0{box1} 1{boxn}
	this.createBox = function(type, room_id, users){
		// get history content message
		// create new room.
		room = new Room(room_id, users);
		room.type = type;
		// make UI view
		room.initBox();
		return room;
	}

	/*
	 * get message by this user, room { from -> to}
	 * su dung cho Box on all page
	 */
	this.readMessage4Page = function(room_id){
		var from = 0;
		if($('#chat-box-content').attr('num_msg')){
			from = parseInt($('#chat-box-content').attr('num_msg'));
		}
		chat_utils.socket.get('/chat/readMessage',{from:from, room_id: room_id}, function(res){
			// list message
			if(res.messages){
				for(var i=0;i<res.messages.length;i++){
					var msg = new Msg(res.messages[i].user_name, null, res.messages[i].text, null)
					msg.preMsgText();
				}
			}
		});
	}

	/*
	 * get message by this user, room { from -> to}
	 * su dung cho Thong bao -> tin nhan
	 */
	this.readMessage = function(room_id){
		var from = 0;
		if($('#chat-box-one-content').attr('num_msg')){
			from = parseInt($('#chat-box-one-content').attr('num_msg'));
		}
		console.log('read message ' + from);
		chat_utils.socket.get('/chat/readMessage',{from:from, room_id: room_id}, function(res){
			// list message
			if(res.messages){
				for(var i=0;i<res.messages.length;i++){
					var msgText  = '<p><b><a href="/member/profile/' + res.messages[i].user + '">' + res.messages[i].user_name + '</a></b>: ' + res.messages[i].text + "</p><br/>";
					$('#chat-box-one-content').prepend(msgText);
					console.log('load ' + i);

					// save num of message
					var num_msg = $('#chat-box-one-content').attr('num_msg');
					if(!num_msg){
						$('#chat-box-one-content').attr('num_msg', 1);
					}else{
						$('#chat-box-one-content').attr('num_msg', 1 + parseInt(num_msg));
					}

					// scroll to view
					if(chat_utils.chat_box_scroll_type=='top'){
						$('#chat-box-one-content').scrollTop(10);
					}else if(chat_utils.chat_box_scroll_type=='auto'){
						// do nothing - auto :(
					}else{
						$('#chat-box-one-content').scrollTop($('#chat-box-one-content')[0].scrollHeight);
					}
				}
			}
			// room info
			if(res.room){
				$('#chat-box-one-header-name span').html(res.room.room_name);
			}
		});
	}

	this.createRoom = function(room_id, users){
		var room = this.getRoom(room_id);
		if(!room){
			// create new room.
			room = new Room(room_id, users);
			// Add room to list
			chat_utils.rooms.push(room);
			// make UI view
			room.initUI();
		}else{
			// room da ton tai. chi them user
			room.user = users;
			this.reinitUI();
		}
		return room;
	};
	this.removeRoom = function(room_id){
		var room = this.getRoom(room_id);
		if(room){
			// remove room from list
			var idx = this.rooms.indexOf(room);
			this.rooms.remove(idx);
			// remove ui
			room.removeUI();
		}else{
			// no room to remove
		}
	}
	this.getRoom = function(room_id){
		var room = null;
		this.rooms.forEach(function(proom){
			if(proom.id==room_id){
				room = proom;
			}
		});
		return room;
	}
	this.addName = function(code, value){
		var isExist = false;
		var i = 0;
		for(i=0;i<this.chat_names.length;i++){
			if(this.chat_names[i].code ==code){
				isExist = true;
			}
		}
		if(!isExist){
			var chat_name = new Chat_name();
			chat_name.code = code;
			chat_name.value = value;
			this.chat_names.push(chat_name);
		}
	};
	this.getName = function(code){
		var name = null;
		var i = 0;
		for(i=0;i<this.chat_names.length;i++){
			if(this.chat_names[i].code ==code){
				name = chat_names[i].value;
			}
		}
		if(!name){
			name = code;
			// update name
		}
		return name;
	};
	this.allowDrop = function(evt){
		evt.preventDefault();
	}
	this.ondragstart= function(evt){
		evt.dataTransfer.setData("user-id",evt.target.getAttribute('user_id'));
	}
	this.ondrop= function(evt){
		evt.preventDefault();
		var user_id =evt.dataTransfer.getData("user-id");
		var room_id = evt.target.getAttribute('room-id');
		var isExist = false;
		if(!room_id){
			room_id = evt.target.parentElement .getAttribute('room-id');
		}
		if(!user_id || !room_id){
			return;
		}
		if(user_id==this.user_id){
			return;
		}else{
			var room = this.getRoom(room_id);
			room.user.forEach(function(user){
				if(user.toString()==user_id.toString()){
					isExist = true;
				}
			})
		}
		if(isExist) return;
			// add new user to group
			socket.get('/chat/addUser', {user_id: user_id, room_id: room_id }, function(res){
			var data = res.addUser;
			if(!data) {
				return;
			}
			var users = data.users;
			var old_room_id = data.old_room_id;
			var new_room_id = data.new_room_id;
			var new_user_id = data.new_user_id;
			if(!users || !old_room_id || !new_room_id){
				return;
			}
			if(old_room_id != new_room_id){
				// them mot room moi
				room_new = chat_utils.createRoom(new_room_id, users);
			}else{
				// room da co, check la tao dung chua de them moi.
			}
		});
	}
}
chat_utils = new Chat_Utils();
function Room(id, users){
	this.id = id;
	this.msg = new Array();
	this.user = new Array();
	this.type = 0; // room1 is default
	if(users){
		this.user = users;
	}
	this.initBox = function(){
		$('#chat-box-one').attr('room-id', this.id);
		$('#chat-users li').removeClass('active');
		if(this.type==0){
			$('#chat-users [user_id="' + this.user[0] + '"]').addClass('active');
		}else{
			$('#chat-users [room_id="' + this.id + '"]').addClass('active');
		}

		// read message from room
		var room_id = $('#chat-box-one').attr('room-id');
		$('#chat-box-one-content').html('');
		this.readMessage(room_id,0);
	}
	/*
	 * get message by this user, room { from -> to}
	 * su dung cho Thong bao -> tin nhan
	 */
	this.readMessage = function(room_id, from, num_msg){
		chat_utils.socket.get('/chat/readMessage',{from:from, room_id: room_id}, function(res){
			// list message
			if(res.messages){
				for(var i=0;i<res.messages.length;i++){
					var msgText  = '<p><b><a href="/member/profile/' + res.messages[i].user + '">' + res.messages[i].user_name + '</a></b>: ' + res.messages[i].text + "</p><br/>";
					$('#chat-box-one-content').prepend(msgText);

					// save num of message
					var num_msg = $('#chat-box-one-content').attr('num_msg');
					if(!num_msg){
						$('#chat-box-one-content').attr('num_msg', 1);
					}else{
						$('#chat-box-one-content').attr('num_msg', 1 + parseInt(num_msg));
					}

					// scroll to view
					if(chat_utils.chat_box_scroll_type=='top'){
						$('#chat-box-one-content').scrollTop(10);
					}else if(chat_utils.chat_box_scroll_type=='auto'){
						// do nothing - auto :(
					}else{
						$('#chat-box-one-content').scrollTop($('#chat-box-one-content')[0].scrollHeight);
					}
				}
			}
			// room info
			if(res.room){
				$('#chat-box-one-header-name span').html(res.room.room_name);
			}
		});
	}
	/*
	 * get message by this user, room { from -> to}
	 * su dung cho Box on all page
	 */
	this.readMessage4Page = function(room_id, from, num_msg){
		chat_utils.socket.get('/chat/readMessage',{from:from, room_id: room_id}, function(res){
			// list message
			if(res.messages){
				for(var i=0;i<res.messages.length;i++){
					var msg = new Msg(res.messages[i].user_name, null, res.messages[i].text, null)
					msg.preMsgText();
				}
			}
		});
	}

	this.initUI = function(){
		var abox = $('#abox div:first').clone();
		var lst_user = '';
		abox.attr('room-id', this.id);
		for(i=0;i<this.user.length;i++){
			lst_user = lst_user + '<span>' + this.user[i] + '</span>   ';
		}
		abox.find('.list-user').html(lst_user);
		abox.appendTo('#chat-boxs');
	};
	this.reinitUI = function(){
		var abox = $('[room-id="' + this.id + '"]');
		var lst_user = '';
		for(i=0;i<this.user.length;i++){
			lst_user = lst_user + '<span>' + this.user[i] + '</span>    ';
		}
		abox.find('.list-user').html(lst_user);
	};
	this.removeUI = function(){

		$('div[room-id="' + this.id + '"]').remove();
	}
}
function Msg(user_name, room, text, date){
	this.user = user_name;
	this.text = text;
	this.date = date;
	this.room = room;
	this.msgText = function(){
		var msgtext = '<b>' + user_name + '</b>';
		msgtext = msgtext + ' : ';
		msgtext = msgtext + this.text;
		msgtext = msgtext + "<br/>";
		$('#chat-box-content').append(msgtext);

		// save num of message
		var num_msg = $('#chat-box-content').attr('num_msg');
		if(!num_msg){
			$('#chat-box-content').attr('num_msg', 1);
		}else{
			$('#chat-box-content').attr('num_msg', 1 + parseInt(num_msg));
		}

		// scroll to view
		if(chat_utils.chat_box_scroll_type=='top'){
			$('#chat-box-content').scrollTop(10);
		}else if(chat_utils.chat_box_scroll_type=='auto'){
			// do nothing - auto :(
		}else{
			$('#chat-box-content').scrollTop($('#chat-box-content')[0].scrollHeight);
		}
	};
	this.preMsgText = function(){
		var msgtext = '<b>' + user_name + '</b>';
		msgtext = msgtext + ' : ';
		msgtext = msgtext + this.text;
		msgtext = msgtext + "<br/>";
		$('#chat-box-content').prepend(msgtext);

		// save num of message
		var num_msg = $('#chat-box-content').attr('num_msg');
		if(!num_msg){
			$('#chat-box-content').attr('num_msg', 1);
		}else{
			$('#chat-box-content').attr('num_msg', 1 + parseInt(num_msg));
		}

		// scroll to view
		if(chat_utils.chat_box_scroll_type=='top'){
			$('#chat-box-content').scrollTop(10);
		}else if(chat_utils.chat_box_scroll_type=='auto'){
			// do nothing - auto :(
		}else{
			$('#chat-box-content').scrollTop($('#chat-box-content')[0].scrollHeight);
		}
	};
}
function initSocket(){
	if(window.socket){
		if(window.socket.connected || window.socket.connecting){
			return;
		}
	}
(function (io) {


  // as soon as this file is loaded, connect automatically,
  var socket = io.connect(CHAT_DOMAIN);
  socket.on('connect', function socketConnected() {
	  console.log('ws connected');
	  chat_utils.socket = socket;
	  // khoi tao list room chat.
	  chat_utils.getListRoom();
	  chat_utils.readNotify();

	// TODO. Su kien gui tin nhan
	$(document).on('keypress','.chat-msg',function(evt){
		if(evt.keyCode==13){
			$(this).parent().find('.chat-send').click();
		}
	});

	// TODO. send a message
	$(document).on('click','.chat-send',function(evt){
		var uibox = $($(this).parent()).parent();
		var text = uibox.find('.chat-msg:first').val();
		var room_id = uibox.attr('room-id');
		if(text!=""){
			uibox.find('.chat-msg:first').val('');
			socket.get('/chat/send',{room: room_id, text: text},function(res){
					// send message
			});
		}

	});

	// TODO. send a message
	$(document).on('keyup','#chat-box-one-cosole-msg',function(evt){
		var code = evt.which;
		var text = "";
		var room_id = "";
		if(code==13){
			text = $('#chat-box-one-cosole-msg').val();
			room_id = $('#chat-box-one').attr('room-id');
			if(text!=""){
				socket.get('/chat/send',{room: room_id, text: text},function(res){
					$('#chat-box-one-cosole-msg').val('');
				});
			}
		}
	});

	// TODO. close room
	$(document).on('click','.chat-box .close',function(evt){
		var room_id = $($(this).parent()).parent().attr('room-id');
		if(room_id){
			chat_utils.removeRoom(room_id);
		}
	});


	// TODO. Su kien tao room
	$(document).on('click','.user-online',function(evt){
		console.log('user online - click');
		var userID = $(this).attr('user_id');
		var roomID = $(this).attr('room_id');
		// clear unread
		$(this).attr('unread',0);
		$(this).find('.unread').html('');
		$('#chat-box-one-content').attr('num_msg',0);
		$('#chat-box-one-content').attr('scroll-type','bottom');
		// create room(1).
		if(userID)
		socket.get('/chat/createRoom', {userid: userID}, function(res){
			if(res){
				chat_utils.user_id = res.user;
				if(res.room_id){
					var room_id = res.room_id;
					var another_user = [userID];
					var room = null;
					room = chat_utils.createBox(0, room_id, another_user);
				}else{
					// nothing - no room
				}
			}
		});

		// create room(n)
		if(roomID){

		}
	});


	/*
	 * @todo. Khi mot message duoc gui den xet 2 truong hop
	 *  - TH1. Nguoi dung dang trong box tin nhan
	 *  	- TH1.1 Box chat dang duoc mo -> cap nhat noi dung chat
	 *  	- TH1.2 Box chat ko duoc mo -> thong bao co message
	 *  - TH2. Nguoi dung ko dang trong box tin nhan
	 *  	- TH2.1 Box chat dang duoc mo -> cap nhat noi dung chat
	 *  	- TH2.2 Box chat da tao nhung khong duoc mo -> tao thong bao
	 *  	- TH2.3 Box chat khong chua room -> khoi tao room va thong bao.
	 *
	 *
	 */
    socket.on('chat/msg', function messageReceived(msg) {
    	if(msg){
    		var box_room_id = $('#chat-box-one').attr('room-id');
    		var user = msg.from;
    		var user_name = msg.from_name;
    		var text = msg.text;
    		var date = msg.date;
    		var room_id = msg.room;
			if(!user || !text || !room_id) return; // do nothing

			if(box_room_id){
				// TH1
				if(box_room_id==room_id){
					// TH1.1 cap nhat noi dung chat
					var msgText  = '<p><b><a href="/member/profile/' + user + '">' + user_name + '</a></b>: ' + text + "</p><br/>";
					$('#chat-box-one-content').append(msgText);

					// save num of message
					var num_msg = $('#chat-box-one-content').attr('num_msg');
					if(!num_msg){
						$('#chat-box-one-content').attr('num_msg', 1);
					}else{
						$('#chat-box-one-content').attr('num_msg', 1 + parseInt(num_msg));
					}

					// scroll to view
					if(chat_utils.chat_box_scroll_type=='top'){
						$('#chat-box-one-content').scrollTop(10);
					}else if(chat_utils.chat_box_scroll_type=='auto'){
						// do nothing - auto :(
					}else{
						$('#chat-box-one-content').scrollTop($('#chat-box-one-content')[0].scrollHeight);
					}
				}else{
					// TH1.2 thong bao co message moi
					$('#chatAudio')[0].play();
					var num_unread = $($('#chat-users li[user_id="' + user + '"]')[0]).attr('unread');
					if(!num_unread){
						num_unread = 0;
					}else{
						num_unread = parseInt(num_unread);
					}
					$($('#chat-users li[user_id="' + user + '"]')[0]).attr('unread', num_unread + 1);
					$('#chat-users li[user_id="' + user + '"] span.unread').html((num_unread + 1) + ' tin');
				}
				return;
			}else{
				// TH2
				if($('#chat-box').length<0){
					console.log('Not found any #chat-box');
					return;
				}
				console.log('TH2');

				var chatbox = $('#chat-box');
				var chatbox_content = $('#chat-box-content');

				if(room_id == chatbox_content.attr('room_id')){
					// TH2.1
					var msg = new Msg(user_name, room_id, text, date); console.log('TH2.1');
					msg.msgText();
					// change status if need (invisible -> min);
				    if($('#chat-box').attr('status')=='invisible'){
				    	$('#chat-box').attr('status','normal');
				    }
				    if($('#chat-box').attr('status')=='min'){
				    	$('#chatAudio')[0].play();
				    }
				}else{
					var isNewRoom = true;
					if($('#room-1th').attr('room_id')==room_id || $('#room-2th').attr('room_id')==room_id || $('#room-3th').attr('room_id')==room_id){
						isNewRoom = false;
					}
					if($('ul.room-list li[room_id="' + room_id + '"]').length>0){
						isNewRoom = false;
					}
					if(!isNewRoom){
						// TH2.2 -> thong bao
						$('#chatAudio')[0].play();
						 console.log('TH2.2');
						if($('#room-1th').attr('room_id')==room_id){
							var num_unread = $('#room-1th').attr('unread');
							if(num_unread){
								num_unread = parseInt(num_unread) + 1;
							}else{
								num_unread = 1;
							}
							$('#room-1th').attr('unread', num_unread);
							$('#room-1th').find('.unread').html('(' + num_unread + ')');
						}
						if($('#room-2th').attr('room_id')==room_id){
							var num_unread = $('#room-2th').attr('unread');
							if(num_unread){
								num_unread = parseInt(num_unread) + 1;
							}else{
								num_unread = 1;
							}
							$('#room-2th').attr('unread', num_unread);
							$('#room-2th').find('.unread').html('(' + num_unread + ')');
						}
						if($('#room-3th').attr('room_id')==room_id){
							var num_unread = $('#room-3th').attr('unread');
							if(num_unread){
								num_unread = parseInt(num_unread) + 1;
							}else{
								num_unread = 1;
							}
							$('#room-3th').attr('unread', num_unread);
							$('#room-3th').find('.unread').html('(' + num_unread + ')');
						}

						if($('.room-more').find('[room_id="' + room_id + '"]').length>0){
							// individual unread
							var num_unread = $('#room-more [room_id="' + room_id + '"]').attr('unread');
							if(num_unread){
								num_unread = parseInt(num_unread) + 1;
							}else{
								num_unread = 1;
							}
							$('#room-more [room_id="' + room_id + '"]').attr('unread', num_unread);
							$('#room-more [room_id="' + room_id + '"]').find('.unread').html('(' + num_unread + ')');
							// sum unread.
						}

						// update notify : so luong room co tin nhan moi.
						var sum_room_unread = 0;
						$('#room-more li').each(function(idx, el){
							if($(el).attr('unread')){
								if($(el).attr('unread')!=0){
									sum_room_unread++;
								}
							}
						});
						$('#room-more').attr('unread', sum_room_unread);
						if(sum_room_unread>0){
							$('#room-more .chat-more-notify').html('(' + sum_room_unread + ')');
						}else{
							$('#room-more .chat-more-notify').html('');
						}

					}else{
						// TH2.3 ->create new room, do it by load list room
						var isLoadBefore = $('#chat-box').attr('isLoadBefore');
						if(isLoadBefore){
							// load this room only
							chat_utils.socket.get('/chat/getRoom',{room_id: room_id}, function(res){
								console.log('load this room:');
								console.log(res);
							})
						}else{
							$('#chat-box').attr('isLoadBefore', true);
							chat_utils.socket.get('/chat/getListRoom',{room_id: room_id}, function(res){
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
							    // read messages.
							    $('.room-th[room_id="' + room_id + '"]').click();
							    $('ul.room-list li[room_id="' + room_id + '"]').click();

							    // change status if need (invisible -> min);
							    if($('#chat-box').attr('status')=='invisible'){
							    	$('#chat-box').attr('status','normal');
							    }

							})
						}

					}
				}
				return;
			}
    	}
    });

    socket.on('chat/resetNotify', function resetNotify(data){
    	// set notity re===
    	console.log('chat/resetNotify');
    	$('#faq_email_inbox_value').html('0');
    });

    socket.on('chat/online', function userOnline(data){
    	var text = '';
    	var users = data.users;
    	for(i=0;i<users.length;i++){
    		user = users[i];
    		text = text + '<li draggable=true ondragstart="chat_utils.ondragstart(event)" class=user-online user_id=' + user.id + '>' +
    		'<img src="/media/get-avatar/images/' + user.id + '" />' +
    		user.name + '</li>';
    	}

    	$('#chat-users').html(text);
    });

    socket.on('chat/updatePageContent', function updatePageContent(res){
    	var func_code = res.func_code;
    	var code_1 = res.code_1;
    	var data = res.data;

    	if(func_code=='question_detail'){

    		var data_obj = $.parseJSON(data);
    		console.log(data_obj);
    		var reply_id = data_obj.replyId;
    		var html = data_obj.html;
    		if(reply_id){
    			updateQuestionAnswerLevel2(reply_id, html);
    		}else{
    			updateQuestionAnswerLevel1(html);
    		}

    	}
    });

  });


  socket.on('disconnect', function socketDisConnect() {
	 console.log('disconnect...');
  });

  // Expose connected `socket` instance globally so that it's easy
  // to experiment with from the browser console while prototyping.
  window.socket = socket;


  // Simple log function to keep the example simple
  function log () {
    if (typeof console !== 'undefined') {
      console.log.apply(console, arguments);
    }
  }


})( window.io);
}