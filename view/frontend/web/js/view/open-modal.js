/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */

define([
    'uiComponent',
    'jquery',
    'mage/cookies'
], function (Component, $) {
    'use strict';

    return Component.extend({
        initialize: function () {
            this._super();

            if (!$.cookie(this.cookieName)) {
                let element = $(this.triggerSelector);
                if (element.length) {
                    element.first().trigger('click');
                }
                $.cookie(this.cookieName, 1);
            }
        }
    });
});
