(function($){



    var loc = '/wp-content/plugins/cdek-delivery-for-woocommerce/assets/js/';


    $('#city_cdek24_select').select2({
        minimumInputLength: 2,
        ajax: {
            url: loc+'city-code.php',
            dataType: 'json',
            type: "GET",
            quietMillis: 50,

            data: function (term) {
                console.log(term);
                return {
                    term: term
                };
            },
            processResults: function (data) {
                return {
                    results: $.map(data, function (item) {
                        return {
                            text: item.name,
                            id: item.id
                        }
                    })
                };
            },
            language: "ru-RU"
            
        }

    });

    





    $( 'form.checkout' ).on( 'change', '#city_cdek24_select', function() { 
        
        $('input[name^="billing_city"]').val($('#city_cdek24_select').val());
        $('#billing_city').val($('#city_cdek24_select').select2('data')[0].text);
        $('body').trigger('update_checkout');
        var text = $('#city_cdek24_select').select2('data');
       
 
    });
})(jQuery);
