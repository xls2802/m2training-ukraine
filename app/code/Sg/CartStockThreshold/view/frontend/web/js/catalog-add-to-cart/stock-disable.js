define([
    'uiComponent',
    'Magento_Customer/js/customer-data'
], function (Component, customerData) {
    'use strict';

    return Component.extend({
        initialize: function () {
            this._super();
            customerData.reload(['addtocartthreshold'], false);
            this.getEnabled = customerData.get('addtocartthreshold');
        }
    });
});


/*
Here could be written a mixin for Magento_Catalog/js/catalog-add-to-cart to "_create:" method  and rewrited this logic
$(this.options.addToCartButtonSelector).attr('disabled', false);
But in this case, I assumed, it will affect to original logic of disabling add-to-cart button and I thought
that it would be better to have my own independent component for my custom logic
*/
