<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Weather Monitor</title>
    </head>
    <body>
        <div id="app" class="h-screen overflow-hidden flex flex-col"></div>
        @vite(['resources/js/app.ts'])
    </body>
</html>
