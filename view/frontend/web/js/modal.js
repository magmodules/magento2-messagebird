define([
    'Magento_Ui/js/modal/modal',
    "jquery",
    "jquery/ui"
], function(modal, $){
    "use strict";

    function initModal(config, element) {
        var options = {
            type: 'popup',
            responsive: true,
            innerScroll: true,
            title: 'Product Subscription',
            buttons: [{
                text: $.mage.__('Close'),
                class: 'modal-close',
                click: function (){
                    this.closeModal();
                }
            }]
        };
        let popup = modal(options, $('#modal-content'));
    }
    return initModal;
});
