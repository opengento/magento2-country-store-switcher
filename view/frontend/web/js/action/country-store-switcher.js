/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */

define([
    'jquery',
    'jquery/ui',
    'mage/dataPost'
], function ($, jqueryUi, dataPost) {
    'use strict';

    $.widget('mage.postCountryStoreSwitcher', {
        _create: function () {
            this._bind();
        },

        _bind: function () {
            $(document).on('change', 'select[data-role="country-store-switcher-post"]', function (event) {
                dataPost().postData($('option[value=' + event.target.value + ']', event.target).data('post'));
            });
        }
    });

    return $.mage.postCountryStoreSwitcher;
});
