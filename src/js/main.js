import '../scss/style.scss';

/**
 * jQuery
 */
jQuery(function($) {
    var $navToggle = $('.js-toggle--nav');
    var $nav = $('.l-header__nav');
    var $html = $('html');
    var $body = $('body');
    var $scrollTopButton = $('.c-btn--scroll-top');

    var $topPulldown = $('.l-header__nav__group.js-toggle--pulldown').first();
    var $prefGroups = $('.shop-info__group.js-toggle--pulldown');

    function popButton($target, dom) {
        $target.removeClass('is-pop');
        void dom.offsetWidth;
        $target.addClass('is-pop');
    }

    function isSpOrTabletViewport() {
        return window.matchMedia('(max-width: 1023px)').matches;
    }

    function openNav($trigger, triggerElement) {
        $nav.addClass('is-open').removeClass('is-closed');
        $html.addClass('is-fixed');
        $body.addClass('is-fixed');

        popButton($trigger, triggerElement);
    }

    function closeNav($trigger, triggerElement) {
        $nav.removeClass('is-open').addClass('is-closed');
        $html.removeClass('is-fixed');
        $body.removeClass('is-fixed');

        popButton($trigger, triggerElement);
    }

    $prefGroups.hide();
    $prefGroups.find('ul').hide();

    if ($nav.length && $navToggle.length) {
        $navToggle.on('click', function(e) {
            e.preventDefault();

            var $this = $(this);
            var willOpen = !$nav.hasClass('is-open');

            if (!willOpen) {
                closeNav($this, this);
                return;
            }

            if (isSpOrTabletViewport()) {
                window.scrollTo(0, 0);
                document.documentElement.scrollTop = 0;
                document.body.scrollTop = 0;

                window.requestAnimationFrame(function() {
                    openNav($this, $this.get(0));
                });
                return;
            }

            openNav($this, this);
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
        var $group = $this.closest('.shop-info__group');
        var $list = $group.find('ul').first();

        $group.toggleClass('is-open');
        $list.slideToggle(200);

        popButton($this, this);
    });

    if ($scrollTopButton.length) {
        function updateScrollTopButton() {
            if ($(window).scrollTop() > 200) {
                $scrollTopButton.addClass('is-visible');
            } else {
                $scrollTopButton.removeClass('is-visible');
            }
        }

        $scrollTopButton.on('click', function(e) {
            e.preventDefault();

            var supportsSmooth = 'scrollBehavior' in document.documentElement.style;
            window.scrollTo({
                top: 0,
                behavior: supportsSmooth ? 'smooth' : 'auto',
            });
        });

        $(window).on('scroll', updateScrollTopButton);
        updateScrollTopButton();
    }

    /**
     * vw算出(SP)
     */
    const valueSP = (10) / 390;
    const resultSP = Math.floor(valueSP * 10000) / 100;
    console.log('SP: ' + resultSP.toFixed(2) + 'vw');

    /**
     * vw算出(Tablet)
     */
    const valueTablet = (116.06) / 768;
    const resultTablet = Math.floor(valueTablet * 10000) / 100;
    console.log('Tablet: ' + resultTablet.toFixed(2) + 'vw');

    /**
     * vw算出(PC)
     */
    const valuePC = (115) / 1920;
    const resultPC = Math.floor(valuePC * 10000) / 100;
    console.log('PC: ' + resultPC.toFixed(2) + 'vw');
});