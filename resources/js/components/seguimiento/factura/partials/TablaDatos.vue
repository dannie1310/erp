<template>
    <div class="row" v-if="factura">
        <div class="col-md-12">
            <span><i class="fa fa-envelope"></i>Datos del Factura</span>
            <div class="table-responsive">
                <table class="table  table-sm">
                    <tr>
                        <th class="encabezado" colspan="6">
                            {{factura.nombre_proyecto}}
                        </th>
                    </tr>
                    <tr>
                        <th class="encabezado  c130">
                            Fecha de emisión
                        </th>
                        <th class="encabezado">
                            Número de Factura
                        </th>
                        <th class="encabezado" colspan="3">
                            Empresa
                        </th>
                        <th class="encabezado c100">Descripción</th>
                    </tr>
                    <tr>
                        <td style="text-align: center">
                            {{ factura.fecha_format }}
                        </td>
                        <td>
                            {{factura.numero}}
                        </td>
                        <td colspan="3">
                            {{ factura.nombre_empresa }}
                        </td>
                        <td>
                            {{ factura.descripcion }}
                        </td>
                    </tr>
                    <tr>
                        <th class="encabezado c100" colspan="3">
                            Cliente
                        </th>
                        <th class="encabezado" colspan="2">
                            Periodo que cubre
                        </th>
                        <th class="encabezado">
                            Moneda
                        </th>
                    </tr>
                    <tr>
                        <td colspan="3">
                            {{ factura.nombre_cliente}}
                        </td>
                        <td style="text-align: center">
                            {{ factura.fecha_fi_format }}
                        </td>
                        <td style="text-align: center">
                            {{ factura.fecha_ff_format }}
                        </td>
                        <td>
                            {{ factura.nombre_moneda }}
                        </td>

                    </tr>
                    <tr>
                        <th class="encabezado c150">
                            UUID
                        </th>
                        <th class="encabezado money">
                            Tipo de Cambio
                        </th>
                        <th class="encabezado">
                            Subtotal
                        </th>
                        <th class="encabezado">
                            IVA
                        </th>
                        <th class="encabezado">
                            Total
                        </th>
                        <th class="encabezado">
                            Estado
                        </th>
                    </tr>
                    <tr>
                        <td style="text-align: right" class="c150">
                            {{factura.uuid}}
                        </td>
                        <td class="money">
                            {{ factura.tipo_cambio }}
                        </td>
                        <td class="money">
                            {{factura.subtotal_format}}
                        </td>
                        <td class="money">
                            {{factura.iva_format}}
                        </td>
                        <td class="money">
                            {{factura.total_format}}
                        </td>
                        <td class="center">
                            <span class="badge" :style="{'background-color': factura.estado_color}">{{ factura.estado_descripcion }}</span>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="row">
                <div class="col-12">
                    <h6><b>Detalle de los conceptos</b></h6>
                </div>
            </div>
            <div class="row">
                <div class="table-responsive col-md-12">
                    <table class="table table-sm">
                        <thead>
                        <tr>
                            <th class="encabezado">#</th>
                            <th class="encabezado">Tipo Ingreso</th>
                            <th class="encabezado">Importe</th>
                        </tr>
                        </thead>
                        <tbody v-for="(doc, i) in factura.conceptos.data">
                            <tr>
                                <td>{{i+1}}</td>
                                <td>{{doc.tipoIngreso.nombre}}</td>
                                <td style="text-align: right"><b>{{doc.importe_format}}</b></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row" v-if="factura.partidas.data.length != 0">
                <div class="col-12">
                    <h6><b>Detalle de las partidas</b></h6>
                </div>
            </div>
            <div class="row" v-if="factura.partidas.data.length != 0">
                <div class="table-responsive col-md-12">
                    <table class="table table-sm">
                        <thead>
                        <tr>
                            <th class="encabezado">#</th>
                            <th class="encabezado">Partida</th>
                            <th class="encabezado">Operador</th>
                            <th class="encabezado"></th>
                            <th class="encabezado">Total</th>
                        </tr>
                        </thead>
                        <tbody v-for="(doc, i) in factura.partidas.data">
                        <tr>
                            <td>{{i+1}}</td>
                            <td>{{doc.partida.partida}}</td>
                            <td>{{doc.partida.nombre_operador}}</td>
                            <td v-if="doc.antes_iva == 1">Antes de IVA</td>
                            <td v-else>Despues de IVA</td>
                            <td style="text-align: right"><b>{{doc.total_format}}</b></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "FacturaTablaDatos",
    components: { },
    props: ['factura'],
    methods :{

    }
}
</script>

<style scoped>
.encabezado{
    text-align: center; background-color: #f2f4f5
}
td, th{
    border: 1px #dee2e6 solid;
}

</style>
