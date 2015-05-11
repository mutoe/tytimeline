(function($) {

	/**
	 * 返回顶部
	 */
	$("#returnTop").click(function() {
		$("html, body").animate({
			scrollTop: 0
		}, 1000)
	});

	$(window).bind("scroll", function() {
		var scrollTop = $(document).scrollTop();
		scrollTop > 660 ? $("#returnTop").css("bottom", "30px") : $("#returnTop").css("bottom", "-100px");
	});

})(jQuery);


/**
 * “喜欢”
 * @param {Object} element
 * example. <button onclick="like(share_id)"><i></i></button>
 */
function like(share_id,e) {
  $.ajax({
  	url: ROOT + "/Shard/like",
  	data: {"share_id": share_id},
  	success: function(data) {
  		if(data.status) {
	  		if (data.info == "1") {
					modalPopup('已经收藏');
		  		//将喜欢按钮变为不再喜欢，更新图标
					e && $(e).children('i').removeClass('am-icon-heart-o').addClass('am-icon-heart');
				} else {
					e && $(e).children('i').removeClass('am-icon-heart').addClass('am-icon-heart-o');
				}
  		} else {
  			modalPopup(data.info, false);
  		}
  	}
  });
}

/**
 * 模态登陆
 */
function modalLogin() {
	$.ajax({
		type: "POST",
		url: ROOT + "/Base/syncHtml",
		data: {modal: 'login'},
		success: function(data) {
			if (data.status) {
				$("#modal-container").html(data.info);
			  $('#modal-login').modal();
			} else {
				modalPopup("没有找到登陆模态窗口，请联系管理员", false);
			}
		}
	});
}

/**
 * popup 提示窗口
 * @param {String} info
 * @param {Boolean} success
 */
function modalPopup(info,status,title) {
	var status = arguments[1] == null ? 'success' : arguments[1];
	$.ajax({
		url: ROOT + "/Base/syncHtml",
		data: {
			modal: 'popup',
			info: info,
			status: status,
			title: title
		},
		success: function(data) {
			if (data.status) {
				$("#modal-container").html(data.info);
			  $('#modal-popup').modal();
			} else {
				modalPopup("没有找到模态窗口，请联系管理员", false);
			}
		}
	});
}

function modalConfirm(fun,info,title) {
	$.ajax({
		url: ROOT + "/Base/syncHtml",
		type: "POST",
		data: {
			modal: 'confirm',
			info: info,
			title: title
		},
		success: function(data) {
			if (data.status) {
				$("#modal-container").html(data.info);
			  $('#modal-confirm').modal({
	        relatedTarget: this,
	        onConfirm: function(options) {
						if (typeof fun === 'function') {
							fun();
						} else {
							location.href = fun;
						}
	        }
	      });
			} else {
				modalPopup("没有找到模态窗口，请联系管理员", false);
			}
		}
	});
}

function checkLogout() {
	modalConfirm(function() {
		$.ajax({
			url: ROOT + "/Base/logout",
			success: function(data) {
				if (data.status) {
	        modalPopup('注销成功，正在返回首页...');
					setTimeout(function() {
						location.href = ROOT + "/Index/index.html";
					}, 2000);
				} else {
					modalPopup('失败了，请联系管理员');
				}
			}
		});
	},"你确实要注销登陆吗？");
	return false;
}

function modalSign() {
	$.ajax({
		url: ROOT + "/Base/syncHtml",
		type: "POST",
		data: {modal: 'sign'},
		success: function(data) {
			if (data.status) {
				$("#modal-container").html(data.info);
			  $('#modal-sign').modal();
			} else {
				modalPopup("没有找到模态窗口，请联系管理员", false);
			}
		}
	});
	return false;
}

function deleteShare(share_id) {
	modalConfirm(function() {
		$.ajax({
			url: ROOT + 'Shard/deleteShare',
			type: "POST",
			data: {share_id: share_id},
			success: function(data) {
				if (data.status) {
					modalPopup(data.info);
					location.reload(true);
				} else {
					modalPopup(data.info, false);
				}
			}
		});
	},"你确实要删除这条纪录吗?");
	return false;
}