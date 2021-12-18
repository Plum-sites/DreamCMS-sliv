<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>DreamCMS | Admin</title>
    <link rel="stylesheet" href="/assets/vuexy/css/core.css?v={{ \Illuminate\Support\Str::random() }}">
</head>
<body>
<noscript>
    <strong>We're sorry but Vuexy doesn't work properly without JavaScript enabled. Please enable it to continue.</strong>
</noscript>
<div id="app">
</div>
<script src="/assets/vuexy/js/main.js?v={{ \Illuminate\Support\Str::random() }}"></script>
</body>
</html>
