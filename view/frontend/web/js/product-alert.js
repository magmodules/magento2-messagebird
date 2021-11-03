define([
    "jquery",
    "jquery/ui"
], function($){
    "use strict";

    function main(config, element) {
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

        let $element = $(element);
        let url = config.AjaxUrl;
        let modalContent = $('#modal-content');
        $(document).on('click', '.messagebird-alert-' + config.type, function() {
            $.ajax({
                showLoader: true,
                url: url,
                type: "GET",
                dataType: 'json'
            }).done(function (data) {
                modalContent.find('.ms-modal-content').html(data);
                modalContent.modal("openModal");
            });
        });
    }
    return main;
});
