<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Intact Balance</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet"/>

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <style>
            body {
                font-family: var(--font-sans);
            }

            .hero-image {
                width: 100%;
                height: auto;
                border-radius: 0.5rem;
            }

            .feature-icon {
                width: 48px;
                height: 48px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 0.5rem;
                margin-bottom: 1rem;
            }
        </style>
    @endif
</head>
<body class="bg-green-50">
<div class="min-h-screen flex flex-col">
    <!-- Header -->
    <header class="bg-white border-b border-gray-200">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-semibold text-green-700">{{ __('welcome.header_title') }}</h1>
                <!-- Mobile menu button -->
                <button class="md:hidden" onclick="toggleMobileMenu()">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <!-- Desktop navigation -->
                <div class="hidden md:flex gap-4">
                    @if(\Illuminate\Support\Facades\Auth::user())
                        <a href="{{route('filament.admin.resources.products.index')}}"
                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                            {{ __('welcome.panel') }}
                        </a>
                    @else
                        <a href="{{route('filament.auth.auth.register')}}"
                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">{{ __('welcome.register_button') }}</a>

                        <a href="{{route('filament.auth.auth.login')}}"
                           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">{{ __('welcome.login_button') }}</a>

                    @endif

                </div>
            </div>
        </div>
    </header>

    <!-- Mobile menu -->
    <div
        class="fixed inset-x-0 top-16 bg-white shadow-lg p-4 transform -translate-x-full transition-transform duration-300 ease-in-out md:hidden"
        id="mobile-menu">
        <div class="container mx-auto px-4">
            <div class="flex flex-col gap-4">
                <a href="#"
                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">{{ __('welcome.register_button') }}</a>
                <a href="#"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">{{ __('welcome.login_button') }}</a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1">
        <div class="container mx-auto px-4 py-8">
            <!-- Welcome Section -->
            <div class="mb-8">
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="p-8">
                        <div class="flex flex-col items-center text-center">
                            <h1 class="text-4xl font-bold text-emerald-700 mb-4">{{ __('welcome.welcome_title') }}</h1>
                            <p class="text-xl text-gray-600 mb-8">{{ __('welcome.welcome_description') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Why Join Us Section -->
            <div class="mb-8">
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="p-6">
                        <h2 class="text-3xl font-bold text-center text-emerald-700 mb-8">{{ __('welcome.why_join_title') }}</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <!-- Card 1 -->
                            <div class="bg-white rounded-lg overflow-hidden transform transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                                <div class="p-6 flex justify-between items-start">
                                    <div>
                                        <h3 class="text-xl font-semibold text-emerald-700 mb-2">{{ __('welcome.card_1_title') }}</h3>
                                        <p class="text-gray-600">{{ __('welcome.card_1_description') }}</p>
                                    </div>
                                    <div class="feature-icon">
                                        <svg class="w-9 h-9 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Card 2 -->
                            <div class="bg-white rounded-lg overflow-hidden transform transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                                <div class="p-6 flex justify-between items-start">
                                    <div>
                                        <h3 class="text-xl font-semibold text-amber-700 mb-2">{{ __('welcome.card_2_title') }}</h3>
                                        <p class="text-gray-600">{{ __('welcome.card_2_description') }}</p>
                                    </div>
                                    <div class="feature-icon">
                                        <svg class="w-9 h-9 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Card 3 -->
                            <div class="bg-white rounded-lg overflow-hidden transform transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                                <div class="p-6 flex justify-between items-start">
                                    <div>
                                        <h3 class="text-xl font-semibold text-blue-700 mb-2">{{ __('welcome.card_3_title') }}</h3>
                                        <p class="text-gray-600">{{ __('welcome.card_3_description') }}</p>
                                    </div>
                                    <div class="feature-icon">
                                        <svg class="w-9 h-9 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cooperation Process Section -->
            <div class="mb-8">
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="p-6">
                        <h2 class="text-3xl font-bold text-center text-emerald-700 mb-8">{{ __('welcome.cooperation_title') }}</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <!-- Card 1 -->
                            <div class="bg-white rounded-lg overflow-hidden transform transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                                <div class="p-6 flex justify-between items-start">
                                    <div>
                                        <h3 class="text-xl font-semibold text-orange-700 mb-2">{{ __('welcome.cooperation_card_1_title') }}</h3>
                                        <p class="text-gray-600">{{ __('welcome.cooperation_card_1_description') }}</p>
                                    </div>
                                    <div class="feature-icon">
                                        <svg class="w-9 h-9 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Card 2 -->
                            <div class="bg-white rounded-lg overflow-hidden transform transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                                <div class="p-6 flex justify-between items-start">
                                    <div>
                                        <h3 class="text-xl font-semibold text-red-700 mb-2">{{ __('welcome.cooperation_card_2_title') }}</h3>
                                        <p class="text-gray-600">{{ __('welcome.cooperation_card_2_description') }}</p>
                                    </div>
                                    <div class="feature-icon">
                                        <svg class="w-9 h-9 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Card 3 -->
                            <div class="bg-white rounded-lg overflow-hidden transform transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                                <div class="p-6 flex justify-between items-start">
                                    <div>
                                        <h3 class="text-xl font-semibold text-teal-700 mb-2">{{ __('welcome.cooperation_card_3_title') }}</h3>
                                        <p class="text-gray-600">{{ __('welcome.cooperation_card_3_description') }}</p>
                                    </div>
                                    <div class="feature-icon">
                                        <svg class="w-9 h-9 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products Section -->
            <div class="mt-8">
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="p-6">
                        <h2 class="text-3xl font-bold text-center text-emerald-700 mb-8">{{ __('welcome.products_title') }}</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                                <div class="p-4 border-b border-gray-200">
                                    <h3 class="text-base font-medium text-gray-900">{{ __('welcome.product_nuts') }}</h3>
                                </div>
                                <div class="p-4">
                                    <p class="text-gray-600">{{ __('welcome.product_nuts_description') }}</p>
                                </div>
                            </div>
                            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                                <div class="p-4 border-b border-gray-200">
                                    <h3 class="text-base font-medium text-gray-900">{{ __('welcome.product_flours') }}</h3>
                                </div>
                                <div class="p-4">
                                    <p class="text-gray-600">{{ __('welcome.product_flours_description') }}</p>
                                </div>
                            </div>
                            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                                <div class="p-4 border-b border-gray-200">
                                    <h3 class="text-base font-medium text-gray-900">{{ __('welcome.product_sweeteners') }}</h3>
                                </div>
                                <div class="p-4">
                                    <p class="text-gray-600">{{ __('welcome.product_sweeteners_description') }}</p>
                                </div>
                            </div>
                            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                                <div class="p-4 border-b border-gray-200">
                                    <h3 class="text-base font-medium text-gray-900">{{ __('welcome.product_legumes') }}</h3>
                                </div>
                                <div class="p-4">
                                    <p class="text-gray-600">{{ __('welcome.product_legumes_description') }}</p>
                                </div>
                            </div>
                            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                                <div class="p-4 border-b border-gray-200">
                                    <h3 class="text-base font-medium text-gray-900">{{ __('welcome.product_superfoods') }}</h3>
                                </div>
                                <div class="p-4">
                                    <p class="text-gray-600">{{ __('welcome.product_superfoods_description') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-50">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-end gap-2">
                <div class="w-64">
                    <h3 class="text-sm font-semibold text-emerald-700 mb-2">{{ __('welcome.footer_title') }}</h3>
                    <p class="text-xs text-gray-600">{{ __('welcome.footer_address') }}</p>
                    <p class="text-xs text-gray-600">{{ __('welcome.footer_phone') }}</p>
                    <p class="text-xs text-gray-600">{{ __('welcome.footer_email') }}</p>
                </div>
                <div class="w-64">
                    <h3 class="text-sm font-semibold text-emerald-700 mb-2">{{ __('welcome.footer_registration_title') }}</h3>
                    <p class="text-xs text-gray-600">{{ __('welcome.footer_krs') }}</p>
                    <p class="text-xs text-gray-600">{{ __('welcome.footer_regon') }}</p>
                    <p class="text-xs text-gray-600">{{ __('welcome.footer_nip') }}</p>
                </div>
            </div>
            <div class="border-t border-gray-200 mt-4 pt-4 text-center text-xs text-gray-600">
                <p>&copy; {{ date('Y') }} {{ __('welcome.footer_copyright') }}.</p>
            </div>
        </div>
    </footer>
</div>

<script>
    function toggleMobileMenu() {
        const menu = document.getElementById('mobile-menu');
        menu.classList.toggle('translate-x-0');
    }
</script>
</body>
</html>
