import '../scss/style.scss';

/**
 * jQuery
 */
jQuery(function($) {
    var $navToggle = $('.js-toggle--nav');
    var $nav = $('.l-header__nav');
    var $html = $('html');
    var $body = $('body');

    var $topPulldown = $('.l-header__nav__group.js-toggle--pulldown').first();
    var $prefGroups = $('.store-info__group.js-toggle--pulldown');

    function popButton($target, dom) {
        $target.removeClass('is-pop');
        void dom.offsetWidth;
        $target.addClass('is-pop');
    }

    $prefGroups.hide();
    $prefGroups.find('ul').hide();

    if ($nav.length && $navToggle.length) {
        $navToggle.on('click', function(e) {
            e.preventDefault();

            var $this = $(this);

            $nav.toggleClass('is-open');
            $nav.toggleClass('is-closed');
            $html.toggleClass('is-fixed');
            $body.toggleClass('is-fixed');

            popButton($this, this);
        });
    }

    $topPulldown.on('click', function() {
        var $this = $(this);

        $this.toggleClass('is-open');
        $prefGroups.slideToggle(200);

        popButton($this, this);
    });

    $prefGroups.on('click', '> p', function(e) {
        e.preventDefault();

        var $this = $(this);
        var $group = $this.closest('.store-info__group');
        var $list = $group.find('ul').first();

        $group.toggleClass('is-open');
        $list.slideToggle(200);

        popButton($this, this);
    });
});