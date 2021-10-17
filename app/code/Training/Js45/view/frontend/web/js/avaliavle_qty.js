define([
    'uiComponent',
    'jquery',
    'ko'
], function (Component, $, ko) {
    'use strict';
    return Component.extend({
        displayProductQty: ko.observable(''),
        isLoading: ko.observable(false),
        url: '',
        initialize: function () {
            this._super();
            return this;
        },
        getProductQty: function (el) {
            this.displayProductQty('');
            this.isLoading(true);
            var self = this;
            let data = {productId: $('#current-product-id').data('product-id')}
            $.ajax({
                url: self.url,
                type: 'post',
                data: data,
                dataType: 'json'})
                .done(function (data) {
                    if (data.qty) {
                        self.displayProductQty(data.qty);
                    }
                }).always(function () {
                self.isLoading(false);
            });
        }
    });
});
