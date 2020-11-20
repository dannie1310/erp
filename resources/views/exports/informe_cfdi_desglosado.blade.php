<table>
    <thead >
    <tr style="background-color: #aaaaaa">
        <th>#</th>
        <th>Estatus</th>
        <th>RFC</th>
        <th>Razón Social</th>
        <th>Fecha Presunto </th>
        <th>Fecha Presunto DOF</th>
        <th>Fecha Definitivo </th>
        <th>Fecha Definitivo DOF</th>
        <th>Fecha Corrección</th>
        <th>Empresa</th>
        <th># CFD</th>
        <th>Importe Incluyendo IVA</th>
    </tr>
    <tr style="background-color: #aaaaaa">
        <th>#</th>
        <th colspan="3">UUID</th>
        <th>Fecha</th>
        <th>Serie</th>
        <th>Folio</th>
        <th>Moneda</th>
        <th>TC </th>
        <th>IVA</th>
        <th>Subtotal</th>
        <th>Total</th>
    </tr>
    </thead>
    <tbody>
    @foreach($informe["informe"] as $tipo)
        @foreach($tipo as $partida)
            @if($partida["tipo"] == "titulo")
                <tr style="background-color: #cccccc">
                    <td colspan="12">{{ $partida["etiqueta"] }}</td>
                </tr>
            @endif
            @if($partida["tipo"] == "partida")
            <tr style="background-color: #cccccc">
                <td>{{ $partida["indice"] }}</td>
                <td>{{$partida["estatus"]}}</td>
                <td>{{$partida["rfc"]}}</td>
                <td>{{$partida["razon_social"]}}</td>
                <td>{{$partida["fecha_presunto"]}}</td>
                <td>{{$partida["fecha_presunto_dof"]}}</td>
                <td>{{$partida["fecha_definitivo"]}}</td>
                <td>{{$partida["fecha_definitivo_dof"]}}</td>
                <td></td>
                <td>{{$partida["empresa"]}}</td>
                <td style="text-align:right">{{$partida["no_CFDI"]}}</td>
                <td style="text-align:right">{{$partida["importe_format"]}}</td>
            </tr>
            @endif
            @if($partida["tipo"] == "uuid")
                <tr>
                    <td>{{ $partida["indice"] }}</td>
                    <td colspan="3">{{ $partida["uuid"] }}</td>
                    <td>{{$partida["fecha"]}}</td>
                    <td>{{$partida["serie"]}}</td>
                    <td>{{$partida["folio"]}}</td>
                    <td>{{$partida["moneda"]}}</td>
                    <td>{{$partida["tipo_cambio"]}}</td>
                    <td style="text-align:right">{{$partida["importe_iva"]}}</td>
                    <td style="text-align:right">{{$partida["subtotal"]}}</td>
                    <td style="text-align:right">{{$partida["total"]}}</td>
                </tr>
            @endif
        @endforeach
    @endforeach
    </tbody>
</table>
