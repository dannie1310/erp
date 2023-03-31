<template>
    <span>
        <div class="row" v-if="!cotizacion.partidas">
            <div class="col-md-12">
                <div class="spinner-border text-success" role="status">
                   <span class="sr-only">Cargando...</span>
                </div>
            </div>
        </div>
        <span v-else>
            <div class="row" >
                <div class="col-md-12">
                    <datos-cotizacion-compra v-bind:cotizacion_compra="cotizacion"></datos-cotizacion-compra>
                </div>
            </div>
            <div class="row">
                <div  class="col-md-12" v-if="cotizacion.partidas">
                    <div class="table-responsive">
                        <table id="tabla-conceptos">
                            <thead>
                            <tr>
                                <th class="index_corto">#</th>
                                <th style="width:110px;">No. de Parte</th>
                                <th>Descripción</th>
                                <th class="unidad">Unidad</th>
                                <th >Cantidad</th>
                                <th class="cantidad_input">Precio Unitario</th>
                                <th class="cantidad_input">% Descuento</th>
                                <th >Precio Total</th>
                                <th >Moneda</th>
                                <th >Precio Total Moneda Conversión</th>
                                <th>Observaciones</th>
                            </tr>
                            </thead>
                            <tbody v-if="cotizacion.partidas">
                                <tr v-for="(partida, i) in cotizacion.partidas.data">
                                    <td style="text-align:center; vertical-align:inherit;">{{i+1}}</td>
                                    <td>{{(partida.material) ? partida.material.numero_parte : '----'}}</td>
                                    <td>{{(partida.material) ? partida.material.descripcion : '----'}}</td>
                                    <td >{{(partida.material) ? partida.material.unidad : '----'}}</td>

                                    <td class="numerico">{{partida.cantidad_format}}</td>
                                    <td class="numerico">
                                        {{partida.precio_unitario_format}}
                                    </td>
                                    <td class="numerico">
                                        {{(parseFloat(partida.descuento)).formatMoney(2,'.',',')}}
                                    </td>
                                    <td style="text-align:right;">{{'$' + parseFloat((partida.cantidad) * partida.precio_unitario).formatMoney(2,'.',',')}}</td>
                                    <td style="width:120px;" >
                                        {{ partida.moneda.nombre}}
                                    </td>
                                    <td style="text-align:right;">{{partida.precio_total_moneda}}</td>
                                    <td style="width:200px;">
                                        {{partida.observacion}}
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="9" style="text-align: right" class="sin_borde">Subtotal antes de descuento:</td>
                                    <td class="numerico">{{ cotizacion.subtotal }}</td>
                                </tr>
                                <tr>
                                    <td colspan="9" style="text-align: right" class="sin_borde">Descuento(%):</td>
                                    <td class="numerico">{{(parseFloat(descuento_cot)).formatMoney(2,'.',',')}}</td>
                                </tr>
                                <tr>
                                    <td colspan="9" style="text-align: right" class="sin_borde">Subtotal Precios Peso (MXN):</td>
                                    <td class="numerico">${{(parseFloat(pesos)).formatMoney(2,'.',',')}}</td>
                                </tr>
                                <tr>
                                    <td colspan="5" style="text-align: right" class="sin_borde"></td>
                                    <td class="numerico sin_borde">TC Dólar:&nbsp;&nbsp;<b>${{(parseFloat(dolar)).formatMoney(2,'.',',')}}</b></td>
                                    <td colspan="3" style="text-align: right" class="sin_borde">Subtotal Precios Dolar (USD):</td>
                                    <td class="numerico">${{(parseFloat(dolares)).formatMoney(2,'.',',')}}</td>
                                </tr>
                                <tr>
                                    <td colspan="5" style="text-align: right" class="sin_borde"></td>
                                    <td class="numerico sin_borde">TC Euro:&nbsp;&nbsp;<b>${{(parseFloat(euro)).formatMoney(2,'.',',')}}</b></td>
                                    <td colspan="3" style="text-align: right" class="sin_borde">Subtotal Precios EURO:</td>
                                    <td class="numerico">${{(parseFloat(euros)).formatMoney(2,'.',',')}}</td>
                                </tr>
                                <tr>
                                    <td colspan="5" style="text-align: right" class="sin_borde"></td>
                                    <td class="numerico sin_borde">TC Libra:&nbsp;&nbsp;<b>${{(parseFloat(libra)).formatMoney(2,'.',',')}}</b></td>
                                    <td colspan="3" style="text-align: right" class="sin_borde">Subtotal Precios LIBRA:</td>
                                    <td class="numerico">${{(parseFloat(libras)).formatMoney(2,'.',',')}}</td>
                                </tr>
                                <tr>
                                    <td colspan="9" style="text-align: right" class="sin_borde">Subtotal Moneda Conversión (MXN):</td>
                                    <td class="numerico">{{cotizacion.subtotal}}</td>
                                </tr>
                                <tr>
                                    <td colspan="9" style="text-align: right" class="sin_borde">IVA ({{cotizacion.tasa_iva_format}}%):</td>
                                    <td class="numerico">{{cotizacion.impuesto}}</td>
                                </tr>
                                <tr>
                                    <td colspan="9" style="text-align: right" class="sin_borde">Total:</td>
                                    <td class="numerico"><b>{{cotizacion.importe}}</b></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <hr v-if="cotizacion.exclusiones.data.length > 0" />
            <div class="row" v-if="cotizacion.exclusiones.data.length > 0">
                <div class="col-md-12">
                    <div >
                        <table class="table table-sm tabla">
                            <thead>
                            <tr>
                                <td  colspan="7" style="border: none;text-align: center"><h6><b>Exclusiones</b></h6></td>
                            </tr>
                            <tr>
                                <th class="index_corto">#</th>
                                <th>Descripción</th>
                                <th class="unidad">Unidad</th>
                                <th class="cantidad_input">Cantidad</th>
                                <th class="cantidad_input">Precio Unitario</th>
                                <th class="cantidad_input">Moneda</th>
                                <th class="cantidad_input">Precio Total</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(exclusion, i) in cotizacion.exclusiones.data">
                                <td class="index_corto">{{ i + 1 }}</td>
                                <td>{{exclusion.descripcion}}</td>
                                <td class="unidad">{{exclusion.unidad}}</td>
                                <td class="cantidad_input" style="text-align:right;">{{exclusion.cantidad_format}}</td>
                                <td class="cantidad_input" style="text-align:right;">{{exclusion.precio_format}}</td>
                                <td>{{exclusion.moneda}}</td>
                                <td class="cantidad_input" style="text-align:right;">{{exclusion.total_format}}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </span>
    </span>
</template>

<script>
import DatosCotizacionCompra from "./DatosCotizacionCompra";
export default {
    name: "cotizacion-partial-show",
    components: {DatosCotizacionCompra},
    props: ['id', 'show'],
    data() {
        return {
            cargando: false,
            cotizacion : [],
            descuento_cot : '0.00',
            monedas: [],
            pesos: 0,
            dolares: 0,
            euros: 0,
            libras: 0,
            dolar:0,
            euro:0,
            libra:0,
            observaciones : '',
            pago: 0,
            anticipo: 0,
            credito: 0,
            tiempo: 0,
            vigencia: 0,
        }
    },
    mounted() {
        this.find();
    },
    methods : {
        find() {
            this.cargando = true;
            this.$store.commit('compras/cotizacion/SET_COTIZACION', null);

            return this.$store.dispatch('compras/cotizacion/find', {
                id: this.id,
                params:{include: ['empresa', 'sucursal', 'complemento', 'partidas', 'exclusiones']}
            }).then(data => {
                this.$store.commit('compras/cotizacion/SET_COTIZACION', data);
                this.items = data.partidas.data;
                this.cotizacion = data;
                this.dolar = data.complemento ? parseFloat(data.complemento.tc_usd).formatMoney(4, '.', '') : 0;
                this.euro = data.complemento ? parseFloat(data.complemento.tc_eur).formatMoney(4, '.', '') : 0;
                this.libra = data.complemento ? parseFloat(data.complemento.tc_libra).formatMoney(4, '.', '') : 0;
            }).finally(() => {
                this.cargando = false;
            });
        },
    },
    computed: {

    },
}
</script>

<style scoped>
table#tabla-resumen-monedas, table.tabla {
    word-wrap: unset;
    width: 100%;
    background-color: white;
    border-color: transparent;
    border-collapse: collapse;
    clear: both;
}

table#tabla-resumen-monedas th, table.tabla th, table#tabla-resumen-monedas td , table.tabla td  {
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

table#tabla-resumen-monedas td.sin_borde, table.tabla td.sin_borde  {
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
table#tabla-resumen-monedas table tbody th, table.tabla table tbody th
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


table tbody td input.text
{
    border: none;
    padding: 0;
    margin: 0;
    width: 100%;
    background-color: transparent;
    font-family: inherit;
    font-size: inherit;
    font-weight: bold;
}

table .numerico
{
    text-align: right;
    padding-left: 0;
    white-space: normal;
}

.text.is-invalid {
    color: #dc3545;
}

table tbody td input.text {
    text-align: right;
}

.encabezado{
    text-align: center;
    background-color: #f2f4f5
}
</style>

