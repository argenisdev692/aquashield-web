@if ($cookieConsentConfig['enabled'] && !$alreadyConsentedWithCookies)
    <div class="js-cookie-consent cookie-consent fixed bottom-0 inset-x-0 pb-2 z-50">
        <div class="max-w-7xl mx-auto px-6">
            <div class="p-4 rounded-lg bg-yellow-500 shadow-lg sm:p-6">
                <div class="flex items-center justify-between flex-wrap">
                    <div class="w-0 flex-1 flex items-center mb-4 sm:mb-0">
                        <p class="ml-3 text-white cookie-consent__message">
                            {!! trans('cookie-consent::texts.message') !!}
                        </p>
                    </div>
                    <div class="flex-shrink-0 w-full sm:w-auto ml-5">
                        <div class="flex space-x-4">
                            <button
                                class="js-cookie-consent-agree cookie-consent__agree cursor-pointer flex items-center justify-center px-4 py-2 rounded-md text-sm font-medium text-yellow-500 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-yellow-500 focus:ring-white">
                                {{ trans('cookie-consent::texts.agree') }}
                            </button>
                            <a href="{{ route('cookies-policy') }}"
                                class="flex items-center justify-center px-4 py-2 rounded-md text-sm font-medium text-white hover:text-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-yellow-500 focus:ring-white">
                                {{ trans('cookie-consent::texts.link') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.laravelCookieConsent = (function() {
            const COOKIE_VALUE = 1;
            const COOKIE_DOMAIN = '{{ config('session.domain') ?? request()->getHost() }}';

            function consentWithCookies() {
                setCookie('{{ $cookieConsentConfig['cookie_name'] }}', COOKIE_VALUE,
                    {{ $cookieConsentConfig['cookie_lifetime'] }});
                hideCookieDialog();
            }

            function cookieExists(name) {
                return (document.cookie.split('; ').indexOf(name + '=' + COOKIE_VALUE) !== -1);
            }

            function hideCookieDialog() {
                const dialogs = document.getElementsByClassName('js-cookie-consent');
                for (let i = 0; i < dialogs.length; ++i) {
                    dialogs[i].style.display = 'none';
                }
            }

            function setCookie(name, value, expirationInDays) {
                const date = new Date();
                date.setTime(date.getTime() + (expirationInDays * 24 * 60 * 60 * 1000));
                document.cookie = name + '=' + value +
                    ';expires=' + date.toUTCString() +
                    ';domain=' + COOKIE_DOMAIN +
                    ';path=/{{ config('session.secure') ? ';secure' : null }}' +
                    '{{ config('session.same_site') ? ';samesite=' . config('session.same_site') : null }}';
            }

            if (cookieExists('{{ $cookieConsentConfig['cookie_name'] }}')) {
                hideCookieDialog();
            }

            const buttons = document.getElementsByClassName('js-cookie-consent-agree');
            for (let i = 0; i < buttons.length; ++i) {
                buttons[i].addEventListener('click', consentWithCookies);
            }

            return {
                consentWithCookies: consentWithCookies,
                hideCookieDialog: hideCookieDialog
            };
        })();
    </script>
@endif
