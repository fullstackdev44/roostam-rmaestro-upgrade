<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Maestro') }}</title>

    <!-- Styles -->

    <!-- Scripts -->
    <script>
        var constExecStates = <?php echo json_encode($constExecStates)?>;        
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" type="text/css" href="./css/sweetalert.min.css" />
    <link rel="stylesheet" type="text/css" href="./libs/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="./css/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="./libs/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="./css/styles.css">
    <link rel="stylesheet" type="text/css" href="./css/app.css">
    <link href='https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Material+Icons' rel="stylesheet">
    <link href="./css/quasar.mat.min.css" rel="stylesheet">

    <!-- Scripts -->
    <script type="text/javascript">       
        var animation_time = 1;
    </script>
    <script type="text/javascript" src="./libs/jquery/dist/jquery.min.js"></script>
    <script type="text/javascript" src="./js/jquery-ui.js"></script>
    <script src="./libs/bootstrap/dist/js/bootstrap.min.js"></script>

    <script type="text/javascript" src="./js/sweetalert.min.js"></script>
    <script type="text/javascript" src="./js/html2canvas.js"></script>
    <script type='text/javascript' src='./js/comment-reply.min.js'></script>
    <script src="./js/main.js"></script>
    <script src="./js/app.js"></script>
	<link rel="stylesheet" href="<?php echo asset('css/styles.css')?>" type="text/css"> 
    <link href="<?php echo asset('js/custom-scripts.js')?>" rel="script">
    <script src="./js/quasar.mat.umd.min.js"></script>

</head>
<body>
    <div id="app">
        <nav class="navbar navbar-default navbar-static-top">
            <div class="container">
                <div class="navbar-header">

                    <!-- Collapsed Hamburger -->
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                        <span class="sr-only">Toggle Navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>

                    <!-- Branding Image -->
                    <a class="navbar-brand" href="{{ url('/') }}">
                        {{ config('app.name', 'Maestro') }}
                    </a>
                </div>

                <div class="collapse navbar-collapse" id="app-navbar-collapse">
                    <!-- Left Side Of Navbar -->
                    <ul class="nav navbar-nav">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="nav navbar-nav navbar-right">
                        <!-- Authentication Links -->
                        @if (Auth::guest())
                            <li><a href="{{ url('/login') }}">Login</a></li>
                            <li><a href="{{ url('/register') }}">Register</a></li>
                        @else
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                    {{ Auth::user()->name }} <i class="fa fa-angle-double-down" aria-hidden="true"></i>
                                </a>

                                <ul class="dropdown-menu" role="menu">
                                    <li>
                                        <a href="{{ url('/logout') }}"
                                            onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                            Logout
                                        </a>

                                        <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </nav>
        
        <div id="main-view">
            @yield('content')
        </div>
        
    </div>
</body>
</html>
