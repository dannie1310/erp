<!DOCTYPE html>
<html lang="es">
<head>
    <link href="/img/company-icon.png" rel="shortcut icon" type="image/x-icon">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SAO</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="{{ mix('css/app.css') }}" rel="stylesheet"/>
</head>
<div id="app">
    <main-app sidebar="{{ $sidebar }}" logo="{{ $logo }}" />
</div>
<script src="{{ mix('js/app.js') }}"></script>
</html>
<script src="https://kit.fontawesome.com/4a7d805650.js" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/ui/1.11.3/jquery-ui.min.js"></script>
