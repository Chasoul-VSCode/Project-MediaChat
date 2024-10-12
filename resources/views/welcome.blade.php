<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MediaChat</title>
    <script>
        window.onload = function() {
            window.location.href = "{{ route('login') }}";
        }
    </script>
</head>
<body>
    <!-- This page will automatically redirect to the login page -->
</body>
</html>
