<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Login') - Sistem EWS</title>
    @vite('resources/css/app.css')
</head>

<body class="ews-app">
    <div class="ews-auth">
        @yield('content')
    </div>
</body>

</html>
