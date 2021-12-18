<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="utf-8">
        <meta name="referrer" content="no-referrer"/>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <meta name="HandheldFriendly" content="true">
        <meta name="format-detection" content="telephone=no">

        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="apple-mobile-web-app-title" content="{{ config('app.name') }}">

        <link href="splashscreens/iphone5_splash.png" media="(device-width: 320px) and (device-height: 568px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image" />
        <link href="splashscreens/iphone6_splash.png" media="(device-width: 375px) and (device-height: 667px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image" />
        <link href="splashscreens/iphoneplus_splash.png" media="(device-width: 621px) and (device-height: 1104px) and (-webkit-device-pixel-ratio: 3)" rel="apple-touch-startup-image" />
        <link href="splashscreens/iphonex_splash.png" media="(device-width: 375px) and (device-height: 812px) and (-webkit-device-pixel-ratio: 3)" rel="apple-touch-startup-image" />
        <link href="splashscreens/iphonexr_splash.png" media="(device-width: 414px) and (device-height: 896px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image" />
        <link href="splashscreens/iphonexsmax_splash.png" media="(device-width: 414px) and (device-height: 896px) and (-webkit-device-pixel-ratio: 3)" rel="apple-touch-startup-image" />
        <link href="splashscreens/ipad_splash.png" media="(device-width: 768px) and (device-height: 1024px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image" />
        <link href="splashscreens/ipadpro1_splash.png" media="(device-width: 834px) and (device-height: 1112px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image" />
        <link href="splashscreens/ipadpro3_splash.png" media="(device-width: 834px) and (device-height: 1194px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image" />
        <link href="splashscreens/ipadpro2_splash.png" media="(device-width: 1024px) and (device-height: 1366px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image" />

        <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png?v=bOR3kovL2p">
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png?v=bOR3kovL2p">
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png?v=bOR3kovL2p">
        <link rel="manifest" href="/site.webmanifest?v=bOR3kovL2p">
        <link rel="mask-icon" href="/safari-pinned-tab.svg?v=bOR3kovL2p" color="#ffffff">
        <link rel="shortcut icon" href="/favicon.ico?v=bOR3kovL2p">
        <meta name="msapplication-TileColor" content="#00aba9">
        <meta name="theme-color" content="#ffffff">

        <meta property="og:title" content="{{ config('app.name') }} - @yield('title', 'комплекс серверов Minecraft')">
        <meta property="og:description" content="@yield('description', 'Самый лучший комплекс серверов Minecraft в СНГ.')">
        <meta property="og:url" content="{{ url()->full() }}">

        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'DreamCMS')</title>

        @if(config('app.s3frontend', false))
            <link href="/frontend/assets/css/style.css?v=2067" rel="stylesheet">
        @else
            <link href="{{ mix('assets/css/style.css') }}" rel="stylesheet">
        @endif

        @yield('css')
    </head>
    <body>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <div id="app"></div>

        @if(Request::header('cf-ipcountry') != 'UA')
            <script src="//vk.com/js/api/openapi.js?146"></script>
        @endif

        @if(config('app.s3frontend', false))
            <script src="/frontend/assets/js/unitpay.min.js"></script>
            <script src="/frontend/assets/js/app.js?v=2067"></script>
        @else
            <script src="{{ mix('/assets/js/unitpay.min.js' ) }}"></script>
            <script src="{{ mix('/assets/js/app.js' ) }}"></script>
        @endif

        @yield('scripts')

        @yield('js')
    </body>
</html>
