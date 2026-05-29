import '../scss/front-page.scss';

jQuery(function($) {
	/**
	 * ヒーロースライダー(sp)
	 */
	function setupSlider(options) {
		var $root = $(options.rootSelector);
		var eventNamespace = options.eventNamespace || '.slider';

		if (!$root.length) {
			return;
		}

		var $slides = $root.find(options.slideSelector);
		var $dots = $root.find(options.dotSelector);
		var $prev = options.prevSelector ? $root.find(options.prevSelector).first() : $();
		var $next = options.nextSelector ? $root.find(options.nextSelector).first() : $();

		if (!$slides.length || !$dots.length || $slides.length !== $dots.length) {
			return;
		}

		var currentIndex = $slides.index($slides.filter('.is-active').first());
		var timerId = null;
		var intervalMs = options.autoplayMs || 0;
		var enableSwipe = !!options.enableSwipe;
		var $swipeTarget = options.swipeTargetSelector ? $root.find(options.swipeTargetSelector).first() : $root;
		var touchStartX = 0;
		var touchStartY = 0;
		var hasTouchStarted = false;
		var swipeThreshold = options.swipeThreshold || 40;

		if (currentIndex < 0) {
			currentIndex = 0;
		}

		function normalizeIndex(index) {
			if (index < 0) {
				return $slides.length - 1;
			}
			if (index >= $slides.length) {
				return 0;
			}
			return index;
		}

		function setActiveSlide(index) {
			var nextIndex = normalizeIndex(index);

			$slides.removeClass('is-active').eq(nextIndex).addClass('is-active');
			$dots.removeClass('is-active').attr('aria-current', 'false');
			$dots.eq(nextIndex).addClass('is-active').attr('aria-current', 'true');

			currentIndex = nextIndex;

			if (typeof options.onAfterChange === 'function') {
				options.onAfterChange(nextIndex);
			}
		}

		function goNext() {
			setActiveSlide(currentIndex + 1);
		}

		function goPrev() {
			setActiveSlide(currentIndex - 1);
		}

		function stopAutoplay() {
			if (timerId !== null) {
				clearInterval(timerId);
				timerId = null;
			}
		}

		function restartAutoplay() {
			if (!intervalMs) {
				return;
			}

			stopAutoplay();
			timerId = setInterval(goNext, intervalMs);
		}

		function destroy() {
			stopAutoplay();
			$dots.off(eventNamespace);
			$prev.off(eventNamespace);
			$next.off(eventNamespace);
			$root.off(eventNamespace);
			$swipeTarget.off(eventNamespace);
		}

		destroy();

		$dots.each(function(index) {
			$(this).on('click' + eventNamespace, function() {
				setActiveSlide(index);
				restartAutoplay();
			});
		});

		if ($prev.length) {
			$prev.on('click' + eventNamespace, function() {
				goPrev();
				restartAutoplay();
			});
		}

		if ($next.length) {
			$next.on('click' + eventNamespace, function() {
				goNext();
				restartAutoplay();
			});
		}

		$root.on('mouseenter' + eventNamespace, stopAutoplay);
		$root.on('mouseleave' + eventNamespace, restartAutoplay);

		if (enableSwipe && $swipeTarget.length) {
			$swipeTarget.on('touchstart' + eventNamespace, function(e) {
				if (!e.originalEvent.touches || e.originalEvent.touches.length !== 1) {
					hasTouchStarted = false;
					return;
				}

				var touch = e.originalEvent.touches[0];
				touchStartX = touch.clientX;
				touchStartY = touch.clientY;
				hasTouchStarted = true;
				stopAutoplay();
			});

			$swipeTarget.on('touchend' + eventNamespace, function(e) {
				if (!hasTouchStarted || !e.originalEvent.changedTouches || !e.originalEvent.changedTouches.length) {
					restartAutoplay();
					return;
				}

				var touch = e.originalEvent.changedTouches[0];
				var deltaX = touch.clientX - touchStartX;
				var deltaY = touch.clientY - touchStartY;
				hasTouchStarted = false;

				if (Math.abs(deltaX) <= swipeThreshold || Math.abs(deltaX) <= Math.abs(deltaY)) {
					restartAutoplay();
					return;
				}

				if (deltaX < 0) {
					goNext();
				} else {
					goPrev();
				}

				restartAutoplay();
			});

			$swipeTarget.on('touchcancel' + eventNamespace, function() {
				hasTouchStarted = false;
				restartAutoplay();
			});
		}

		setActiveSlide(currentIndex);
		restartAutoplay();

		return {
			destroy: destroy,
		};
	}

	// SP用のヒーロースライダーの初期化と同期
	var heroSliderSpInstance = null;
	function syncHeroSliderSp() {
		var isSpViewport = window.matchMedia('(max-width: 767px)').matches;

		if (isSpViewport && !heroSliderSpInstance) {
			// SP用のドットを再生成
			var $hero = $('.p-front-page__hero');
			var $slides = $hero.find('.p-front-page__hero__slider-sp .p-front-page__hero__link');
			var $pagination = $hero.find('.p-front-page__hero__pagination');

			$pagination.empty();
			for (var i = 0; i < $slides.length; i += 1) {
				var $dot = $('<button type="button" class="p-front-page__hero__dot" aria-label="スライド' + (i + 1) + 'を表示"></button>');
				if (i === 0) {
					$dot.addClass('is-active').attr('aria-current', 'true');
				}
				$pagination.append($dot);
			}

			heroSliderSpInstance = setupSlider({
				rootSelector: '.p-front-page__hero',
				slideSelector: '.p-front-page__hero__slider-sp .p-front-page__hero__link',
				dotSelector: '.p-front-page__hero__dot',
				swipeTargetSelector: '.p-front-page__hero__slider-sp',
				enableSwipe: true,
				autoplayMs: 9000,
				eventNamespace: '.heroSliderSp',
			});
		}

		if (!isSpViewport && heroSliderSpInstance) {
			heroSliderSpInstance.destroy();
			heroSliderSpInstance = null;

			// PC用のドットに復元
			var $paginationPc = $('.p-front-page__hero__pagination');
			$paginationPc.html(
				'<button class="p-front-page__hero__dot hero__dot--01 is-active" type="button" aria-label="スライド1を表示" aria-current="true"></button>' +
				'<button class="p-front-page__hero__dot hero__dot--02" type="button" aria-label="スライド2を表示"></button>' +
				'<button class="p-front-page__hero__dot hero__dot--03" type="button" aria-label="スライド3を表示"></button>' +
				'<button class="p-front-page__hero__dot hero__dot--04" type="button" aria-label="スライド4を表示"></button>' +
				'<button class="p-front-page__hero__dot hero__dot--05" type="button" aria-label="スライド5を表示"></button>'
			);
		}
	}

	// リサイズ時にヒーロースライダーを同期
	syncHeroSliderSp();
	$(window).on('resize.heroSliderSpInit', syncHeroSliderSp);

	// 特売スライダーのモード別インスタンス
	var specialOffersSliderSpInstance = null;
	var specialOffersSliderPcInstance = null;
	var lastSpecialOffersMode = null;
	var specialOffersInitialSliderHtml = null;

	function extractSpecialOffersKeyFromClass(className, prefix) {
		var matched = (className || '').match(new RegExp(prefix + '--(\\d{2})'));

		return matched ? matched[1] : null;
	}

	function resetSpecialOffersToFirst() {
		var $page = $('.p-front-page');
		var $slider = $page.find('.p-front-page__special-offers__slider').first();
		var $items = $slider.find('.p-front-page__special-offers__item');
		var $dots = $page.find('.p-front-page__special-offers__pagination__dot');

		if ($slider.length && $items.length) {
			$slider.find('.p-front-page__special-offers__item').removeClass('is-active');
			$slider.find('.special-offers__item--01').first().addClass('is-active');
		}

		if ($dots.length) {
			$dots.removeClass('is-active').attr('aria-current', 'false');
			$dots.filter('.special-offers__pagination__dot--01').first().addClass('is-active').attr('aria-current', 'true');
		}
	}

	function captureSpecialOffersInitialSliderHtml() {
		if (specialOffersInitialSliderHtml !== null) {
			return;
		}

		var $slider = $('.p-front-page__special-offers__slider').first();

		if ($slider.length) {
			specialOffersInitialSliderHtml = $slider.html();
		}
	}

	function rerenderSpecialOffersSliderFromInitial() {
		if (specialOffersInitialSliderHtml === null) {
			return;
		}

		var $slider = $('.p-front-page__special-offers__slider').first();

		if ($slider.length) {
			$slider.html(specialOffersInitialSliderHtml);
		}
	}

	function updateSpecialOffersNoteWrapState() {
		var $notes = $('.p-front-page__special-offers__item__details__title__note');
		var wrappedDetailsClass = 'is-wrapped';
		var detailsSelector = '.p-front-page__special-offers__item__details';

		if (!$notes.length) {
			return;
		}

		$(detailsSelector).removeClass(wrappedDetailsClass);

		$notes.each(function() {
			var element = this;
			var style = window.getComputedStyle(element);
			var lineHeight = parseFloat(style.lineHeight);

			if (!lineHeight || Number.isNaN(lineHeight)) {
				var fontSize = parseFloat(style.fontSize) || 0;
				lineHeight = fontSize * 1.4;
			}

			if (!lineHeight) {
				return;
			}

			var isWrapped = element.scrollHeight > (lineHeight + 1);
			if (isWrapped) {
				$(element).closest(detailsSelector).addClass(wrappedDetailsClass);
			}
		});
	}

	function updateSpecialOffersItemNotePrefixForTablet() {
		var $notes = $('.p-front-page__special-offers__item--note');
		var isTabletViewport = window.matchMedia('(min-width: 768px) and (max-width: 1023px)').matches;

		if (!$notes.length) {
			return;
		}

		$notes.each(function() {
			var $note = $(this);
			var originalText = $note.data('originalNoteText');

			if (typeof originalText !== 'string') {
				originalText = $note.text().replace(/^※/, '');
				$note.data('originalNoteText', originalText);
			}

			$note.text(isTabletViewport ? ('※' + originalText) : originalText);
		});
	}

	function updateSpecialOffersGoodDealClass() {
		var $items = $('.p-front-page__special-offers__slider .p-front-page__special-offers__item');

		if (!$items.length) {
			return;
		}

		$items.removeClass('is-good-deal');
		$items.filter('.is-active').addClass('is-good-deal');
	}

	/**
	 * 特売スライダー(sp) を初期化
	 */
	function setupSpecialOffersSliderSp() {
		var $page = $('.p-front-page');
		var $root = $page.find('.p-front-page__special-offers').first();
		var $items = $root.find('.p-front-page__special-offers__slider .p-front-page__special-offers__item');
		var $dots = $page.find('.p-front-page__special-offers__pagination__dot');

		if (!$root.length || !$items.length || !$dots.length || $items.length !== $dots.length) {
			return null;
		}

		$items.removeClass('is-active').eq(0).addClass('is-active');
		$dots.removeClass('is-active').attr('aria-current', 'false');
		$dots.eq(0).addClass('is-active').attr('aria-current', 'true');

		return setupSlider({
			rootSelector: '.p-front-page',
			slideSelector: '.p-front-page__special-offers__slider .p-front-page__special-offers__item',
			dotSelector: '.p-front-page__special-offers__pagination__dot',
			prevSelector: '.c-slider--btn--left[aria-label="前の特売商品へ"]',
			nextSelector: '.c-slider--btn--right[aria-label="次の特売商品へ"]',
			swipeTargetSelector: '.p-front-page__special-offers__slider',
			enableSwipe: true,
			autoplayMs: 9000,
			eventNamespace: '.specialOffersSliderSp',
			onAfterChange: function() {
				updateSpecialOffersNoteWrapState();
				updateSpecialOffersGoodDealClass();
			},
		});
	}

	/**
	 * 特売スライダー(pc) を初期化
	 */
	function setupSpecialOffersSliderPc() {
		var $page = $('.p-front-page');
		var $root = $page.find('.p-front-page__special-offers').first();
		var $slider = $root.find('.p-front-page__special-offers__slider');
		var $prev = $root.find('.c-slider--btn--left[aria-label="前の特売商品へ"]').first();
		var $next = $root.find('.c-slider--btn--right[aria-label="次の特売商品へ"]').first();
		var $dots = $page.find('.p-front-page__special-offers__pagination__dot');
		var autoplayMs = 9000;
		var timerId = null;
		var isAnimating = false;
		var isDestroyed = false;

		if (!$root.length || !$slider.length || !$prev.length || !$next.length) {
			return null;
		}

		function alignActiveItemToCenter() {
			var $items = $slider.find('.p-front-page__special-offers__item');

			if (!$items.length) {
				return;
			}

			var centerIndex = $items.length >= 3 ? 1 : 0;
			var activeIndex = $items.index($items.filter('.is-active').first());

			if (activeIndex < 0) {
				activeIndex = 0;
			}

			while (activeIndex > centerIndex) {
				$items = $slider.find('.p-front-page__special-offers__item');
				$slider.append($items.first());
				activeIndex -= 1;
			}

			while (activeIndex < centerIndex) {
				$items = $slider.find('.p-front-page__special-offers__item');
				$slider.prepend($items.last());
				activeIndex += 1;
			}
		}

		function syncCenteredActiveAndDot() {
			var $items = $slider.find('.p-front-page__special-offers__item');

			if (!$items.length) {
				return;
			}

			var centerIndex = $items.length >= 3 ? 1 : 0;
			var $activeItem = $items.eq(centerIndex);

			$items.removeClass('is-active').removeClass('is-good-deal');
			$activeItem.addClass('is-active').addClass('is-good-deal');

			if (!$dots.length) {
				return;
			}

			var activeClass = $activeItem.attr('class') || '';
			var matched = activeClass.match(/special-offers__item--(\d{2})/);

			if (!matched) {
				return;
			}

			var activeKey = matched[1];

			$dots.each(function() {
				var $dot = $(this);
				var dotClass = $dot.attr('class') || '';
				var dotMatched = dotClass.match(/special-offers__pagination__dot--(\d{2})/);

				if (dotMatched && dotMatched[1] === activeKey) {
					$dot.addClass('is-active').attr('aria-current', 'true');
				} else {
					$dot.removeClass('is-active').attr('aria-current', 'false');
				}
			});

			updateSpecialOffersNoteWrapState();
		}

		function rotateNext() {
			var $items = $slider.find('.p-front-page__special-offers__item');
			$slider.append($items.first());
			syncCenteredActiveAndDot();
		}

		function rotatePrev() {
			var $items = $slider.find('.p-front-page__special-offers__item');
			$slider.prepend($items.last());
			syncCenteredActiveAndDot();
		}

		function getVisibleItemsForPc() {
			return $slider.find('.p-front-page__special-offers__item').slice(0, 3);
		}

		function getItemKeyFromElement(element) {
			var matched = ((element && element.className) || '').match(/special-offers__item--(\d{2})/);

			return matched ? matched[1] : null;
		}

		function resetPcItemAnimationState() {
			$slider.find('.p-front-page__special-offers__item').each(function() {
				this.getAnimations().forEach(function(animation) {
					animation.cancel();
				});

				this.style.opacity = '';
				this.style.transform = '';
				this.style.animation = '';
			});
		}

		function animateRotate(direction, speedProfile) {
			if (isAnimating || isDestroyed) {
				return Promise.resolve(false);
			}

			isAnimating = true;

			var isFast = speedProfile === 'fast';
			var leaveDuration = isFast ? 160 : 320;
			var enterDuration = isFast ? 200 : 380;

			var isNext = direction === 'next';
			var leaveX = isNext ? '-1.25vw' : '1.25vw';
			var enterX = isNext ? '1.25vw' : '-1.25vw';
			var $visibleBefore = getVisibleItemsForPc();

			var leaveAnimations = [];
			$visibleBefore.each(function() {
				leaveAnimations.push(this.animate([
					{ opacity: 1, transform: 'translateX(0)' },
					{ opacity: 0, transform: 'translateX(' + leaveX + ')' }
				], {
					duration: leaveDuration,
					easing: 'ease',
					fill: 'forwards',
				}));
			});

			return Promise.all(leaveAnimations.map(function(animation) {
				return animation.finished.catch(function() {
					return null;
				});
			})).then(function() {
				if (isDestroyed) {
					return false;
				}

				if (isNext) {
					rotateNext();
				} else {
					rotatePrev();
				}

				if (isDestroyed) {
					return false;
				}

				var $visibleAfter = getVisibleItemsForPc();
				var enterAnimations = [];

				$visibleAfter.each(function() {
					enterAnimations.push(this.animate([
						{ opacity: 0, transform: 'translateX(' + enterX + ')' },
						{ opacity: 1, transform: 'translateX(0)' }
					], {
						duration: enterDuration,
						easing: 'cubic-bezier(0.22, 0.61, 0.36, 1)',
						fill: 'both',
					}));
				});

				return Promise.all(enterAnimations.map(function(animation) {
					return animation.finished.catch(function() {
						return null;
					});
				})).then(function() {
					return !isDestroyed;
				});
			}).finally(function() {
				isAnimating = false;
			});
		}

		function moveItemToCenterByKey(targetKey, speedProfile) {
			var $items = $slider.find('.p-front-page__special-offers__item');
			var total = $items.length;
			var centerIndex = total >= 3 ? 1 : 0;
			var targetIndex = -1;

			$items.each(function(index) {
				if (targetIndex >= 0) {
					return;
				}

				if (getItemKeyFromElement(this) === targetKey) {
					targetIndex = index;
				}
			});

			if (targetIndex < 0 || total <= 1) {
				return Promise.resolve(false);
			}

			var stepsNext = (targetIndex - centerIndex + total) % total;
			var stepsPrev = (centerIndex - targetIndex + total) % total;
			var direction = stepsNext <= stepsPrev ? 'next' : 'prev';
			var steps = Math.min(stepsNext, stepsPrev);

			if (steps === 0) {
				syncCenteredActiveAndDot();
				return Promise.resolve(true);
			}

			var sequence = Promise.resolve();

			for (var i = 0; i < steps; i += 1) {
				sequence = sequence.then(function() {
					return animateRotate(direction, speedProfile);
				});
			}

			return sequence;
		}

		function stopAutoplay() {
			if (timerId !== null) {
				clearInterval(timerId);
				timerId = null;
			}
		}

		function restartAutoplay() {
			stopAutoplay();
			timerId = setInterval(function() {
				animateRotate('next');
			}, autoplayMs);
		}

		$prev.on('click.specialOffersPc', function() {
			animateRotate('prev');
			restartAutoplay();
		});
		$next.on('click.specialOffersPc', function() {
			animateRotate('next');
			restartAutoplay();
		});
		$dots.on('click.specialOffersPc', function() {
			var matched = (this.className || '').match(/special-offers__pagination__dot--(\d{2})/);

			if (!matched) {
				return;
			}

			stopAutoplay();
			moveItemToCenterByKey(matched[1], 'fast').then(function() {
				restartAutoplay();
			});
		});
		$root.on('mouseenter.specialOffersPc', stopAutoplay);
		$root.on('mouseleave.specialOffersPc', restartAutoplay);
		alignActiveItemToCenter();
		syncCenteredActiveAndDot();
		updateSpecialOffersGoodDealClass();
		restartAutoplay();

		return {
			destroy: function() {
				isDestroyed = true;
				stopAutoplay();
				resetPcItemAnimationState();
				$prev.off('.specialOffersPc');
				$next.off('.specialOffersPc');
				$dots.off('.specialOffersPc');
				$root.off('.specialOffersPc');
			},
		};
	}

	/**
	 * 特売スライダーをSP/PCで排他的に同期
	 */
	function syncSpecialOffersSliderMode() {
		var isSpViewport = window.matchMedia('(max-width: 767px)').matches;
		var nextMode = isSpViewport ? 'sp' : 'pc';

		captureSpecialOffersInitialSliderHtml();

		if (isSpViewport) {
			if (specialOffersSliderPcInstance) {
				specialOffersSliderPcInstance.destroy();
				specialOffersSliderPcInstance = null;
			}

			if (nextMode !== lastSpecialOffersMode) {
				rerenderSpecialOffersSliderFromInitial();
				resetSpecialOffersToFirst();
			}

			if (!specialOffersSliderSpInstance) {
				specialOffersSliderSpInstance = setupSpecialOffersSliderSp();
			}

			updateSpecialOffersItemNotePrefixForTablet();
			updateSpecialOffersNoteWrapState();

			lastSpecialOffersMode = nextMode;
			return;
		}

		if (specialOffersSliderSpInstance) {
			specialOffersSliderSpInstance.destroy();
			specialOffersSliderSpInstance = null;
		}

		if (nextMode !== lastSpecialOffersMode) {
			resetSpecialOffersToFirst();
		}

		if (!specialOffersSliderPcInstance) {
			specialOffersSliderPcInstance = setupSpecialOffersSliderPc();
		}

		updateSpecialOffersItemNotePrefixForTablet();
		updateSpecialOffersNoteWrapState();

		lastSpecialOffersMode = nextMode;
	}

	// リサイズ時に特売スライダーをSP/PC排他で同期
	syncSpecialOffersSliderMode();
	$(window).on('resize.specialOffersSliderMode', syncSpecialOffersSliderMode);
	$(window).on('resize.specialOffersNoteWrap', function() {
		updateSpecialOffersItemNotePrefixForTablet();
		updateSpecialOffersNoteWrapState();
	});

	/**
	 * ヒーロースライダー(pc) ページネーション
	 */
	function setupHeroPaginationPc(options) {
		var sliderSelector = options && options.sliderSelector ? options.sliderSelector : '.p-front-page__hero__slider-pc';
		var eventNamespace = options && options.eventNamespace ? options.eventNamespace : '.heroPaginationPc';
		var $hero = $('.p-front-page__hero');
		var $slider = $hero.find(sliderSelector).first();
		var $dots = $hero.find('.p-front-page__hero__dot');
		var $slides = $slider.find('.p-front-page__hero__link');
		var currentKey = null;
		var rafId = null;
		var lastViewportWidth = (window.visualViewport && window.visualViewport.width) ? window.visualViewport.width : window.innerWidth;

		if (!$hero.length || !$slider.length || !$dots.length || !$slides.length) {
			return null;
		}

		function syncHeroPcFlowMetrics() {
			var $currentSlides = $slider.find('.p-front-page__hero__link');
			var loopMetrics = getLoopMetrics($currentSlides);

			if (!loopMetrics) {
				return;
			}

			var firstRect = $currentSlides.get(0).getBoundingClientRect();
			var loopStartRect = $currentSlides.get(loopMetrics.loopSlideCount).getBoundingClientRect();
			var loopWidth = loopStartRect.left - firstRect.left;

			if (loopWidth > 0) {
				$hero.css('--hero-slider-pc-loop-width', loopWidth + 'px');
			}
		}

		function getLoopMetrics($currentSlides) {
			if (!$currentSlides || $currentSlides.length <= 1) {
				return null;
			}

			var firstKey = resolveSlideKey($($currentSlides.get(0)), 0);
			var repeatIndex = -1;

			if (!firstKey) {
				return null;
			}

			$currentSlides.each(function(index) {
				if (index === 0 || repeatIndex >= 0) {
					return;
				}

				var key = resolveSlideKey($(this), index);
				if (key === firstKey) {
					repeatIndex = index;
				}
			});

			if (repeatIndex <= 0) {
				return null;
			}

			return {
				loopSlideCount: repeatIndex,
			};
		}

		function getNumberedModifier(className, prefix) {
			var matched = className.match(new RegExp(prefix + '--(\\d{2})'));

			return matched ? matched[1] : null;
		}

		function formatSlideKeyFromIndex(index) {
			var slideNumber = (index % $dots.length) + 1;

			return String(slideNumber).padStart(2, '0');
		}

		function resolveSlideKey($slide, index) {
			var className = $slide.attr('class') || '';
			var fromModifier = getNumberedModifier(className, 'hero__link');

			if (fromModifier) {
				return fromModifier;
			}

			return formatSlideKeyFromIndex(index);
		}

		function getSlidesByKey(targetKey) {
			var $matchedSlides = $slides.filter('.hero__link--' + targetKey);

			if ($matchedSlides.length) {
				return $matchedSlides;
			}

			return $slides.filter(function(index) {
				return resolveSlideKey($(this), index) === targetKey;
			});
		}

		function getCurrentTranslateX() {
			var transformValue = window.getComputedStyle($slider.get(0)).transform;

			if (!transformValue || transformValue === 'none') {
				return 0;
			}

			return new DOMMatrixReadOnly(transformValue).m41;
		}

		function updateDots(activeKey) {
			$dots.each(function() {
				var $dot = $(this);
				var dotKey = getNumberedModifier($dot.attr('class') || '', 'hero__dot');

				if (dotKey === activeKey) {
					$dot.addClass('is-active').attr('aria-current', 'true');
				} else {
					$dot.removeClass('is-active').attr('aria-current', 'false');
				}
			});
		}

		function getCenteredSlideKey() {
			var heroRect = $hero.get(0).getBoundingClientRect();
			var heroCenterX = heroRect.left + (heroRect.width / 2);
			var closestKey = null;
			var closestDistance = null;

			$slides.each(function(index) {
				var slideKey = resolveSlideKey($(this), index);

				if (!slideKey) {
					return;
				}

				var slideRect = this.getBoundingClientRect();
				var slideCenterX = slideRect.left + (slideRect.width / 2);
				var distance = Math.abs(heroCenterX - slideCenterX);

				if (closestDistance === null || distance < closestDistance) {
					closestDistance = distance;
					closestKey = slideKey;
				}
			});

			return closestKey;
		}

		function syncDotsToCenteredSlide() {
			var centeredKey = getCenteredSlideKey();

			if (centeredKey) {
				currentKey = centeredKey;
				updateDots(centeredKey);
			}

			rafId = window.requestAnimationFrame(syncDotsToCenteredSlide);
		}

		function destroy() {
			if (rafId !== null) {
				cancelAnimationFrame(rafId);
				rafId = null;
			}

			$dots.off(eventNamespace);
			$(window).off(eventNamespace);
		}

		function normalizeStartOffset(positionX) {
			var sliderElement = $slider.get(0);
			var heroElement = $hero.get(0);
			var $currentSlides = $slider.find('.p-front-page__hero__link');

			if (!sliderElement || !heroElement || !$currentSlides.length) {
				return positionX;
			}

			var slideWidth = $currentSlides.eq(0).outerWidth();
			var computedStyle = window.getComputedStyle(sliderElement);
			var gap = parseFloat(computedStyle.gap || computedStyle.columnGap || '0') || 0;
			var loopWidth = parseFloat(window.getComputedStyle(heroElement).getPropertyValue('--hero-slider-pc-loop-width'));
			var viewportWidth = heroElement.getBoundingClientRect().width;

			if (!Number.isFinite(loopWidth) || loopWidth <= 0 || !Number.isFinite(viewportWidth) || viewportWidth <= 0 || !Number.isFinite(slideWidth) || slideWidth <= 0) {
				return positionX;
			}

			var normalized = positionX;

			while (normalized < -loopWidth) {
				normalized += loopWidth;
			}

			while (normalized > 0) {
				normalized -= loopWidth;
			}

			return normalized;
		}

		function restartAnimationFromPosition(positionX) {
			var normalizedPositionX = normalizeStartOffset(positionX);

			$slider.css({
				animation: 'none',
				transform: 'translate3d(' + normalizedPositionX + 'px, 0, 0)',
			});

			$hero.css('--hero-slider-pc-start-offset', normalizedPositionX + 'px');
			$slider.get(0).offsetHeight;
			$slider.css({
				animation: '',
				transform: '',
			});
		}

		function centerSlideByKey(targetKey) {
			var $candidateSlides = getSlidesByKey(targetKey);

			if (!$candidateSlides.length) {
				return;
			}

			var heroRect = $hero.get(0).getBoundingClientRect();
			var heroCenterX = heroRect.left + (heroRect.width / 2);
			var currentTranslateX = getCurrentTranslateX();
			var targetElement = null;
			var minDistance = Infinity;

			$candidateSlides.each(function() {
				var slideRect = this.getBoundingClientRect();
				var slideCenterX = slideRect.left + (slideRect.width / 2);
				var distance = Math.abs(heroCenterX - slideCenterX);

				if (distance < minDistance) {
					minDistance = distance;
					targetElement = this;
				}
			});

			if (!targetElement) {
				return;
			}

			var slideRect = targetElement.getBoundingClientRect();
			var slideCenterX = slideRect.left + (slideRect.width / 2);
			var offsetX = heroCenterX - slideCenterX;

			restartAnimationFromPosition(currentTranslateX + offsetX);
			currentKey = targetKey;
			updateDots(targetKey);
		}

		$dots.each(function() {
			var $dot = $(this);
			var dotIndex = $dots.index($dot);
			var targetKey = getNumberedModifier($dot.attr('class') || '', 'hero__dot') || formatSlideKeyFromIndex(dotIndex);

			if (!targetKey) {
				return;
			}

			$dot.on('click' + eventNamespace, function() {
				centerSlideByKey(targetKey);
			});
		});

		$(window).on('resize' + eventNamespace, function() {
			var nextViewportWidth = (window.visualViewport && window.visualViewport.width) ? window.visualViewport.width : window.innerWidth;

			// SafariのURLバー表示/非表示で発生する「高さだけ変わるresize」では位置を再計算しない
			if (Math.abs(nextViewportWidth - lastViewportWidth) < 1) {
				return;
			}

			lastViewportWidth = nextViewportWidth;
			syncHeroPcFlowMetrics();

			if (currentKey) {
				centerSlideByKey(currentKey);
			}
		});

		syncHeroPcFlowMetrics();
		syncDotsToCenteredSlide();

		return {
			destroy: destroy,
		};
	}

	// PC用のヒーロースライダーページネーションの初期化と同期
	var heroSliderPcInstance = null;
	var heroPaginationMode = null;

	function getHeroPaginationMode() {
		if (window.matchMedia('(max-width: 767px)').matches) {
			return 'sp';
		}

		if (window.matchMedia('(max-width: 1023px)').matches) {
			return 'tablet';
		}

		return 'pc';
	}

	function syncHeroPaginationPc() {
		var nextMode = getHeroPaginationMode();
		var sliderSelector = nextMode === 'tablet' ? '.p-front-page__hero__slider-tablet' : '.p-front-page__hero__slider-pc';
		var eventNamespace = nextMode === 'tablet' ? '.heroPaginationTablet' : '.heroPaginationPc';

		if (nextMode === 'sp') {
			if (heroSliderPcInstance) {
				heroSliderPcInstance.destroy();
				heroSliderPcInstance = null;
			}

			heroPaginationMode = nextMode;
			return;
		}

		if (heroSliderPcInstance && nextMode !== heroPaginationMode) {
			heroSliderPcInstance.destroy();
			heroSliderPcInstance = null;
		}

		if (!heroSliderPcInstance) {
			heroSliderPcInstance = setupHeroPaginationPc({
				sliderSelector: sliderSelector,
				eventNamespace: eventNamespace,
			});
		}

		heroPaginationMode = nextMode;
	}

	// リサイズ時にヒーロースライダーのページネーションを同期
	syncHeroPaginationPc();
	$(window).on('resize.heroPaginationPcInit', syncHeroPaginationPc);

	/**
	 * 店舗一覧ページネーション
	 */
	function setupStoreTabsPagination() {
		var $shop = $('.p-front-page__shop');

		if (!$shop.length) {
			return;
		}

		var $tabs = $shop.find('.p-front-page__shop__group__tab');
		var $items = $shop.find('.c-shop-card');
		var $listWrapper = $shop.find('.js-shop-content');
		var $pagination = $shop.find('.p-front-page__shop__pagination');
		var $prev = $shop.find('.c-slider--btn--left[aria-label="前の店舗へ"]').first();
		var $next = $shop.find('.c-slider--btn--right[aria-label="次の店舗へ"]').first();
		function getStorePerPage() {
			if (window.matchMedia('(min-width: 1024px)').matches) {
				return 4;
			}

			if (window.matchMedia('(min-width: 768px)').matches) {
				return 3;
			}

			return 2;
		}

		var perPage = getStorePerPage();
		var currentFilter = 'all';
		var currentPage = 0;

		if (!$tabs.length || !$items.length || !$listWrapper.length) {
			return;
		}

		if (!$pagination.length) {
			$pagination = $('<div class="p-front-page__shop__pagination" aria-label="店舗一覧のページネーション"></div>');
			$listWrapper.after($pagination);
		}

		function getFilteredItems() {
			if (currentFilter === 'all') {
				return $items;
			}

			return $items.filter('.is-' + currentFilter);
		}

		function renderDots(pageCount) {
			$pagination.empty();

			for (var i = 0; i < pageCount; i += 1) {
				var isActive = i === currentPage;
				var $dot = $('<button type="button" class="p-front-page__shop__pagination__dot" aria-label="ページ' + (i + 1) + 'を表示"></button>');

				if (isActive) {
					$dot.addClass('is-active').attr('aria-current', 'true');
				}

				$dot.on('click', (function(pageIndex) {
					return function() {
						currentPage = pageIndex;
						updateVisibleItems();
					};
				})(i));

				$pagination.append($dot);
			}
		}

		function updateVisibleItems() {
			perPage = getStorePerPage();

			var $filtered = getFilteredItems();
			var pageCount = Math.max(1, Math.ceil($filtered.length / perPage));

			if (currentPage >= pageCount) {
				currentPage = 0;
			}

			var start = currentPage * perPage;
			var end = start + perPage;

			$items.removeClass('is-active');
			$filtered.slice(start, end).addClass('is-active');

			renderDots(pageCount);
		}

		function getPageCount() {
			var $filtered = getFilteredItems();

			return Math.max(1, Math.ceil($filtered.length / perPage));
		}

		$tabs.each(function(index) {
			$(this).on('click', function() {
				$tabs.removeClass('is-active active');
				$(this).addClass('is-active active');

				if (index === 0) {
					currentFilter = 'all';
				} else if (index === 1) {
					currentFilter = 'chiba';
				} else if (index === 2) {
					currentFilter = 'tokyo';
				} else {
					currentFilter = 'saitama';
				}

				currentPage = 0;
				updateVisibleItems();
			});
		});

		$tabs.removeClass('is-active active').eq(0).addClass('is-active active');
		updateVisibleItems();

		if ($prev.length) {
			$prev.on('click', function() {
				var pageCount = getPageCount();

				if (pageCount <= 1) {
					return;
				}

				currentPage = (currentPage - 1 + pageCount) % pageCount;
				updateVisibleItems();
			});
		}

		if ($next.length) {
			$next.on('click', function() {
				var pageCount = getPageCount();

				if (pageCount <= 1) {
					return;
				}

				currentPage = (currentPage + 1) % pageCount;
				updateVisibleItems();
			});
		}

		$(window).on('resize.shopTabsPagination', function() {
			var nextPerPage = getStorePerPage();

			if (nextPerPage !== perPage) {
				currentPage = 0;
				updateVisibleItems();
			}
		});
	}

	// 店舗一覧ページネーションを有効化
	setupStoreTabsPagination();
});