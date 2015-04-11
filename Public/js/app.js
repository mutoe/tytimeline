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