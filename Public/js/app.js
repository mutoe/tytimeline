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


	$('#model-login').avgrund({
		height: 340,
		closeByDocument: false, // ..and by clicking document itself
		holderClass: 'model-login',
		onBlurContainer: '.container',
		template: '<form class="am-form" data-am-validator>'+
  	'<fieldset>'+
  		'<legend><div class="logo"></div> 登陆</legend>'+
	    '<label for="model-nickname">用户名:</label>'+
	    '<input type="text" id="model-nickname" name="nickname" minlength="2" maxlength="24">'+
	    '<br />'+
	    '<label for="model-passwd">密码:</label>'+
	    '<input type="password" id="model-passwd" name="password" minlength="3" maxlength="32">'+
	    '<br />'+
	    '<div class="am-cf">'+
	      '<input type="button" value="登录" class="am-btn am-btn-primary am-fr" onclick="modelLogin()">'+
	      '<a class="am-btn am-btn-default am-fl" href="http://bbs.cqjtu.edu.cn/register.php">没有帐号 ^_^?</a>'+
	    '</div>'+
    '</fieldset>'+
  '</form>'
	});

	$('#model-popup').avgrund({
		height: 200,
		width: 480,
		holderClass: 'model-popup',
		onBlurContainer: '.container',
		template: '<div class="header">'+
				'<h2 class="am-text-success am-text-bottom"><i class="am-icon-smile-o am-icon-md"></i> <span class="title">成功了</span> <span class="am-text-sm">天佑时光轴</span></h2>'+
			'</div>'+
			'<hr />'+
			'<div class="info">欢迎回来 上士 木头没有强迫症</div>'
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
var modelLogin = function() {
	$(".avgrund-close").click();
	$.ajax({
		type:"post",
		url: ROOT + "/Login/index.html",
		data: {
			'nickname': $('#model-nickname').val(),
			'password': $("#model-passwd").val()
		},
		success: function(data) {
			if (data.status) {
				$(".avgrund-close").click();
				setTimeout(function() {
					popup('登陆成功,正在返回...');
				}, 501);
				setTimeout(function() {
					location.reload(true);
				}, 3000);
			} else {
				popup('登陆失败:' + data.info, false);
			}
		}
	});
}

var modelLogout = function() {
	if ( confirm("您确实要登出吗？") ) {
		$.ajax({
			url: ROOT + "/Base/logout.html",
			success: function(data) {
				if (data.status) {
					setTimeout(function() {
						popup('登出成功,正在返回首页...');
					}, 501);
					setTimeout(function() {
						location.href = ROOT;
					}, 1000);
				} else {
					popup('登陆失败:' + data.info, false);
				}
			}
		});
	}
}

var modelSign = function() {
	popup(
		'本站已接入校内论坛，请<a href="http://bbs.cqjtu.edu.cn/register.php">移步论坛</a>进行注册，或<a href="'+ ROOT +'/Login/index.html">点此使用论坛帐号进行登陆</a>',
		true, '请使用论坛帐号进行登陆'
	);
}

/**
 * popup 提示窗口
 * @param {String} info
 * @param {Boolean} success
 */
var popup = function(info,success,title) {
	var success = arguments[1] ? arguments[1] : true;
	var title = arguments[2] ? arguments[2] : null;
	$("#model-popup").click();
	if(!success) {
		$(".model-popup .header h2").removeClass('am-text-success').addClass('am-text-error');
		$(".model-popup .header h2 i").removeClass('am-icon-smile-o').addClass('am-icon-times-circle');
		$(".model-popup .header h2 .title").html('出错了');
	}
	if(title != null) $(".model-popup .header h2 .title").html( title );
	$(".model-popup .info").html( info );
}
