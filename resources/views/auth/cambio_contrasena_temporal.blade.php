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
    <script>
        function validate(){
            if(!document.getElementById("clave_nueva").value==document.getElementById("clave_confirmacion").value)alert("Passwords do no match");
            return document.getElementById("clave_nueva").value==document.getElementById("clave_confirmacion").value;
            return false;
        }
    </script>
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
                <p class="login-box-msg">Actualización de Contraseña</p>
                <form method="POST" onsubmit="return validate()" action="{{ route('login') }}">
                {{ csrf_field() }}
                    <div class="input-group mb-3">
                        <input type="password" name="clave_nueva" class="form-control{{ $errors->has('clave_nueva') ? ' is-invalid' : '' }}" placeholder="Contraseña Nueva" value="{{ old('clave_nueva') }}" required autofocus>
                        <div class="input-group-append">
                            <span class="fas fa-lock input-group-text"></span>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" name="clave_confirmacion" class="form-control{{ $errors->has('clave_confirmacion') ? ' is-invalid' : '' }}" placeholder="Confirmar Contraseña" required>
                        <div class="input-group-append">
                            <span class="fa fa-lock input-group-text"></span>
                        </div>
                        @if ($errors->has('clave_confirmacion'))
                            <span class="invalid-feedback" role="alert">panda
                                {{ $errors->first('clave_confirmacion') }}
                            </span>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-block btn-flat">Actualizar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>