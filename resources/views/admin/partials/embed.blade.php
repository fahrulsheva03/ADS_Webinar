<!DOCTYPE html>
<html lang="en">
@include('admin.partials.header')
<body class="bg-white">
    <div class="container-fluid py-3">
        @yield('content')
    </div>

    @include('admin.partials.script')
    <script>
        (function () {
            const embedParams = new URLSearchParams(window.location.search);
            const embedOn = embedParams.get('embed') === '1' || embedParams.get('embed') === 'true';

            function withEmbed(urlValue) {
                const url = new URL(urlValue || window.location.href, window.location.href);
                url.searchParams.set('embed', '1');
                return url.toString();
            }

            function ensureEmbedForm(form) {
                if (!embedOn || !form) return;

                const action = form.getAttribute('action');
                if (action !== null && action !== '') {
                    form.setAttribute('action', withEmbed(action));
                } else if (action === '') {
                    form.setAttribute('action', withEmbed(window.location.href));
                }

                let input = form.querySelector('input[name="embed"]');
                if (!input) {
                    input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'embed';
                    input.value = '1';
                    form.appendChild(input);
                } else {
                    input.value = '1';
                }
            }

            function ensureEmbedLink(a) {
                if (!embedOn || !a) return;
                if (a.target && a.target !== '_self') return;
                if (a.hasAttribute('download')) return;

                const rawHref = a.getAttribute('href');
                if (!rawHref || rawHref.startsWith('#') || rawHref.startsWith('javascript:')) return;

                let url;
                try {
                    url = new URL(rawHref, window.location.href);
                } catch (e) {
                    return;
                }

                if (url.origin !== window.location.origin) return;
                if (!url.pathname.startsWith('/admin')) return;
                if (url.searchParams.get('embed')) return;

                url.searchParams.set('embed', '1');
                a.href = url.toString();
            }

            if (embedOn) {
                Array.from(document.querySelectorAll('form')).forEach((form) => ensureEmbedForm(form));
                Array.from(document.querySelectorAll('a[href]')).forEach((a) => ensureEmbedLink(a));

                document.addEventListener(
                    'submit',
                    (e) => {
                        const form = e.target;
                        if (form && form.tagName === 'FORM') {
                            ensureEmbedForm(form);
                        }
                    },
                    true
                );
            }

            function postHeight() {
                const height = Math.max(
                    document.documentElement?.scrollHeight || 0,
                    document.body?.scrollHeight || 0
                );
                try {
                    window.parent?.postMessage({ type: 'embed-resize', height }, window.location.origin);
                } catch (e) {
                }
            }

            postHeight();

            if (window.ResizeObserver) {
                const ro = new ResizeObserver(() => postHeight());
                if (document.body) {
                    ro.observe(document.body);
                }
                ro.observe(document.documentElement);
            } else {
                window.addEventListener('resize', () => postHeight());
                window.setInterval(() => postHeight(), 800);
            }

            window.addEventListener('load', () => postHeight());
        })();
    </script>
</body>
</html>
