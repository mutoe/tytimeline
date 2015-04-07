
	var show_year = function (year, el) {
		var y = $("#spy_"+year).offset().top;
		$(window).smoothScroll({
			position: y
		});
		//cur(year, el);//TODO:有些问题，无法展开当前月份的项目
	}

	var show_month = function (year,month, el) {
		var y = $("#spy_"+year+month).offset().top;
		$(window).smoothScroll({
			position: y
		});
		$(".current").removeClass('current');
		$(el).addClass('current');
	}

	var cur = function (year, el) {
		$(".scrubber .month").not("[id^='spy_"+year+"_']").slideUp('normal', function() {
			$("[id^='spy_"+year+"_']").slideDown();
		});
	}
