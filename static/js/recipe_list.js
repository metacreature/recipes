$(function() {

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
                                    $('#recipe_list a.recipe_entry[href="' + data.recipe_id + '"]').remove();
                                    data.deleted = true;
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

    var createIngredients = function(persons, data) {
        var factor = persons / data.persons;

        var has_alternative = false;
        for (row of data.ingredients_list) {
            if (row.is_alternative) {
                has_alternative = true;
                break;
            }
        }

        var html = '<table>';
        for (row of data.ingredients_list) {
            var quantity = formatQuantity(row.quantity * factor);
            html +=     '<tr>';
            if (has_alternative) {
                html +=     '<td>' + (row.is_alternative ? LANG_RECIPE_LIST_DETAIL_INGREDINTS_ALTERNATIVE + ' ' : '') + '</td>';
            }
            html +=         '<td class="quantity">' + quantity + '</td>';
            html +=         '<td>' + (row.unit_name ? html_special_chars(row.unit_name) : '' ) + '</td>';
            html +=         '<td>' + html_special_chars(row.ingredients_name) + '</td>';
            html +=     '</tr>';
        }
        html += '</table>';
        return html;
    }

    var createSlide = function(data, node) {
            
        data = data.data;

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

        if(data.editable) {
            html += '<div class="edit_buttons">';
            html += '<a class="btn edit" href="/recipes/editor?recipe_id=' + data.recipe_id + '"><img src="/static/images/icons/build-outline.svg" alt=""></a>';
            html += '<a class="btn delete" href=""><img src="/static/images/icons/trash-outline.svg" alt=""></a>';
            html += '</div>';
        }

        html += '<div class="recipe_name">';
        if (!row.public) {
            html += '<img src="/static/images/icons/lock-closed.svg" class="private" alt="">';
        }
        html += html_special_chars(data.recipe_name);
        html += '</div>';

        html += '<div class="tags">';
        html += data.category_name;
        if (data.tag_list.length) {
            html += ', ' + html_special_chars(data.tag_list.join(', '));
        }
        html += '</div>';

        html += '<div class="user_name">@ ' + html_special_chars(data.user_name) + '</div>';

        html += '<div class="wrapper">';
        if (data.image_name) {
            html += '<img src="/gallery/recipes/' + data.image_name + '.webp" alt="">';
        }
        html +=     '<div class="wrapper_ingredients">';
        html +=         '<div class="persons">';
        html +=             '<img src="/static/images/icons/people-sharp.svg" alt="">';
        html +=             '<span>' + data.persons + '</span>';
        html +=             '<select>';
        var cnt = 0;
        var max_persons = data.persons > 12 ? data.persons : 12;
        while(cnt < max_persons) {
            cnt++;
            html +=             '<option' + (data.persons == cnt ? ' selected' : '') + '>' + cnt + '</option>';
        }
        html +=             '</select>';
        html +=             '<div class="clear"></div>';
        html +=         '</div>';
        html +=         '<div class="ingredients_list">';
        html +=             createIngredients(data.persons, data);
        html +=         '</div>';
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

        node.find('select').on('click', function() { 
            node.find('.ingredients_list').html(createIngredients($(this).val(), data));
        });
        node.find('.edit_buttons a.delete').on('click', function(e) {
            e.preventDefault(); 
            deleteRecipe(data, node, html_deleted);
        });
    }

    var createSlideshow = function() {
        slideshow('#recipe_list a.recipe_entry', '/recipes/list/get', 'recipe_id', createSlide);
    }


    // recipe-list

    function buildRecipeList(recipe_list) {
        for(row of recipe_list) {
            var html = '<a class="entry recipe_entry" href="'+row.recipe_id+'">';
                html += '<div class="recipe_name">'+html_special_chars(row.recipe_name)+'</div>';
                html += '<div class="category_name">'+row.category_name+'</div>';
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
            $('#recipe_list').append(html);
        }
        createSlideshow();
    }
    buildRecipeList(recipe_list);

    // form

    $('.filter_form').ajax_form({
        'success': function(form, button, data) {
            if (data.success) {
                if ($('.filter_form input[name="recipe_name"]').val() == ''){
                    $('.filter_form input[name="recipe_name"]').prop('disabled', true);
                }
                $('.filter_form input[name="page"]').prop('disabled', true);
                var query_string = form.serialize();
                $('.filter_form input[name="recipe_name"]').prop('disabled', false);
                $('.filter_form input[name="page"]').prop('disabled', false);

                var href = self.location.href.replace(/[#].*$/, '').replace(/\?.*$/, '');
                history.replaceState({}, '', href + (query_string ? '?' + query_string : ''));

                if (!button.hasClass('pagination')) {
                    $('#recipe_list .entry:not(.add_recipe)').remove();
                }
                buildRecipeList(data.data);
            }
            button.removeClass('pagination');
        },
        'beforesubmit': function(form, button) {
            if (!button.hasClass('pagination')) {
                $('.filter_form input[name="page"]').val(0);
            }
        },
    });


    $('.filter_form select, .filter_form input[name="recipe_name"]').on('change', function(){
        $('.filter_form').submit();
    });
});