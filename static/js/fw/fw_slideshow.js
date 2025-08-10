/*

 File: fw_slideshow.js
 Copyright (c) 2014 Clemens K. (https://github.com/metacreature)
 
 MIT License
 
 Permission is hereby granted, free of charge, to any person obtaining a copy
 of this software and associated documentation files (the "Software"), to deal
 in the Software without restriction, including without limitation the rights
 to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 copies of the Software, and to permit persons to whom the Software is
 furnished to do so, subject to the following conditions:
 
 The above copyright notice and this permission notice shall be included in all
 copies or substantial portions of the Software.
 
 THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 SOFTWARE.

*/

var slideshow_init = {};
var slideshow = function(jquery_selector, slideshow_ajax_url, id_name, renderSlide, onChangeStateForward) {
    if (slideshow_init[jquery_selector]) {
        return;
    }
    slideshow_init[jquery_selector] = true;
    
    if (!$('#focushelper').length) {
        $('body').prepend('<a href="javascript:;" id="focushelper" title="focushelper"></a>');
    }

    var active_ajax = null;
    var slideshow_navigation = {};
    var slideshow_cache = {};
    var has_touchevents = "ontouchstart" in document.documentElement;
    var focushelper_node  = $('#focushelper');

    var stopAjax = function() {
        try{
            if (active_ajax) active_ajax.abort();
        } catch(e) {}
        active_ajax = null;
    }

    var removeSlides = function() {
        var actual = $('#slideshow_header').data('actual');
        $('#slideshow .inner .slide').stop(true, true).each(function() {
            if ($(this).attr('id') != 'slide_'+actual) {
                $(this).remove();
            }
        });
    }

    var setActive = function(id){
        $(jquery_selector).removeClass('active').each(function(){
            if ($(this).attr('href') == id){
                $(this).addClass('active');
            }
        });
    }

    var loadSlideshowSlide = function (id, next, historyadd) 
    {
        stopAjax();
        removeSlides();

        var slideshow_node = $('#slideshow .inner');
        var slideshow_header_node = $('#slideshow_header');
        
        slideshow_header_node.data('actual', id);
        
        if (slideshow_navigation[id].next) {
            slideshow_header_node.find('.next').removeClass('disabled');
        } else {
            slideshow_header_node.find('.next').addClass('disabled');
        }
        if (slideshow_navigation[id].prev) {
            slideshow_header_node.find('.prev').removeClass('disabled');
        } else {
            slideshow_header_node.find('.prev').addClass('disabled');
        }
        
        var old_node = slideshow_node.find('.slide');
        var node = $('<div class="slide loading" id="slide_'+id+'"><div class="loading_inner"><div class="icon_loading dark"></div></div></div>');
        if (next) {
            slideshow_node.append(node.css('margin-left', 0));
            old_node.animate({'margin-left': '-50%'}, {
                'duration': 300, 
                'easing': 'linear',
                'complete': removeSlides
            });
        } else {
            node.css('margin-left', '-50%');
            slideshow_node.prepend(node);
            node.animate({'margin-left': 0}, {
                'duration': 300, 
                'easing': 'linear',
                'complete': removeSlides
            });
        }

        var history_url = document.location.href.replace(new RegExp('[#]' + id_name + '=[0-9]+$','g'),'');
        if (historyadd) {
            history.pushState({'slideshow': 'open', 'historyadd': $('#slideshow').attr('historyadd')}, '', history_url + '#' + id_name + '=' + id);
        } else {
            history.replaceState({'slideshow': 'open', 'historyadd': $('#slideshow').attr('historyadd')}, '', history_url + '#' + id_name + '=' + id);
        }

        if (slideshow_cache[id]) {
            renderSlide(slideshow_cache[id], node);
            setActive(id);
            node.removeClass('loading');
        } else {
            var postdata = {};
            postdata[id_name] = id;
            active_ajax = $.ajax({
                type: "POST",
                url: slideshow_ajax_url,
                data: postdata,

            }).done(function (data) {
                if (data && data.data) {
                    slideshow_cache[id] = data;
                    renderSlide(slideshow_cache[id], node);
                    setActive(id);
                    node.removeClass('loading');
                }
            });
        }
        
        return node;
    }

    var openSlideshow = function(id, noanimate, historyadd, statehistoryadd) 
    {
        if (!$('#slideshow').length) {

            var node = $('<div id="slideshow" data-opened="1" style="top: 0px; position: fixed;">'+
                        '<div id="slideshow_header">'+
                            '<a class="prev" href="#"><span class="btn"></span>'+(!has_touchevents ? '<span class="area"></span>' : '')+'</a>'+
                            '<a class="next" href="#"><span class="btn"></span>'+(!has_touchevents ? '<span class="area"></span>' : '')+'</a>'+
                            '<a class="close" href="#"><span class="btn"></span></a>'+
                        '</div>'+
                        '<div class="body"><div class="inner"></div><div class="clear"></div></div></div>');
            $('body').append(node);

            if (historyadd || statehistoryadd) {
                $('#slideshow').attr('historyadd', 'yes');
            }

            $('#slideshow_header .close').on('click', onClickSlideshowClose);
            $('#slideshow_header .prev').on('click', onClickSlideshowPrev);
            $('#slideshow_header .next').on('click', onClickSlideshowNext);
            $('#slideshow_header a').on('mouseenter', onHoverSlideshowBtn);
            
            if (has_touchevents) {
                node.find('.body').on('touchstart', function(e) {
                    touchstart_x = e.touches[0].clientX;
                });
                node.find('.body').on('touchmove', function(e) {
                    if (touchstart_x !== null && e.touches.length == 1) {
                        var diff = $(this).width() / 3;
                        diff = diff > 150 ? 150 : diff;
                        if (touchstart_x + diff < e.touches[0].clientX) {
                            touchstart_x = null;
                            onClickSlideshowPrev();
                        } else if (touchstart_x - diff > e.touches[0].clientX) {
                            touchstart_x = null;
                            onClickSlideshowNext();
                        }
                    }
                });
                node.find('.body').on('touchend', function(e) {
                    touchstart_x = null;
                });
            }
            
            var finish_anim = function() {
                $('body').addClass('slideshow_opened');
                node.css('top', 0).css('opacity', 1);
                if (node.find('> div > a:focus').length == 0) {
                    focushelper_node.focus();
                }
            }
            
            //if (noanimate) {
                finish_anim();
            /*} else {
                node.animate({'opacity': 1}, {
                    'duration': 300, 
                    'easing': 'linear',
                    'complete': finish_anim,
                });
            }*/
        }
        
        $('#slideshow').find('.inner').html('');
        var prev = null;
        slideshow_navigation = {};
        $(jquery_selector).each(function() {
            var href = $(this).attr('href');
            if (prev) 
                slideshow_navigation[prev].next = href;
            slideshow_navigation[href] = {prev: prev, next: null};
            prev = href;
        });
        loadSlideshowSlide(id, true, historyadd);
    }

    var closeSlideshow = function (empty_slideshow_cache, perform_back) 
    {
        $('body').removeClass('slideshow_opened');
        if (empty_slideshow_cache) {
            slideshow_cache = {};
        }
        if ($('#slideshow').data('opened')) {
            $('#slideshow').data('opened', false);
            $('#slideshow').stop(true, true);
            
            $('#slideshow').animate({'opacity': 0}, {
                'duration': 300, 
                'easing': 'linear',
                'complete': function() {
                    $('#slideshow').remove();
                }
            });
        }
        if ($('#slideshow').attr('historyadd') == 'yes') {
            if (perform_back) {
                history.back();
            }
        } else {
            history.replaceState({}, '', document.location.href.replace(new RegExp('[#]' + id_name + '=[0-9]+$', 'g'),''));
        }
    }

    var onClickSlideshowPrev = function(e) 
    {
        if (e) e.preventDefault();
        var navi = slideshow_navigation[$('#slideshow_header').data('actual')].prev;
        if (!$('#slideshow').data('opened') || !navi)
            return;
        loadSlideshowSlide(navi, false);
    }

    var onClickSlideshowNext = function(e) 
    {
        if (e) e.preventDefault();
        var navi = slideshow_navigation[$('#slideshow_header').data('actual')].next;
        if (!$('#slideshow').data('opened') || !navi)
            return;
        loadSlideshowSlide(navi, true);
    }

    var onClickThumb = function(e) 
    {
        e.preventDefault();
        var id = $(this).attr('href');
        if ($('#slideshow').length) 
            return;
        openSlideshow(id, false, true, false);
    }

    var onClickSlideshowClose = function(e) 
    {
        e.preventDefault();
        if (!$('#slideshow').data('opened')) 
            return;
        stopAjax();
        closeSlideshow(false, true);
    }

    var onPopState = function(e) {
        e.preventDefault();
        if (e.state && e.state.slideshow == 'open') {
            if (onChangeStateForward) {
                onChangeStateForward(e);
            }
            onLoadSlideshow();
        } else {
            if (!$('#slideshow').data('opened')) 
                return;
            stopAjax();
            closeSlideshow(false, false);
        }
    }

    var onHoverSlideshowBtn = function(e) {
        $(this).parent().find('a').blur();
    }


    $(document).on('keydown', function(e) {
        if ($('body').hasClass('blockkeyactions')) {
            return;
        }
        if (e.originalEvent.repeat || !$('#slideshow').data('opened')) {
        } else if(e.which === 27) {
            onClickSlideshowClose(e);
        } else if(e.which === 37) {
            onClickSlideshowPrev(e);
        } else if(e.which === 39) {
            onClickSlideshowNext(e);
        }
    });

    var onLoadSlideshow = function()
    {
        if (!id_name) {
            return;
        }
        if (new RegExp('#' + id_name + '=[0-9]+$').test(document.location.href)) {
            var id = document.location.href.split('#' + id_name + '=')[1];
            var node = $(jquery_selector + '[href="'+id+'"]');
            if (node.length == 1) {
                let statehistoryadd = false;
                if (history.state && history.state.historyadd == 'yes') {
                    statehistoryadd = true;
                }
                openSlideshow(id, true, false,  statehistoryadd);
                return true;
            }
        }
        return false;
    }
    
    $(jquery_selector).click(onClickThumb);
    window.addEventListener('popstate', onPopState);
	$(document).on('click', jquery_selector, onClickThumb);
    onLoadSlideshow();
}
