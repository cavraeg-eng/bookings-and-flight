(function () {
	'use strict';

	if (window.bafFrontendRuntimeLoaded) {
		return;
	}

	window.bafFrontendRuntimeLoaded = true;

	function getTravelpayoutsRedirectLink(event) {
		var path = typeof event.composedPath === 'function' ? event.composedPath() : [];
		var index;
		var node;

		for (index = 0; index < path.length; index += 1) {
			node = path[index];

			if (node && node.nodeType === 1 && node.matches && node.matches('a[href^="https://tpwgts.com/wl/redirect"]')) {
				return node;
			}
		}

		if (event.target && event.target.closest) {
			return event.target.closest('a[href^="https://tpwgts.com/wl/redirect"]');
		}

		return null;
	}

	document.addEventListener(
		'click',
		function (event) {
			var link;
			var target;
			var opened = null;

			if (event.button !== 0 || event.altKey || event.ctrlKey || event.metaKey || event.shiftKey) {
				return;
			}

			link = getTravelpayoutsRedirectLink(event);

			if (!link || !link.href) {
				return;
			}

			event.preventDefault();
			event.stopPropagation();

			target = link.getAttribute('target') || '_blank';

			try {
				opened = window.open(link.href, target, 'noopener');
			} catch (error) {
				opened = null;
			}

			if (!opened) {
				window.location.assign(link.href);
			}
		},
		true
	);
}());
