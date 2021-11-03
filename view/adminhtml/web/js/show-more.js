require([
    'jquery'
], function ($) {

    var $mmHeadingComment = $('.magmodules-messagebird-heading-comment');

    if($mmHeadingComment.length) {

        $(window).load(function() {

            var showMoreLessBtnHtml = '<div class="magmodules-messagebird-show-more-actions"><a href="javascript:void(0)" class="magmodules-messagebird-show-btn-more">'
                + $.mage.__('Show more.') + '</a>'
                + '<a href="javascript:void(0)" class="magmodules-messagebird-show-btn-less">' + $.mage.__('Show less.') + '</a></div>';

            $mmHeadingComment.each(function (i, el) {
                var elStyles = getComputedStyle(el);
                var $el = $(el);
                var oldHtml = $el.html();
                var ellipsesIndex = oldHtml.length;
                var maxElHeight = parseInt(elStyles.lineHeight) * 2;

                if (maxElHeight < $el.outerHeight()) {

                    while (maxElHeight < $el.outerHeight()) {
                        $el.html(function (index, text) {
                            var newText = text.replace(/\W*\s(\S)*$/, '');
                            ellipsesIndex = newText.length;
                            return newText;
                        });
                    }

                    var visibleStr = oldHtml.substr(0, ellipsesIndex);
                    var hiddenStr = oldHtml.substr(ellipsesIndex);

                    $el.html('<span>' + visibleStr + '</span><span class="magmodules-messagebird-show-more-block">'
                        + hiddenStr.replace('<br/>', '<div></div>')
                        + '</span>' + showMoreLessBtnHtml);

                }
            });
        });

        /**
         * Toggle show more btn event.
         */
        $(document).on('click', '.magmodules-messagebird-show-more-actions a', function() {
            $(this).closest('.magmodules-messagebird-heading-comment').toggleClass('magmodules-messagebird-show-more-active');
        });
    }
});
