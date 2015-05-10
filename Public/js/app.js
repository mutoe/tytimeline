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
 * example. <button data-id="1" onclick="like(this)"><i></i></button>
 */
var like = function (el) {
	var el = $(el);
	var id = el.attr("data-id");
  $.ajax({
  	url: "../Shard/like",
  	data: {"share_id":id},
  	success: function(data) {
  		if(data.status) {
	  		if (data.info == "1") {
		  		//将喜欢按钮变为不再喜欢，更新图标
					el.children('i').removeClass('am-icon-heart-o').addClass('am-icon-heart');
				} else {
					el.children('i').removeClass('am-icon-heart').addClass('am-icon-heart-o');
				}
  		} else {
  			alert(data.info);
  		}
  	},
  	error: function(data) {
  		alert(data.info);
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
				alert("没有找到登陆模态窗口，请联系管理员");
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
		url: ROOT + "/Base/syncHtml.html",
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
				alert("没有找到模态窗口，请联系管理员");
			}
		}
	});
}

function modalConfirm(fun,info,title) {
	$.ajax({
		url: ROOT + "/Base/syncHtml.html",
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
				alert("没有找到模态窗口，请联系管理员");
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
}

function modalSign () {
	$.ajax({
		url: ROOT + "/Base/syncHtml.html",
		type: "POST",
		data: {modal: 'sign'},
		success: function(data) {
			if (data.status) {
				$("#modal-container").html(data.info);
			  $('#modal-sign').modal();
			} else {
				alert("没有找到模态窗口，请联系管理员");
			}
		}
	});
}