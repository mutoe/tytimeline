
/**
 * popup 提示窗口
 * @param {String} info
 * @param {Boolean} success
 */
function modalPopup(info,status,title) {
	var status = arguments[1] == null ? 'success' : arguments[1];
	$.ajax({
		type: "post",
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
				alert(data.info);
			}
		}
	});
}

function modalConfirm(fun,info,title,fun2,yes,no) {
	$.ajax({
		url: ROOT + "/Base/syncHtml",
		type: "POST",
		data: {
			modal: 'confirm',
			info: info,
			title: title,
			yes: yes,
			no: no
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
          },
          onCancel: function(options) {
            if (typeof fun2 === 'function') {
              fun2();
            } else {
              location.href = fun2;
            }
          }
	      });
			} else {
				modalPopup(data.info, false);
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