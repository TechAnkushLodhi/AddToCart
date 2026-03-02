define([
    'jquery',
    'Magento_Ui/js/modal/modal'
], function ($, modal) {
    'use strict';

    var lastAddedProductId = null;

    /**
     * STEP 1: Capture clicked product ID (100% accurate)
     */
    $(document).on('submit', 'form[data-role=tocart-form]', function () {
        var productId = $(this).find('input[name=product]').val();

        if (productId) {
            lastAddedProductId = productId;
            console.log('Clicked Product ID:', lastAddedProductId);
        }
    });

    /**
     * STEP 2: After successful ajax add to cart → open popup
     */
    $(document).on('ajax:addToCart', function () {

        if (!lastAddedProductId) {
            return;
        }

        $.ajax({
            url: '/addtocartpopup/ajax/popup',
            type: 'POST',
            dataType: 'json',
            data: {
                product_id: lastAddedProductId
            },
            success: function (response) {

                var popup = $('<div />').html(response.html);

                modal({
                    type: 'popup',
                    title: 'Product Added To Cart',
                    responsive: true,
                    innerScroll: true,
                    buttons: []
                }, popup);

                popup.modal('openModal');

                // reset (important)
                lastAddedProductId = null;
            }
        });
    });
});
