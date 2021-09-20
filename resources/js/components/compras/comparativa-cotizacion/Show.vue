<template>
    <span>
        <div class="card" v-if="cargando">
            <div class="card-body">
                <div class="row" >
                    <div class="col-md-12">
                        <div class="spinner-border text-success" role="status">
                           <span class="sr-only">Cargando...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card" v-else>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <encabezado-solicitud-compra v-bind:solicitud_compra="solicitud"></encabezado-solicitud-compra>

                    </div>

                </div>
                <div class="row">
                    <div class="col-md-12 table-responsive">
                        <table class="table table-sm ">
                            <thead>
                            <tr  >
                                <th rowspan="2" class="index_corto">
                                    #
                                </th>
                                <th rowspan="2">
                                    Descripción
                                </th>
                                <th class="c70" rowspan="2">
                                    Unidad
                                </th>
                                <th class="c70" rowspan="2">
                                    Cantidad
                                </th>
                                <template v-for = "(cotizacion, c) in cotizaciones" >
                                    <th class="c300 no_negrita" colspan="4">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <b>{{cotizacion.empresa}}</b>
                                            </div>
                                        </div>
                                        <hr style="margin: 1px">
                                        <div class="row">
                                            <div class="col-md-3">
                                                Fecha:
                                            </div>
                                            <div class="col-md-3">
                                                <b>{{cotizacion.fecha}}</b>
                                            </div>
                                            <div class="col-md-3">
                                                Fecha de Envío:
                                            </div>
                                            <div class="col-md-3">
                                                <b>{{cotizacion.fecha_envio}}</b>
                                            </div>
                                        </div>
                                        <hr style="margin: 1px">
                                        <div class="row">
                                            <div class="col-md-3">
                                                Anticipo
                                            </div>
                                            <div class="col-md-3">
                                                Crédito
                                            </div>
                                            <div class="col-md-3">
                                                Plazo de Entrega
                                            </div>
                                            <div class="col-md-3">
                                                Vigencia
                                            </div>
                                        </div>
                                        <hr style="margin: 1px">
                                         <div class="row">
                                            <div class="col-md-3">
                                                <b>{{cotizacion.anticipo}}</b>
                                            </div>
                                            <div class="col-md-3">
                                                <b>{{cotizacion.dias_credito}}</b>
                                            </div>
                                            <div class="col-md-3">
                                                <b>{{cotizacion.plazo_entrega}}</b>
                                            </div>
                                            <div class="col-md-3">
                                                <b>{{cotizacion.vigencia}}</b>
                                            </div>
                                        </div>
                                    </th>

                                </template>

                            </tr>
                            <tr>
                                <template v-for = "(cotizacion, c) in cotizaciones" >
                                    <th>
                                        Precio Unitario
                                    </th>
                                    <th>
                                        Descuento
                                    </th>
                                    <th >
                                        Moneda
                                    </th>
                                    <th >
                                        Importe Pesos (MXN)
                                    </th>
                                </template>
                            </tr>
                            </thead>
                            <tbody>

                            <tr  v-for="(partida, i) in partidas">
                                <td>
                                    {{partida.indice}}
                                </td>
                                <td>
                                    {{partida.material}} {{partida.indice}}
                                </td>
                                <td>
                                    {{partida.unidad}}
                                </td>
                                <td style="text-align: right">
                                    {{partida.cantidad}}
                                </td>
                                <template v-for = "(cotizacion, c) in cotizaciones" >
                                    <td style="text-align: right ;"  :style="partida.cotizaciones[c] && partida.cotizaciones[c].precio_con_descuento == precios_menores[i]?`background-color : #f2f4f5`:``">
                                        <span v-if="partida.cotizaciones[c]">
                                            ${{partida.cotizaciones[c].precio_unitario.formatMoney(2,".",",")}}
                                        </span>
                                    </td>
                                    <td style="text-align: right;" :style="partida.cotizaciones[c] && partida.cotizaciones[c].precio_con_descuento == precios_menores[i]?`background-color : #f2f4f5`:``">
                                        <span v-if="partida.cotizaciones[c]">
                                            {{partida.cotizaciones[c].descuento_partida_format}}
                                        </span>
                                    </td>
                                    <td :style="partida.cotizaciones[c] && partida.cotizaciones[c].precio_con_descuento == precios_menores[i]?`background-color : #f2f4f5`:``">
                                        <span v-if="partida.cotizaciones[c]">
                                            {{partida.cotizaciones[c].moneda}}
                                        </span>
                                    </td>
                                    <td style="text-align: right" :style="partida.cotizaciones[c] && partida.cotizaciones[c].precio_con_descuento == precios_menores[i]?`background-color : #f2f4f5`:``">
                                        <span v-if="partida.cotizaciones[c]">
                                            ${{partida.cotizaciones[c].precio_total_moneda.formatMoney(2,".",",")}}
                                        </span>
                                    </td>
                                </template>
                            </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" style="border: none"></td>

                                    <template v-for = "(cotizacion, c) in cotizaciones" >
                                        <td colspan="3" style="text-align: right;border:none">Subtotal Pesos MXN:</td>
                                        <td style="text-align: right">${{cotizacion.suma_subtotal_partidas.formatMoney(2,".",",")}}</td>
                                    </template>

                                </tr>
                                <tr>
                                    <td colspan="4" style="border: none"></td>

                                    <template v-for = "(cotizacion, c) in cotizaciones" >
                                        <td colspan="3" style="text-align: right;border:none">Descuento Global:</td>
                                        <td style="text-align: right">{{cotizacion.descuento_global}}</td>
                                    </template>

                                </tr>
                                <tr>
                                    <td colspan="4" style="border: none"></td>
                                    <template v-for = "(cotizacion, c) in cotizaciones" >
                                        <td colspan="3" style="text-align: right;border:none">Subtotal Pesos MXN:</td>
                                        <td style="text-align: right">${{cotizacion.subtotal_con_descuento.formatMoney(2,".",",")}}</td>
                                    </template>
                                </tr>
                                <tr>
                                    <td colspan="4" style="border: none"></td>
                                    <template v-for = "(cotizacion, c) in cotizaciones" >
                                        <td colspan="3" style="text-align: right;border:none">IVA:</td>
                                        <td style="text-align: right">${{cotizacion.iva.formatMoney(2,".",",")}}</td>
                                    </template>
                                </tr>
                                <tr>
                                    <td colspan="4" style="border: none"></td>
                                    <template v-for = "(cotizacion, c) in cotizaciones" >
                                        <td colspan="3" style="text-align: right;border:none">Total:</td>
                                        <td style="text-align: right"><b>${{cotizacion.total.formatMoney(2,".",",")}}</b></td>
                                    </template>
                                </tr>
                                <tr>
                                    <td colspan="4" style="border: none"></td>
                                    <template v-for = "(cotizacion, c) in cotizaciones" >
                                        <td colspan="4" style="text-align: center;border: none">&nbsp;</td>
                                    </template>
                                </tr>
                                <tr>
                                    <td colspan="4" style="border: none"></td>
                                    <template v-for = "(cotizacion, c) in cotizaciones" >
                                        <td colspan="4" style="text-align: center;" class="encabezado"><b>Observaciones</b></td>
                                    </template>
                                </tr>
                                <tr>
                                    <td colspan="4" style="border: none"></td>
                                    <template v-for = "(cotizacion, c) in cotizaciones" >
                                        <td colspan="4" >{{ cotizacion.observaciones }}</td>
                                    </template>
                                </tr>
                                <tr>
                                    <td :colspan="3 + (cantidad_cotizaciones * 4)" style="border: none">&nbsp;</td>
                                </tr>
                                <template v-if="exclusiones.cantidad > 0">
                                    <tr>
                                        <td colspan="4" style="border: none"></td>
                                        <td :colspan="cantidad_cotizaciones * 4" class="encabezado">EXCLUSIONES</td>
                                    </tr>
                                    <tr>
                                        <th class="encabezado index_corto" >
                                            #
                                        </th>
                                        <th class="encabezado" >
                                            Descripción
                                        </th>
                                        <th  class="encabezado">
                                            Unidad
                                        </th>
                                        <th class="encabezado">
                                            Cantidad
                                        </th>

                                         <template v-for = "(cotizacion, c) in cotizaciones" >
                                            <th class="encabezado">
                                                Precio Unitario
                                            </th>
                                            <th colspan="2" class="encabezado">
                                                Moneda
                                            </th>
                                            <th class="encabezado">
                                                Importe Pesos (MXN)
                                            </th>
                                        </template>
                                    </tr>
                                    <template v-for="(exclusion, iex) in exclusiones">
                                        <tr  v-if="exclusion.importe>0">
                                            <td >{{exclusion.indice}}</td>
                                            <td >{{exclusion[0].descripcion}}</td>
                                            <td >{{exclusion[0].unidad}}</td>
                                            <td >{{exclusion[0].cantidad}}</td>
                                            <template v-for = "(cotizacion, c) in cotizaciones" >
                                                <template v-if="c == iex">
                                                    <td style="text-align: right;">${{exclusion[0].precio_unitario.formatMoney(2,".",",")}}</td>
                                                    <td colspan="2">{{exclusion[0].moneda}}</td>
                                                    <td style="text-align: right;">${{exclusion[0].total.formatMoney(2,".",",")}}</td>
                                                </template>
                                                <template v-else>
                                                    <td >&nbsp;</td>
                                                    <td colspan="2">&nbsp;</td>
                                                    <td >&nbsp;</td>
                                                </template>
                                            </template>
                                        </tr>
                                    </template>
                                    <tr>
                                        <td colspan="4" style="border: none"></td>
                                        <template v-for = "(cotizacion, c) in cotizaciones" >
                                            <td colspan="3" style="text-align: right; border: none">Total Exclusiones:</td>
                                            <td style="text-align: right" v-if="exclusiones[c] && exclusiones[c].importe>0">${{exclusiones[c].importe.formatMoney(2,".",",")}}</td>
                                            <td style="text-align: right" v-else>-</td>
                                        </template>
                                    </tr>

                                    <tr>
                                        <td :colspan="4 +(cantidad_cotizaciones *5)" style="border: none">&nbsp;</td>
                                    </tr>

                                    <tr>
                                        <td colspan="4" style="border: none"></td>
                                        <template v-for = "(cotizacion, c) in cotizaciones" >
                                            <td colspan="3" style="text-align: right; border: none">Total Comparativa:</td>
                                            <td style="text-align: right" v-if="exclusiones[c] && exclusiones[c].importe>0"><b>${{(exclusiones[c].importe + cotizacion.total).formatMoney(2,".",",")}}</b></td>
                                            <td style="text-align: right" v-else><b>${{cotizacion.total.formatMoney(2,".",",")}}</b></td>
                                        </template>
                                    </tr>

                                </template>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </span>

</template>

<script>
    import EncabezadoSolicitudCompra from "../solicitud-compra/partials/Encabezado";
    export default {
        name: "comparativa-cotizacion-compra-show",
        components: {EncabezadoSolicitudCompra},
        props: ['id'],
        data(){
            return{
                cargando: false,
                cotizaciones : [],
                exclusiones : [],
                partidas : [],
                precios_menores : [],
                cantidad_partidas:'',
                solicitud : '',
                cantidad_cotizaciones : '',
                indices_partidas : []
            }
        },
        mounted() {
            this.find();
        },
        methods: {
            find() {

                this.cargando = true;

                return this.$store.dispatch('compras/solicitud-compra/getComparativaCotizaciones', {
                    id: this.id,
                    params:{}
                }).then(data => {
                    this.solicitud = data.solicitud
                    this.cotizaciones = data.cotizaciones
                    this.exclusiones = data.exclusiones
                    this.partidas = data.partidas
                    this.precios_menores = data.precios_menores
                    this.cantidad_partidas = data.cantidad_partidas;
                    this.cantidad_cotizaciones = data.cantidad_cotizaciones
                }).finally(()=> {
                    this.cargando = false;
                })
            },

        },
        computed: {

        }
    }
</script>

<style scoped>
table {
    word-wrap: unset;
    width: 100%;
    background-color: white;
    border-color: transparent;
    border-collapse: collapse;
    clear: both;
    font-size: 10px;
}

table th,  table td {
    border: 1px solid #dee2e6;
}

table thead th
{
    padding: 0.2em;

    background-color: #f2f4f5;
    font-weight: bold;
    color: black;
    overflow: hidden;
    text-align: center;
}

table thead th.no_negrita
{
    padding: 0.2em;

    background-color: #f2f4f5;
    font-weight: normal;
    color: black;
    overflow: hidden;
    text-align: center;
}

table td.sin_borde {
    border: none;
    padding: 2px 5px;
}

table thead th {
    text-align: center;
}
table tbody tr
{
    border-width: 0 1px 1px 1px;
    border-style: none solid solid solid;
    border-color: white #CCCCCC #CCCCCC #CCCCCC;
}
table tbody td,
table tbody th
{
    border-right: 1px solid #ccc;
    color: #242424;
    line-height: 20px;
    overflow: hidden;
    padding: 2px 5px;
    text-align: left;
    text-overflow: ellipsis;
    -o-text-overflow: ellipsis;
    -ms-text-overflow: ellipsis;
    white-space: nowrap;
}

.encabezado{
    text-align: center;
    background-color: #f2f4f5;
    font-weight: bold;
}

</style>
