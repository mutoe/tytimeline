(function($) {

  // 如果窗口高度过低则隐藏footer
  if ($(window).height() < 880) {$(".footer").hide().height(0);}

  // 时光轴（胶片）高度修正
  var foo = $(window).height() - $(".am-collapse").outerHeight() - $(".footer").outerHeight() - $(".index").outerHeight() - $(".stripes").height() * 2;
  $(".timeline").height(foo).css('top',$(".am-collapse").outerHeight() + $(".index").outerHeight() + $(".stripes").outerHeight());
  $(".stripes").width($(".container").width() / 6 + $(window).width());
  $(".stripes.bottom").css('top', $(".am-collapse").outerHeight() + $(".index").outerHeight() + $(".timeline").outerHeight() + $(".stripes").height());

  // 时光轴内最后一个区块的右外边距修正
  $(".block").eq(-2).css('margin-right', 0);

  // footer高度修正
  $(".footer-timeline").css('top',$(".am-collapse").outerHeight() + $(".timeline").outerHeight() + $(".index").outerHeight() + 2 * $(".stripes").height() - 1);

  // 时光轴头尾内容高度修正
  $(".future,.pass").width($(window).width() / 2).height($(window).height() * 0.75);

  // 不同年份下的月份分离
  var current = current || 0;
  $(".index ul.month li").each(function() {
    if(current == 0) {
      current = $(this).data('year');
    } else if(current != $(this).data('year')) {
      $(this).css('margin-left','+=80px');
      current = $(this).data('year');
    }
  });

  // 年份中心位置修正
  $(".index ul.year li").eq(0).addClass('current');
  var foo = $(".index ul.year li").eq(0).offset().left;
  $(".index ul.year").css('left', $(window).width() / 2 - foo);
  // 月份中心位置修正
  $(".index ul.month li").eq(0).addClass('current');
  $(".index ul.month").css('left', $(window).width() / 2);

  $(".pic").each(function() {
    $(this).width($(this).children('img').width());
  });

})(jQuery);

var perYearWidth = $(".index ul.year li").outerWidth();
var blockPositionArray = new Array();
$(".block").each(function() {
  blockPositionArray.push(parseInt($(this).position().left));
});
blockPositionArray.shift();
blockPositionArray.shift();

// FireFox 下的滚动检测
if (document.addEventListener) {document.addEventListener('DOMMouseScroll', scrollFunc, false);}
// IE/Opear 下的滚动检测
window.onmousewheel = document.onmousewheel = scrollFunc;

// 检测到滚动时执行的方法
function scrollFunc (e) {
  var direct = 0;
  e = e || window.event;
  if(e.wheelDelta) {
    var slide = e.wheelDelta > 0;
  } else if(e.detail) {
    var slide = e.detail > 0;
  }
  var el = $(".timeline");
  var el2 = $(".stripes");
  if(slide) {
    el.stop();
    el2.stop();
    el.css({'left':'-=360px'});
    el2.css({'left':'-=60px'});
    var width = $(window).width() - el.width();
    setTimeout(function() {
      if (parseInt(el.css('left')) < width) {
        el.stop(true);
        el2.stop(true);
        el.css({'left': width});
        el2.css({'left': width / 6});
      }
      indexPositionFix();
    }, 300);
  } else {
    el.stop();
    el2.stop();
    el.css({'left':'+=360px'});
    el2.css({'left':'+=60px'});
    setTimeout(function() {
      if (parseInt(el.css('left')) > 0) {
        el.stop(true);
        el2.stop(true);
        el.css({'left':0});
        el2.css({'left':0});
      }
      indexPositionFix();
    }, 300);
  }
}

// 滚动完成后各元素位置修正
function indexPositionFix() {
  // 中心位置确认
  var midPos = $(window).width() / 2 - parseInt($(".timeline").css('left'));

  // 添加当前月份高亮
  for(var index in blockPositionArray) {
    if(midPos < blockPositionArray[index]) {
      $(".index ul.month li").removeClass('current');
      $(".index ul.month").children('li[data-index="' + index +'"]').addClass('current');
      break;
    } else {
      continue;
    }
  }
  // 月份中心位置修正
  var el = $('.index ul.month li.current');
  $(".index ul.month").css('left', $(window).width() / 2 - el.position().left - parseInt(el.css('margin-left')));

  // 年份中心位置修正
  var curYear = $('.index ul.month li.current').data('year');
  $(".index ul.year li").removeClass('current');
  $(".index ul.year").children('li[data-year="' + curYear +'"]').addClass('current');
  var el = $('.index ul.year li.current');
  $(".index ul.year").css('left', $(window).width() / 2 - el.position().left - parseInt(el.css('margin-left')) - parseInt(el.css('padding-left')));
}

/**
 * 点击月份自动修正位置
 * @param {Number} idx block下的data-index属性值
 */
function jumpIndexBlock(idx) {
  var el = $(".block[data-index='"+idx+"']");
  var el2 = $(".stripes");
  var fix = $(window).width() / 2 - el.position().left - 160;
	$(".timeline").css('left', fix +"px");
	el2.css('left', fix / 6 +"px");
	setTimeout(function() {
  	indexPositionFix();
	}, 300);
}