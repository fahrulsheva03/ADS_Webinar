 <!-- Required vendors -->
 <script src="{{ asset('assetsAdmin/vendor/global/global.min.js') }}"></script>
 <script src="{{ asset('assetsAdmin/vendor/chartjs/chart.bundle.min.js') }}"></script>
 <script src="{{ asset('assetsAdmin/vendor/bootstrap-datetimepicker/js/moment.js') }}"></script>
 <script src="{{ asset('assetsAdmin/vendor/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js') }}"></script>
 <script src="{{ asset('assetsAdmin/vendor/bootstrap-select/js/bootstrap-select.min.js') }}"></script>

 <!-- Chart piety plugin files -->
 <script src="{{ asset('assetsAdmin/vendor/peity/jquery.peity.min.js') }}"></script>

 <!-- Apex Chart -->
 <script src="{{ asset('assetsAdmin/vendor/apexchart/apexchart.js') }}"></script>

 <!-- Dashboard 1 -->
 <script src="{{ asset('assetsAdmin/js/dashboard/dashboard-1.js') }}?v={{ filemtime(public_path('assetsAdmin/js/dashboard/dashboard-1.js')) }}"></script>

 <!-- localizationTool -->
 <script src="{{ asset('assetsAdmin/js/jquery.localizationTool.js') }}"></script>
 <script src="{{ asset('assetsAdmin/js/translator.js') }}"></script>

 <script src="{{ asset('assetsAdmin/js/custom.min.js') }}"></script>
 <script src="{{ asset('assetsAdmin/js/deznav-init.js') }}"></script>
 <script src="{{ asset('assetsAdmin/js/demo.js') }}"></script>

<script>
	(function () {
		function enforceSidebarFull() {
			try {
				if (typeof setCookie === 'function') {
					setCookie('sidebarStyle', 'full');
				} else {
					var d = new Date();
					d.setTime(d.getTime() + 30 * 60 * 1000);
					document.cookie = 'sidebarStyle=full;expires=' + d.toUTCString() + ';path=/';
				}
			} catch (e) {}

			try {
				var body = document.body;
				if (body) {
					body.setAttribute('data-sidebar-style', 'full');
				}
				var main = document.getElementById('main-wrapper');
				if (main) {
					main.classList.remove('menu-toggle');
					main.classList.remove('iconhover-toggle');
				}
			} catch (e) {}

			try {
				if (window.jQuery) {
					window.jQuery('body').attr('data-sidebar-style', 'full');
					window.jQuery('#main-wrapper').removeClass('menu-toggle iconhover-toggle');
				}
			} catch (e) {}
		}

		enforceSidebarFull();
		document.addEventListener('DOMContentLoaded', enforceSidebarFull);
		window.addEventListener('load', function () {
			setTimeout(enforceSidebarFull, 50);
			setTimeout(enforceSidebarFull, 300);
			setTimeout(enforceSidebarFull, 1200);
		});

		try {
			if (window.jQuery && window.jQuery.fn && window.jQuery.fn.append && !window.jQuery.fn.__dzAppendPatched) {
				var oldAppend = window.jQuery.fn.append;
				window.jQuery.fn.append = function () {
					if (arguments.length > 0) {
						var html = arguments[0];
						if (
							typeof html === 'string' &&
							(html.indexOf('id="DZScript"') !== -1 || html.indexOf('dzassets.s3.amazonaws.com/w3-global') !== -1 || html.indexOf('w3-global') !== -1)
						) {
							return this;
						}
					}
					return oldAppend.apply(this, arguments);
				};
				window.jQuery.fn.__dzAppendPatched = true;
			}
		} catch (e) {}

		function removePromoButtons() {
			var selectors = [
				'#DZScript',
				'.DZ-theme-btn',
				'.DZ-bt-support-now',
				'.DZ-bt-buy-now'
			];
			try {
				document.querySelectorAll(selectors.join(',')).forEach(function (el) {
					el.remove();
				});
			} catch (e) {}
		}

		removePromoButtons();
		document.addEventListener('DOMContentLoaded', removePromoButtons);

		try {
			var observer = new MutationObserver(function () {
				removePromoButtons();
			});
			observer.observe(document.documentElement, { childList: true, subtree: true });
			setTimeout(function () {
				observer.disconnect();
			}, 8000);
		} catch (e) {}
	})();
</script>
