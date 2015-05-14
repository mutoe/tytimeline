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
				modalPopup(data.info, false);
			}
		}
	});
	return false;
}
