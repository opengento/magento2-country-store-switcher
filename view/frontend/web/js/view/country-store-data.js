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
            isInvalidated: function () {
                return this._super() || BASE_URL !== this.countryStoreData().base_url;
            }
        });
    };
});
