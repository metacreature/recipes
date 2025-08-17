/*

 File: recipe_list.js
 Copyright (c) 2025 Clemens K. (https://github.com/metacreature)
 
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

$(function() {

    var updateListData = function(data) {
        for (i in recipe_list) {
            if (recipe_list[i].recipe_id == data.recipe_id) {
                recipe_list[i] = data;
                break;
            }
        }
    }

    // recipe-detail (slideshow)

    var deleteRecipe = function(data, node, html_deleted) {
        $.confirm({
            title: LANG_RECIPE_LIST_DELETE_CONFIRM_TITLE,
            content: LANG_RECIPE_LIST_DELETE_CONFIRM_TEXT.replace('{recipe_name}', '<br><b>"' + html_special_chars(data.recipe_name) + '"</b><br>'),
            escapeKey: 'cancel',
            type: 'red',
            backgroundDismiss: true,
            onOpenBefore: function () {
                $('body').addClass('blockkeyactions');
            },
            onClose: function () {
                $('body').removeClass('blockkeyactions');
            },
            buttons: {
                confirm: {
                    text: LANG_RECIPE_LIST_DELETE_CONFIRM_CONFIRM,
                    action: function () {
                        $.ajax({
                            type: "POST",
                            dataType: "json",
                            url: '/recipes/list/delete',
                            data: {'recipe_id': data.recipe_id},
                            success: function (xhr) {
                                if (xhr.success) {
                                    node.html(html_deleted);
                                    data.deleted = true;
                                    updateListData(data);
                                }
                            }
                        });
                    }
                },
                cancel: {
                    text: LANG_RECIPE_LIST_DELETE_CONFIRM_CANCEL,
                    action: function () {}
                }
            }
        });
    }

    var toggleFavoriteSending = false;
    function toggleFavorite(data, node) {
        if (toggleFavoriteSending) {
            return;
        }
        toggleFavoriteSending = true;
        var url = data.is_favorite ? '/recipes/list/remove_favorite' : '/recipes/list/add_favorite';
        $.ajax({
            type: "POST",
            dataType: "json",
            url: url,
            data: {'recipe_id': data.recipe_id},
            success: function (xhr) {
                if (xhr.success) {
                    data.is_favorite = data.is_favorite ? 0 : 1;
                    updateListData(data);
                    node.find('.recipe_name img')[0].src = '/static/images/icons/heart-' + (data.is_favorite ? 'sharp.svg' : 'outline.svg');
                }
                toggleFavoriteSending = false;
            },
            complete: function() {
                toggleFavoriteSending = false;
            }
        });


    }

    function formatQuantity(quantity) {
        if (quantity == 0) {
            return '';
        }
        if (quantity > 10) 
            return Math.round(quantity);
        
        quantity = Math.round(quantity * 1000) / 1000;
        var number = Math.floor(quantity);
        if (quantity == number) {
            return number;
        }
        var number_string = number + '&nbsp;'
        if (number == 0) {
            number_string = ''
        } else {
            quantity = (quantity - number);
        }
        if (number <= 5 && quantity >= 0.88)
            return number_string + '<i>7/8</i>';
        if (number <= 5 && quantity >= 0.7)
            return number_string + '<i>3/4</i>';
        if (number <= 5 && quantity >= 0.6)
            return number_string + '<i>2/3</i>';
        if (quantity >= 0.45)
            return number_string + '<i>1/2</i>';
        if (number <= 5 && quantity >= 0.3)
            return number_string + '<i>1/3</i>';
        if (number <= 5 && quantity >= 0.2)
            return number_string + '<i>1/4</i>';
        if (number) {
            return number;
        }
        return '<i>1/8</i>';
    }

    var clearIngredientsInputs = function () {
        $('.wrapper_ingredients input, .wrapper_ingredients .btn').remove();
        $('.wrapper_ingredients span').show();
    }

    var createIngredients = function(factor, data, node) {
        
        var has_alternative = false;
        var colspan = 2;
        for (row of data.ingredients_list) {
            if (row.is_alternative) {
                has_alternative = true;
                colspan = 3;
                break;
            }
        }
        
        var html = '<table>';
        html +=       '<tr data-orig="'+data.persons+'" class="persons">';
        html +=         '<td colspan="' + colspan + '">';
        html +=             '<img src="/static/images/icons/people-sharp.svg" alt="">';
        html +=             '<span>' + formatQuantity(data.persons * factor) + '</span>';
        html +=             '<select>';
        var cnt = 0;
        var max_persons = data.persons > 12 ? data.persons : 12;
        while(cnt < max_persons) {
            cnt++;
            html +=             '<option value="' + cnt + '">' + cnt + '</option>';
        }
        html +=             '</select>';
        html +=         '</td>';
        html +=         '<td>';
        html +=             '<img src="/static/images/icons/refresh-outline.svg" alt="" class="reset" data-value="'+data.persons+'">';
        html +=         '</td>';
        html +=       '</tr>';

        for (var i in data.ingredients_list) {
            var row = data.ingredients_list[i];
            var quantity = formatQuantity(row.quantity * factor);
            html +=     '<tr data-orig="'+row.quantity+'" class="ingredients">';
            if (has_alternative) {
                html +=     '<td>' + (row.is_alternative ? LANG_RECIPE_LIST_DETAIL_INGREDINTS_ALTERNATIVE + ' ' : '') + '</td>';
            }
            html +=         '<td class="quantity"><span>' + quantity + '</span></td>';
            html +=         '<td>' + (row.unit_name ? html_special_chars(row.unit_name) : '' ) + '</td>';
            html +=         '<td>' + html_special_chars(row.ingredients_name) + '</td>';
            html +=     '</tr>';
        }
        html += '</table>';

        node.find('.wrapper_ingredients').html(html);

        var changeIngredients = function(e) {
            e.preventDefault(); 
            e.stopPropagation();
            var ele = $(e.currentTarget);
            var tr = ele.parents( "tr" );
            var value = ele.data('value') ? ele.data('value') + '': tr.find('input, select').val();
            if (/^[0-9]+([.,][0-9]+){0,1}$/.test(value)) {
                value = value.replace(',', '.');
                createIngredients(value / tr.data('orig'), data, node);
            } else {
                clearIngredientsInputs();
            }
        }

        node.find('.wrapper_ingredients tr.ingredients').on('mousedown', function(e) {
            if (!$(this).data('orig')) {
                return;
            }
            e.preventDefault();
            e.stopPropagation();
            var td = $(this).find('td.quantity');
            if (td.find('input').length == 0) {
                clearIngredientsInputs();
                td.append('<input type="number" value=""><a class="btn"><img src="/static/images/icons/checkmark-sharp.svg" alt=""></a>');
                td.find('span').hide();
                td.find('.btn').on('click', changeIngredients);
                td.find('input').on('change', changeIngredients).on('keydown', function(e) {
                    if (e.key === 'Enter' || e.keyCode === 13) {
                        changeIngredients(e);
                    }
                });
            }
            td.find('input').focus();
        });

        node.find('.wrapper_ingredients tr.persons select').val('').on('change', changeIngredients);
        node.find('.wrapper_ingredients tr.persons .reset').on('mousedown', changeIngredients);
    }
    
    var createShoppingList = function(data, node) {
        var has_alternative = false;
        for (row of data.shopping_list) {
            if (row.is_alternative) {
                has_alternative = true;
                break;
            }
        }

        var html = '';
        html += '<div class="recipe_detail">';
        html += '<div class="recipe_name">' + LANG_RECIPE_LIST_SHOPPINGLIST + '</div>';
        html += '<div class="wrapper_ingredients">';
        html +=     '<table>';
        for (var i in data.shopping_list) {
            var row = data.shopping_list[i];
            var quantity = formatQuantity(row.quantity);
            html +=     '<tr class="ingredients">';
            if (has_alternative) {
                html +=     '<td>' + (row.is_alternative ? LANG_RECIPE_LIST_DETAIL_INGREDINTS_ALTERNATIVE + ' ' : '') + '</td>';
            }
            html +=         '<td class="quantity"><span>' + quantity + '</span></td>';
            html +=         '<td>' + (row.unit_name ? html_special_chars(row.unit_name) : '' ) + '</td>';
            html +=         '<td>' + html_special_chars(row.ingredients_name) + '</td>';
            html +=     '</tr>';
        }
        html +=     '</table>';
        html += '</div>';
        html += '</div>';
        node.html(html);
    }

    var createSlide = function(data, node) {
        
        data = data.data;
        if (typeof data['shopping_list'] != 'undefined') {
            createShoppingList(data, node);
            return;
        }

        var html_deleted = '';
        html_deleted += '<div class="recipe_deleted">';
        html_deleted +=     '<img src="/static/images/icons/trash-outline.svg" alt="">';
        html_deleted +=     '<span>' + html_special_chars(data.recipe_name) + '</span>';
        html_deleted += '</div>';

        if (data.deleted) {
            node.html(html_deleted);
            return;
        }

        var html = '';
        html += '<div class="recipe_detail">';
        
        html += '<div class="edit_buttons">';
        if(data.editable) {
            html += '<a class="btn edit" href="/recipes/editor?recipe_id=' + data.recipe_id + '"><img src="/static/images/icons/build-outline.svg" alt=""></a>';
            html += '<a class="btn delete" href=""><img src="/static/images/icons/trash-outline.svg" alt=""></a>';
            html += '<div class="clear"></div>';
        }
        html +=     '<a class="btn print" href="javascript: window.print();"><img src="/static/images/icons/print-outline.svg" alt=""></a>';
        html += '</div>';
        

        html += '<div class="recipe_name">';
        if (IS_LOGIN) {
            if(data.is_favorite) {
                html += '<img src="/static/images/icons/heart-sharp.svg" class="favimg" alt="">';
            } else {
                html += '<img src="/static/images/icons/heart-outline.svg" class="favimg" alt="">';
            }
        }
        html += html_special_chars(data.recipe_name);
        if (parseInt(data.public) == 0) {
            html += '<img src="/static/images/icons/lock-closed.svg" class="private" alt="">';
        }
        html += '</div>';

        html += '<div class="tags">';
        html += data.category_name;
        if (data.tag_list.length) {
            html += ', ' + html_special_chars(data.tag_list.join(', '));
        }
        html += '</div>';

        
        html += '<div class="user_name">@ ' + html_special_chars(data.user_name) + '</div>';

        if (data.costs || data.duration || data.total_duration) {
            html += '<div class="numbers">';
            if (data.costs) {
                html += '<div class="costs">' + LANG_RECIPE_LIST_DETAIL_COSTS + ': '+(Math.round(data.costs/ data.persons * 100) / 100) + SETTINGS_CURRENCY + '</div>';
            }
            if (data.duration || data.total_duration) {
                html += '<div class="duration">' + LANG_RECIPE_LIST_DETAIL_DURATION + ': ';
                html += data.duration ? data.duration : '-';
                html += '/';
                html += data.total_duration ? data.total_duration : '-';
                html += ' ' + LANG_RECIPE_LIST_DETAIL_DURATION_UNIT + '</div>';
            }
            html += '<div class="clear"></div>';
            html += '</div>';
        }

        
        html += '<div class="clear"></div>';

        html += '<div class="wrapper">';
        if (data.image_name) {
            html += '<img src="/gallery/recipes/' + data.image_name + '.webp" alt="">';
        }
        html +=     '<div class="wrapper_ingredients">';
        html +=      '</div>';
        html +=      '<div class="clear"></div>';
        html += '</div>';

        html += '<table class="step_list">';
        var cnt = 0;
        for(step of data.step_list) {
            cnt++;
            html += '<tr><td>#' + cnt + '</td><td>' + nl2br(html_special_chars(step)) + '</td></tr>';
        }
        html += '</table>';

        html += '</div>';
        node.html(html);
        
        createIngredients(1, data, node);

        node.find('.edit_buttons a.delete').on('click', function(e) {
            e.preventDefault(); 
            deleteRecipe(data, node, html_deleted);
        });

        if (IS_LOGIN) {
            node.find('.recipe_name').on('click', function(e) {
                e.preventDefault(); 
                toggleFavorite(data, node);
            });
        }

        node.on('mousedown', clearIngredientsInputs);
    }

    var createSlideshow = function() {
        slideshow('#recipe_list a.slide_entry', '/recipes/list/get', 'recipe_id', createSlide, null, onCloseSlideshow);
    }

    var onCloseSlideshow = function() {
        buildRecipeList(recipe_list);
    }


    // recipe-list

    function _buildRecipeList(recipe_list, is_favorite) {
        var has_favorite;
        for(row of recipe_list) {
            if (row.deleted || (is_favorite && !row.is_favorite) || (!is_favorite && row.is_favorite)) {
                continue;
            }
            has_favorite = has_favorite || row.is_favorite;
            let html = '<a class="entry recipe_entry slide_entry" href="'+row.recipe_id+'">';
                html += '<div class="recipe_name">';
                if(row.is_favorite) {
                    html += '<img src="/static/images/icons/heart-sharp.svg" class="favimg" alt="">';
                }
                html += html_special_chars(row.recipe_name)+'</div>';
                html += '<div class="category_name">'+row.category_name;
                if (row.costs_pp) {
                    html += '<span>'+(Math.round(row.costs_pp * 100) / 100) + SETTINGS_CURRENCY +'</span>';
                }
                html += '</div>';
                if (row.image_name) {
                    html += '<img src="/gallery/recipes/'+row.image_name+'_s.webp" class="recipe_img" alt="">';
                } else {
                    html += '<img src="/static/images/recipe_blank_s.png" class="recipe_img" alt="">';
                }
                html += '<div class="user_name">'+html_special_chars(row.user_name)+'</div>';
                if (!row.public) {
                    html += '<img src="/static/images/icons/lock-closed.svg" class="private" alt="">';
                }
                html += '</a>';
            if (is_favorite && row.is_favorite) {
                $(html).insertBefore('#recipe_list .add_recipe');
            } else {
                $('#recipe_list').append(html);
            }
        }
        if (is_favorite && has_favorite) {
            $('<div class="favseperator"></div>').insertBefore('#recipe_list .add_recipe');

            let html = '<a class="entry shopping_list slide_entry" href="0">';
            html +=        '<img src="/static/images/icons/bag-outline.svg" alt="">';
            html +=        '<span>' + LANG_RECIPE_LIST_SHOPPINGLIST + '</span>';
            html +=     '</a>';
            $('#recipe_list').prepend(html);
        }
    }

    function buildRecipeList(recipe_list) {
        $('#recipe_list .entry:not(.add_recipe), #recipe_list .favseperator').remove();
        _buildRecipeList(recipe_list, true);
        _buildRecipeList(recipe_list, false);
        createSlideshow();
    }
    buildRecipeList(recipe_list);

    // form

    $('.filter_form').ajax_form({
        'default_error': LANG_FORM_DEFAULT_ERROR,
        'success': function(form, button, data) {
            if (data.success) {
                if ($('.filter_form input[name="recipe_name"]').val() == ''){
                    $('.filter_form input[name="recipe_name"]').prop('disabled', true);
                }
                if ($('.filter_form input#sort').val() == ''){
                    $('.filter_form input#sort').prop('disabled', true);
                }
                var query_string = form.serialize();
                $('.filter_form input[name="recipe_name"]').prop('disabled', false);
                $('.filter_form input#sort').prop('disabled', false);

                var href = self.location.href.replace(/[#].*$/, '').replace(/\?.*$/, '');
                history.replaceState({}, '', href + (query_string ? '?' + query_string : ''));
                recipe_list = data.data;
                buildRecipeList(data.data);
            }
        }
    });


    $('.filter_form select, .filter_form input[name="recipe_name"]').on('change', function(){
        $('.filter_form').submit();
    });

    $('#recipe_list_filter #sort_popup a').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $('.filter_form #sort').val($(this).attr('href'));
        sort_close();
        $('.filter_form').submit();
    });

    var sort_open = function () {
        $('#recipe_list_filter #sort_popup a').removeClass('active');
        $('#recipe_list_filter #sort_popup a[href="' + $('.filter_form input#sort').val() + '"]').addClass('active')
        $('#recipe_list_filter #sort_popup').show();
    }
    var sort_close = function () {
        $('#recipe_list_filter #sort_popup').hide();
    }
    $('#recipe_list_filter .btn.sort').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        if ($('#recipe_list_filter #sort_popup').is(':hidden')) {
            sort_open();
        } else {
            sort_close();
        }
    });
    document.addEventListener('click', function(e) {
        sort_close();
    });
});