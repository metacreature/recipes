

/****************************************************/
/**************** infinite scroll *******************/

var toAnchor = function(id) {
	var top = $(id).offset().top;
	window.scrollTo(0,top);
}

var initInfiniteScroll = function(sFormSelector, sEndpointUrl, renderFunc, data_list, maxPP) {

	$("#infinite_scroll_loading").hide();
	
	var win = $(window);
	var doc = $(document);
	var form = $(sFormSelector);
	var res = renderFunc(data_list, maxPP, 0);
	var load_next = res[0];
	var iPage = res[1];
	var loading = false;

	if ($("#infinite_scroll_pagina").length) {
		$("#infinite_scroll_pagina").html("").hide();
		$("#infinite_scroll_pagina").append('<li><a href="javascript:toAnchor(\'#TOP\')"><span class="glyphicon glyphicon-chevron-up"></span></a></li>');
		$("#infinite_scroll_pagina").append('<li><a href="javascript:toAnchor(\'#BOTTOM\')"><span class="glyphicon glyphicon-chevron-right"></span></a></li>');
		$(".infinite_scroll_anchor").each(function() {
			var node = $('<li><a href="javascript:toAnchor(\'#infinite_scroll_anchor_'+$(this).data('page')+'\')">'+($(this).data('page')+1)+'</a></li>')
			node.insertBefore('#infinite_scroll_pagina li:last');
		});
		if (iPage > 1) {
			$("#infinite_scroll_pagina").show();
		}
	}

	function scrollInfinite() {
		if (!load_next || loading) {
			return;
		}
		if (doc.height() - win.height() <= win.scrollTop() + 30) {
			loading = true;
			$("#infinite_scroll_loading").show();
			var form_data = form.serializeArray();
			form_data.push({name: 'page', value: iPage});
			
			var url = document.location.href;
			url = url.replace(/page=[0-9]+/,'page='+iPage);
			if (url.indexOf('page='+iPage) < 0) {
				url += (url.indexOf('?') > 0 ? '&' : '?')+'page='+iPage;
			}
			history.replaceState({}, document.title, url);
			
			$.ajax({
				type: "POST",
				url: sEndpointUrl,
				data: form_data,
				dataType: "json",
				complete: function(xhr) {
					$('#infinite_scroll_loading').hide();
					if (!xhr.responseJSON || !xhr.responseJSON.data_list || !xhr.responseJSON.data_list.length) {
						load_next = false;
						loading = false;
						return;
					}
					$('<li><a href="javascript:toAnchor(\'#infinite_scroll_anchor_'+iPage+'\')">'+(iPage+1)+'</a></li>').insertBefore('#infinite_scroll_pagina li:last');
					$("#infinite_scroll_pagina").show();
					
					var res = renderFunc(xhr.responseJSON.data_list, maxPP, iPage);
					load_next = res[0];
					iPage = res[1];
					loading = false;
				}
			});
		}
	}
	
	$(function(){
		win.scroll(scrollInfinite);
	});
}