/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */

var config = {
    map: {
        '*': {
            postCountryStoreSwitcher: 'Opengento_CountryStoreSwitcher/js/action/country-store-switcher'
        }
    },
    config: {
        mixins: {
            'Opengento_CountryStore/js/view/country-store-data': {
                'Opengento_CountryStoreSwitcher/js/view/country-store-data': true
            }
        }
    }
};
