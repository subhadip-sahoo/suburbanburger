function Captcha(){
    var alpha = new Array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
    var i;
    for (i=0;i<6;i++){
        var a = alpha[Math.floor(Math.random() * alpha.length)];
        var b = alpha[Math.floor(Math.random() * alpha.length)];
        var c = alpha[Math.floor(Math.random() * alpha.length)];
        var d = alpha[Math.floor(Math.random() * alpha.length)];
        var e = alpha[Math.floor(Math.random() * alpha.length)];
        var f = alpha[Math.floor(Math.random() * alpha.length)];
        var g = alpha[Math.floor(Math.random() * alpha.length)];
    }
    var code = a + ' ' + b + ' ' + ' ' + c + ' ' + d + ' ' + e + ' '+ f + ' ' + g;
    document.getElementById("mainCaptcha").value = code;
    document.getElementById("DisplayCpatcha").innerHTML = code;
 }
 
 function ValidCaptcha(){
    var string1 = removeSpaces(document.getElementById('mainCaptcha').value);
    var string2 = removeSpaces(document.getElementById('txtInput').value);
    if (string1 == string2){
        return true;
    }
    else{
        alert('Security code does not match!');
        return false;
    }
 }
 function removeSpaces(string){
    return string.split(' ').join('');
 }
 
(function($){
    var alertMessage = function(type, message){
        var cls = (type == 'error') ? 'alert-danger' : (type == 'warning') ? 'alert-warning' : (type == 'info') ? 'alert-info' : 'alert-success';
        var alertHTML = '';
        alertHTML = '<div role="alert" class="alert '+cls+' alert-dismissible">';
        alertHTML += '<button aria-label="Close" data-dismiss="alert" class="close" type="button"><span aria-hidden="true">×</span></button>';
        alertHTML += message;
        alertHTML += '</div>';
        return alertHTML;
    }
    
    var checkTime = function(){
        $.post(template.ajaxurl, {action: 'get_order_timing'}, function(response){
            $('#order_timing').html(response);
        });
    }
    
    $(function(){
        $('.carousel').carousel(); 
        $('[data-toggle="tooltip"]').tooltip();
        $(".navbar .dropdown").hover(            
            function() {
                $(this).next('.dropdown-menu', this).stop( true, true ).fadeIn("fast");
                $(this).toggleClass('open');
                $('b', this).toggleClass("caret caret-up");
            },
            function() {
                $(this).next('.dropdown-menu', this).stop( true, true ).fadeOut("fast");
                $(this).toggleClass('open');
                $('b', this).toggleClass("caret caret-up");
        });
        
        $('#login_form').submit(function(event){
            event.preventDefault();
            var formdata = $(this).serialize();
            $.post(template.ajaxurl, formdata, function(response){
                response = $.parseJSON(response);
                $('#ajaxresponse').html(alertMessage(response.type, response.message));
                if(response.type == 'success'){
                    window.location.href = template.current_url;
                }
            });
        });
        
        $('#reg_form').submit(function(event){
            event.preventDefault();
            var formdata = $(this).serialize();
            $.post(template.ajaxurl, formdata, function(response){
                response = $.parseJSON(response);
                $('#ajaxresponsereg').html(alertMessage(response.type, response.message));
                if(response.type == 'success'){
                    $('#reg_form')[0].reset();
                }
            });
        });
        
        $('#fpwd_form').submit(function(event){
            event.preventDefault();
            var formdata = $(this).serialize();
            $.post(template.ajaxurl, formdata, function(response){
                response = $.parseJSON(response);
                $('#ajaxresponsefpwd').html(alertMessage(response.type, response.message));
                console.log(response);
                if(response.type == 'info'){
                    $('#fpwd_form')[0].reset();
                    $('#fpwd-modal').modal('hide')
                    $('#rpwd-modal').modal();
                }
            });
        });
        
        $('#rpwd_form').submit(function(event){
            event.preventDefault();
            var formdata = $(this).serialize();
            $.post(template.ajaxurl, formdata, function(response){
                response = $.parseJSON(response);
                $('#ajaxresponsereset').html(alertMessage(response.type, response.message));
                if(response.type == 'success'){
                    $('#rpwd_form')[0].reset();
                }
            });
        });
        
        $('#prod-cat').ddscrollSpy({ 
            scrolltopoffset: -50,
            enableprogress: 'progress',
            scrollduration: 300
	});
        
        $(window).scroll(function () {
            if($( document ).width() >= 800){
                if ($(window).scrollTop() > 500) {
                    if($('body').hasClass('page-id-60')== true){
                        $('.cart-item-box').css({top:'0px', position: 'fixed'});
                        $('#item-menu').css({top:'0px', position: 'fixed'});	
                    }
                    else{
                        $('.cart-item-box').css({top:'120px', position: 'fixed'});
                        $('#item-menu').css({top:'120px', position: 'fixed'});
                    }
                }else{
                    $('.cart-item-box').removeAttr('style');
                    $('#item-menu').removeAttr('style');
                }
            }
        });
        
        $(document).delegate('.btn-add-to-cart', 'click', function(event){
            event.preventDefault();
            $(this).text('ADDED');
            var product_id = $(this).data('product');
            var quantity = $(this).data('quantity');
            var burger_note = null;
            if($(this).hasClass('linked') != true){
               if($('#burger_note_' + product_id).is(':checked')){
                    burger_note = $('#burger_note_' + product_id).val();
                } 
            }
            var combo_data = null;
            if($(this).data('product-type') == 'combo'){
                combo_data = $('#add-prod-' + product_id).serialize();
                console.log(combo_data);
            }
            $.post(template.ajaxurl, {action: 'add_to_cart', product_id: product_id, quantity: quantity, combo_data : combo_data, burger_note : burger_note}, function(response){
                console.log(response);
                response = $.parseJSON(response);
                $('.cart-products').html(response.html);
                $('.sub_total').text('$'+response.sub_total);
                $('.total').text('$'+response.total);
                $('.proceed-to-checkout').removeAttr('disabled');
                $('#my-cart').text('My Cart ('+ response.product_count +')');
                if(response.product_count != 0){
                    $('.cart-alert-msg').remove();
                }
                if(response.is_linked_product_in_cart == true){
                    $('.linked').removeAttr('disabled');
                }
            });
        });
        
        $(document).delegate('.update_cart', 'click', function(){
            var id = $(this).data('id');
            var quantity = $(this).closest('.cart-items').find('.qty').val();
            $.post(template.ajaxurl, {action: 'update_cart', product_id: id, quantity: quantity, request: 'update'}, function(response){
                console.log(response);
                response = $.parseJSON(response);
                $('.cart-products').html(response.html);
                $('.sub_total').text('$'+response.sub_total);
                $('.total').text('$'+response.total);
            });
        });
        
        $(document).delegate('.remove_product', 'click', function(){
            var id = $(this).data('id');
            if($(this).hasClass('linked_product_' + id)){
                var setAlert = false;
                $('.cart-items').each(function(){
                    if($(this).children('ul').hasClass('linked_product_attribute_' + id)){
                        setAlert = true;
                    }
                });
                if(setAlert == true){
                    alert('Please remove the products those come with this');
                    return false;
                }
            }
            $.post(template.ajaxurl, {action: 'update_cart', product_id: id, request: 'cancel'}, function(response){
                console.log(response);
                response = $.parseJSON(response);
                $('.cart-products').html(response.html);
                $('.sub_total').text('$'+response.sub_total);
                $('.total').text('$'+response.total);
                $('#my-cart').text('My Cart ('+ response.product_count +')');
                if(response.product_count == 0){
                    $('.proceed-to-checkout').attr('disabled', 'disabled');
                    $('.proceed-to-checkout').closest('.form-group').before('<p class="text-center text-danger item_add cart-alert-msg">Please add at least 1 item to your order!</p>');
                }else{
                    $('.proceed-to-checkout').removeAttr('disabled');
                }
                if(response.is_linked_product_in_cart == false){
                    $('.linked').attr('disabled', 'disabled');
                }
            });
        });
        
        $('#place_order').click(function(event){
            event.preventDefault();
            $.post(template.ajaxurl, {action: 'session_cart'}, function(response){
                //console.log(response);
                if(response == 'success'){
                    $('#pay_with_paypal').submit();
                }
            });
        });
        
        $('input[type=radio][name=only_one]').change(function(){
            $('.sel-only-one').children('input').removeAttr('checked').attr('disabled', 'disabled');
            $(this).parent().next('.sel-only-one').children('input').removeAttr('disabled');
            $(this).parent().next('.sel-only-one').children('input:first').prop('checked', true);
        });
        
        $(window).load(function(){
            setInterval(checkTime, 1800000);
        });
        
        $('input[type=radio][name=select_payment]').change(function(){
            if($(this).val() == 'paypal_standered'){
                $('#pay_with_paypal').show();
                $('#checkout_label').show();
                $('#paypal_pro').hide();
            }
            if($(this).val() == 'paypal_pro'){
                $('#pay_with_paypal').hide();
                $('#checkout_label').hide();
                $('#paypal_pro').show();
            }
        });
        
        $('#place_order_with_credit_card').click(function(event){
            event.preventDefault();
            HoldOn.open({
                theme:'sk-circle',
                message:"<p>Do not refresh the page while your transaction is being processing...</p>"
            });
            $.post(template.ajaxurl, {action: 'session_cart'}, function(response){
                //console.log(response);
                setTimeout(function(){
                    HoldOn.close();
                },7000);
                if(response == 'success'){
                    $('#paypal_pro').submit();
                }
            });
        });
    });
})(jQuery);