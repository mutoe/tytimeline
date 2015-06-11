(function($) {

  // 最小高度
  if ($(window).height() > $('body').height()) {
    $('footer').css( "margin-top", $(window).height() - $('body').height() + 40);
  }

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

	$("#search-box").children('input').on('focus', function() {
		var el = $(this);
		el.animate({width: '12em'}, 'fast');
		el.next('button').fadeIn();
	});
	$("#search-box").children('input').on('blur', function() {
		var el = $(this).next('button');
		setTimeout(function() {
			el.fadeOut();
			el.prev('input').animate({width: '6em'}, 'slow');
		}, 1000);
	});

})(jQuery);

/**
 * 搜索
 */
function search() {
	var content = $("#search-box").children('input').val();
	console.log(content);
	location.href = ROOT + "/search/" + content;
	return false;
}

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


function deleteShare(share_id) {
	modalConfirm(function() {
		$.ajax({
			url: ROOT + '/Shard/deleteShare',
			type: "POST",
			data: {share_id: share_id},
			success: function(data) {
				if (data.status) {
					modalPopup("删除成功，正在返回...");
					setTimeout(function() {
						history.go(-2);
            location.reload();
					},1000);
				} else {
					modalPopup(data.info, false);
				}
			}
		});
	},"你确实要删除这条纪录吗?");
	return false;
}

function readNotice(noticeID, el) {
	$.ajax({
		type:"post",
		url: ROOT + "/user/readNotice",
		data: {notice_id: noticeID},
		success: function(data) {
		  $(el).removeClass('am-btn-primary').addClass('am-btn-default').text('朕已阅').attr('disabled', 'disabled');
		}
	});
}