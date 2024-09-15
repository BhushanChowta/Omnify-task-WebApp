<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'My Application')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}"> <!-- Link to your CSS file -->
    @stack('styles') <!-- Stack for additional styles -->
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="{{ route('discounts.index') }}">Home</a></li>
                <li><a href="{{ route('discounts.create') }}">Create Discount</a></li>
                <!-- Add more navigation links as needed -->
            </ul>
        </nav>
    </header>

    <main>
        @yield('content') <!-- The main content of the page will be injected here -->
    </main>

    <footer>
        <p>&copy; {{ date('Y') }} Omnify Task.</p>
    </footer>

    <script src="{{ asset('js/app.js') }}"></script> <!-- Link to your JavaScript file -->
    @stack('scripts') <!-- Stack for additional scripts -->
</body>
</html>
