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

function Inc (temp) {
	$("#Inc").remove();
	$("#pubu").append('<div id="Inc"><b><i class="am-icon-heart"></i> 已喜欢<\/b><\/div>');
	$('#Inc').css({
		'text-align': 'center',
		'font-size': '1.4rem',
		'position': 'fixed',
		'z-index': '200',
		'color': '#C30',
		'left': '10px',
		'bottom': '100px',
		'width': '20rem',
		'border-radius': '4px',
		'background-color': 'gold',
		'box-shadow': '1px 1px 2px'
	});
	$('#Inc').animate({
		top: top - 200+'px',
		opacity: '0'
	}, 3000,
	function() {
		$(this).fadeOut(1000).remove();
	});
}