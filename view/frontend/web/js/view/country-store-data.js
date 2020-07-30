/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */

define([
    'Magento_Customer/js/customer-data',
    'underscore'
], function (customerData, _) {
    'use strict';

    return function (CountryStoreData) {
        return CountryStoreData.extend({
            initialize: function () {
                this._super();

                if (this._hasScopeChanged()) {
                    customerData.set('country_store_data', {'reload': true});
                    customerData.reload(['country_store_data']);
                }
            },

            _hasScopeChanged: function () {
                return BASE_URL !== this.countryStoreData().base_url &&
                    !this.countryStoreData().reload &&
                    !_.contains(customerData.getExpiredSectionNames(), 'country_store_data');
            }
        });
    };
});
