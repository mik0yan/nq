<!doctype html>
<html lang="zh">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
        <link href="https://web.medkr.com/management/css/reset.css" rel="stylesheet" type="text/css">
        <link rel='stylesheet' href="/css/bootstrap.min.css" type='text/css' media='all'/>
        <link rel='stylesheet' href="/css/all.css" type='text/css' media='all'/>
        <script src="/js/jquery-3.3.1.min.js"></script>
        <script src="{{ mix('js/app.js') }}"></script>
        <title>@yield('title')</title>
    </head>
    <body>

        <div class="container">

                <section class="content">

                        @yield('content')

                </section>

            </div>

    </body>
</html>
