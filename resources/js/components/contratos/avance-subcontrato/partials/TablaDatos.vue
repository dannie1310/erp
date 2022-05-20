<template>
    <div class="row" v-if="avance">
        <div class="col-md-12">
            <span><i class="fa fa-envelope"></i>Datos del Avance</span>
            <div class="table-responsive">
                <table class="table  table-sm">
                    <tr>
                        <th class="encabezado" colspan="6">
                            {{avance.razon_social}}
                        </th>
                    </tr>
                    <tr>
                        <th class="encabezado  c130">
                            Folio del Subcontrato
                        </th>
                        <th class="encabezado">
                            Subcontrato
                        </th>
                        <th class="encabezado c100">Subtotal</th>
                        <th class="encabezado c100">IVA</th>
                        <th class="encabezado c100">Total</th>
                    </tr>
                    <tr>
                        <td style="text-align: center">
                            <router-link :to="{name: 'subcontrato-show', params:{id : avance.subcontrato.id }}" target="_blank">
                                <span style="color:black; text-decoration: underline">{{avance.subcontrato.numero_folio_format}}</span>
                            </router-link>
                        </td>
                        <td style="text-align: center">
                            {{avance.subcontrato.referencia}}
                        </td>
                        <td class="money">
                            {{avance.subtotal_format}}
                        </td>
                        <td class="money">
                            {{avance.impuesto_format}}
                        </td>
                        <td class="money">
                            {{avance.total_format}}
                        </td>
                    </tr>
                    <tr>
                        <th class="encabezado" colspan="2">
                            Fechas de Reconocimiento de Avance
                        </th>
                        <th class="encabezado" colspan="2">
                            Periodo de Avance
                        </th>
                        <th class="encabezado center">
                            Nombre
                        </th>
                    </tr>
                    <tr>
                        <th class="encabezado c100">
                            Inicio
                        </th>
                        <th class="encabezado c100">
                            Término
                        </th>
                        <th class="encabezado c100">
                            Ejecución
                        </th>
                        <th class="encabezado c100">
                            Contable
                        </th>
                        <td class="center" rowspan="2">
                            {{avance.nombre_usuario}}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            {{avance.cumplimiento_format}}
                        </td>
                        <td>
                            {{avance.vencimiento_format}}
                        </td>
                        <td>
                            {{avance.fecha_ejecucion_format}}
                        </td>
                        <td>
                            {{avance.fecha_contable_format}}
                        </td>
                    </tr>
                    <tr>
                        <th class="encabezado" colspan="4">Observaciones</th>
                        <th class="encabezado">Estado</th>
                    </tr>
                    <tr>
                        <td colspan="4">
                            {{avance.observaciones}}
                        </td>
                        <td class="center">
                            <span class="badge" :style="{'background-color': avance.color_estado}">{{ avance.descripcion_estado }}</span>
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
    props: ['avance'],
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
