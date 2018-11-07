
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Cover Template for Bootstrap</title>

    <link rel="stylesheet" href="{{asset('css/app.css')}}">
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
</head>

<body class="text-center">

<div class="cover-container d-flex w-100 h-100 flex-column" id="app">
    <header class="masthead mb-auto">
        <div class="inner">
            <h3 class="masthead-brand">Cover</h3>
        </div>
    </header>

    @yield('content')

    <footer class="mastfoot mt-auto">
        <div class="inner">
            <p>Just a footer 👀</p>
        </div>
    </footer>
</div>
<script src="{{asset('js/app.js')}}"></script>
<script src="{{asset('js/jquery.appear.js')}}"></script>
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
</script>
</body>
</html>
