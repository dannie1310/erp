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
                <p class="login-box-msg">Iniciar Sesión</p>

                <form method="POST" action="{{ route('login') }}">
                    {{ csrf_field() }}
                    <div class="input-group mb-3">
                        <input type="text" name="usuario" class="form-control{{ $errors->has('usuario') ? ' is-invalid' : '' }}" placeholder="Usuario" value="{{ old('usuario') }}" required autofocus>
                        <div class="input-group-append">
                            <span class="fas fa-user input-group-text"></span>
                        </div>
                        @if ($errors->has('usuario'))
                            <span class="invalid-feedback" role="alert">
                                {{ $errors->first('usuario') }}
                            </span>
                        @endif
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" name="clave" class="form-control{{ $errors->has('clave') ? ' is-invalid' : '' }}" placeholder="Contraseña" required>
                        <div class="input-group-append">
                            <span class="fa fa-lock input-group-text"></span>
                        </div>
                        @if ($errors->has('clave'))
                            <span class="invalid-feedback" role="alert">
                                {{ $errors->first('clave') }}
                            </span>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-block btn-flat">Entrar</button>
                        </div>
                    </div>
                </form>
                <form method="POST" action="{{ route('login') }}">
                    <div class="row">
                        {{ csrf_field() }}
                        <div class="col-md-12">
                            <button type="submit"  class="btn btn-link float-right">¿Olvidaste tu contraseña?
                                <input type="text" hidden="true" name="restablecer_contrasena" value="true">
                            </button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>
