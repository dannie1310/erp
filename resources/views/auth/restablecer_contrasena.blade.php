<!DOCTYPE html>
<html lang="es">
<head>
    <link href="/img/company-icon.png" rel="shortcut icon" type="image/x-icon">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>SAO</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="{{ mix('css/app.css') }}" rel="stylesheet"/>
    <style>
        #content {
            width: 100%; height: 100%;
            background-color: #d2d6de;
            position: absolute; top: 0; left: 0;
        }
    </style>
    <script src="{{ mix('js/app.js') }}"></script>
    <script src="https://kit.fontawesome.com/4a7d805650.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/ui/1.11.3/jquery-ui.min.js"></script>
</head>
<body>
<div id="content">
    <div class="row">
        <div class="login-logo offset-4 centered">
            <img src="{{URL::asset('/img/logo_hc.png')}}" class="img-responsive img-rounded" width="70%">
        </div>
    </div>
    <diV class="login-box offset-4 centered">

        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg">Restablecer Contraseña</p>

                <form method="POST" action="{{ route('login') }}">
                    {{ csrf_field() }}

                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>
