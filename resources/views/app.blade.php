<!DOCTYPE html>
<html class='h-full' lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    
        <link rel='icon' href='/logo.png'>

        <link rel="manifest" href="/manifest.json">

        <title>{{ env('APP_NAME') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />    
        
        @vite('resources/styles/main.css')
    </head>
    <body class='h-full'>
        <div id='app' class='bg-zinc-900 h-full'></div>
        @vite('resources/js/app.js')
    </body>
</html>
