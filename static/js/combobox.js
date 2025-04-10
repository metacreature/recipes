$.fn.combobox = function(options, data) {
	
    return this.each(function() {

        var settings = $.extend(true, {}, $.fn.combobox.defaults, options);
        var node = $(this);
        $.fn.combobox.instance += 1;
        
        
    	var list_node = $('<div class="combobox_dd" id="combobox_dd_'+$.fn.combobox.instance+'"></div>');
    	node.wrap('<div class="combobox_wrapper" id="combobox_wrapper_'+$.fn.combobox.instance+'"></div>');
    	node.after(list_node);
        
        
        node.data('combobox_instance', $.fn.combobox.instance);
        node.addClass('combobox');
        var button = $('<span class="glyphicons glyphicons-menu-hamburger combobox_button"></span>');
        node.after(button);
        
        var is_focused = false;
        node.focus_timer = null;
        node.focus(function(e) {
        	$.fn.combobox.load_list(node, list_node, settings, true);
        	focus_timer = setTimeout(function() {is_focused = true;}, 40);
        });
        node.blur(function(e) {
        	if (focus_timer) {
        		window.clearTimeout(node.focus_timer);
        		node.focus_timer = null;
        	}
        	is_focused = false;
        	list_node.hide();
        });
        node.mousedown(function() {
        	if (!is_focused) {
        		return;
        	}
        	if (list_node.is(':visible')) {
        		list_node.hide();
        	} else {
        		$.fn.combobox.load_list(node, list_node, settings, true);
        	}
        });
        node.keyup(function() {
        	$.fn.combobox.load_list(node, list_node, settings, true);
        	is_focused = true;
        })
    });
}

$.fn.combobox.load_list = function (node, list_node, settings, show_dd) {
	if (list_node.data('loaded')) {
		$.fn.combobox.hideshow_options(node, list_node, show_dd);
	} else {
		list_node.data('loaded', true);
		if (settings.list) {
			var data_list = settings.list;
			var i, ele;
			for (i in data_list) {
				ele = $('<div>'+html_special_chars(data_list[i])+'</div>').data('val', data_list[i]);
				list_node.append(ele);
				if (typeof data_list[i]== 'string') {
					data_list[i] = data_list[i].toLowerCase();
				}
			}
			list_node.data('data_list', data_list);
			list_node.find('div').mousedown(function(e) {
				if (node.focus_timer) {
					window.clearTimeout(node.focus_timer);
					node.focus_timer = null;
				}
				node.val($(this).data('val'))
				is_focused = false;
				list_node.hide();
			});
			
			$.fn.combobox.hideshow_options(node, list_node, show_dd);
			return;
		}
		$.ajax({
			type: "POST",
			dataType: "json",
			url: settings.url,
			data: {table: settings.table, col: settings.col, id: settings.id, field: node.attr('name')},
			complete: function (xhr) {
				if (xhr.responseJSON && typeof xhr.responseJSON.data != 'undefined') {
					var data_list = xhr.responseJSON.data;
					var i, ele;
			    	for (i in data_list) {
			    		ele = $('<div>'+html_special_chars(data_list[i].entry)+'</div>').data('val', data_list[i].entry);
			    		list_node.append(ele);
						if (typeof data_list[i].entry == 'string') {
							data_list[i] = data_list[i].entry.toLowerCase();
						} else {
							data_list[i] = data_list[i].entry;
						}
			    	}
					list_node.data('data_list', data_list);
					list_node.find('div').mousedown(function(e) {
			        	if (node.focus_timer) {
			        		window.clearTimeout(node.focus_timer);
			        		node.focus_timer = null;
			        	}
			        	node.val($(this).data('val'))
			        	is_focused = false;
			        	list_node.hide();
			        });
					
					$.fn.combobox.hideshow_options(node, list_node, show_dd);
				} else {
					list_node.data('loaded', false);
				}
			},
		});
	}
}

$.fn.combobox.hideshow_options = function (node, list_node, show_dd) {
	
	var data_list = list_node.data('data_list');
	var val_lower = node.val().toLowerCase();
	
	if (is_empty(node.val()) || data_list.indexOf(val_lower) === -1) {
    	list_node.find('div').show().removeClass('active');
    	if (show_dd) {
    		list_node.show();
    	}
		list_node.scrollTop(0);
		return;
	}
	if (data_list.indexOf(val_lower) > -1) {
    	list_node.find('div').show().removeClass('active').each(function() {
			if ($(this).data('val').toLowerCase() == val_lower) {
				$(this).addClass('active');
				if (show_dd) {
            		list_node.show();
            	}
				list_node.scrollTop($(this).position().top);
				return false;
			}
		});
    	
	} else {
		list_node.find('div').removeClass('active').each(function() {
			if ($(this).data('val').toLowerCase().indexOf(val_lower) > -1) {
				$(this).show();
			} else {
				$(this).hide();
			}
		});
		if (show_dd) {
    		list_node.show();
    	}
		list_node.scrollTop(0);
	}
}

$.fn.combobox.instance = 0;
$.fn.combobox.defaults = {
	url: null,
	table: null, 
	col: null, 
	id: null,
	list: null,
}