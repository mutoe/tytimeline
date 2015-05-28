/**
 * 清除缓存
 */
function clearCache() {
	$.ajax({
		type: 'post',
		url: ROOT+"/Base/clearCache",
		success: function(data) {
			if(data.status) {
				modalPopup("清除缓存成功！");
				setTimeout(function() {
					location.reload(true);
				}, 1000);
			} else {
				modalPopup("清除缓存失败！", false);
			}
		}
	});
}