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
                        <th class="encabezado" colspan="2">
                            Empresa
                        </th>
                        <th class="encabezado c100">Cliente</th>
                        <th class="encabezado c100">Descripción</th>
                    </tr>
                    <tr>
                        <td style="text-align: center">
                            {{ factura.fecha_format }}
                        </td>
                        <td colspan="2">
                            {{ factura.nombre_empresa }}
                        </td>
                        <td>
                            {{ factura.nombre_cliente }}
                        </td>
                        <td>
                            {{ factura.descripcion}}
                        </td>
                    </tr>
                    <tr>
                        <th class="encabezado" colspan="2">
                            Periodo que cubre
                        </th>
                        <th class="encabezado">
                            Moneda
                        </th>
                        <th class="encabezado money">
                            Tipo de Cambio
                        </th>
                        <th class="encabezado">
                            Concepto
                        </th>
                    </tr>
                    <tr>
                        <td>
                            {{ factura.fecha_fi_format }}
                        </td>
                        <td>
                            {{ factura.fecha_ff_format }}
                        </td>
                        <td>
                            {{ factura.nombre_moneda }}
                        </td>
                        <td class="money">
                            {{ factura.tipo_cambio }}
                        </td>
                        <td>
                            {{ factura.concepto }}
                        </td>
                    </tr>
                    <tr>
                        <th class="encabezado">
                            Importe
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
                        <td class="money">
                            {{ factura.importe_format }}
                        </td>
                        <td class="money">

                        </td>
                        <td class="money">

                        </td>
                        <td class="money">

                        </td>
                        <td class="center">
                            <span class="badge" :style="{'background-color': factura.estado_color}">{{ factura.estado_descripcion }}</span>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="row">
                <div class="col-12">
                    <h6><b>Detalle de las partidas</b></h6>
                </div>
            </div>
            <div class="row">
                <div class="table-responsive col-md-12">
                    <table class="table table-sm">
                        <thead>
                        <tr>
                            <th class="encabezado">#</th>
                            <th class="encabezado">Clave</th>
                            <th class="encabezado">Concepto</th>
                            <th class="encabezado">Unidad</th>
                            <th class="encabezado">Cantidad Contratada</th>
                            <th class="encabezado">Precio Unitario</th>
                            <th class="encabezado">Cantidad</th>
                        </tr>
                        </thead>
                        <tbody v-for="(doc, i) in avance.partidas">
                        <tr v-if="doc.para_estimar != undefined">
                            <td>{{i}}</td>
                            <td>{{doc.clave}}</td>
                            <td><b>{{doc.descripcion}}</b></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr v-else>
                            <td>{{i}}</td>
                            <td>{{doc.clave}}</td>
                            <td>{{doc.descripcion_concepto}}</td>
                            <td>{{doc.unidad}}</td>
                            <td class="money">{{doc.cantidad_subcontrato_format}}</td>
                            <td class="money">{{doc.precio_unitario_subcontrato_format}}</td>
                            <td class="money">{{doc.cantidad_avance_format}}</td>
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
    name: "AvanceTablaDatos",
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
