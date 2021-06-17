jQuery(document).ready(function($){    
    
    //Ajax for Add to Cart
    $('.btn-simple').click(function() {        
        $(this).addClass('adding-cart');
        var product_id = $(this).attr('id');
        
        $.ajax ({
            url     : yoga_fitness_data.url,  
            type    : 'POST',
            data    : 'action=yoga_fitness_add_cart_single&product_id=' + product_id,    
            success : function(results){
                $('#'+product_id).replaceWith(results);
            }
        }).done(function(){
            var cart = $('#cart-'+product_id).val();
            $('.cart .number').html(cart);         
        });
    });


});