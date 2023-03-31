<template>
    <span>
        <div class="card" v-if="!presupuesto">
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
            <div class="card-body table-responsive">
                <DatosPresupuesto v-bind:presupuesto="presupuesto"></DatosPresupuesto>
                <div class="row" >
                    <div class="col-md-12 table-responsive">
                        <table id="tabla-conceptos" >
                            <thead>
                                <tr>
                                    <th >#</th>
                                    <th >Clave</th>
                                    <th >Concepto</th>
                                    <th >UM</th>
                                    <th  class="contratado">Cantidad Solicitada</th>

                                    <th  class="contratado">Cantidad Autorizada</th>
                                    <th  v-if="presupuesto.con_descuento_partidas">PU antes descuento</th>
                                    <th  v-if="presupuesto.con_descuento_partidas">Importe antes descuento</th>
                                    <th  v-if="presupuesto.con_descuento_partidas">% Descuento</th>
                                    <th  >PU</th>
                                    <th  >Importe</th>
                                    <th  v-if="presupuesto.con_moneda_extranjera">Moneda</th>
                                    <th  v-if="presupuesto.con_moneda_extranjera">PU Moneda Conversión</th>
                                    <th  v-if="presupuesto.con_moneda_extranjera">Importe Moneda Conversión</th>
                                    <th  v-if="presupuesto.con_observaciones_partidas">Observaciones</th>

                                    <th class="destino">Destino</th>
                                </tr>

                            </thead>
                            <tbody v-for="(concepto, i) in presupuesto.contratos.data">
                                <tr v-if="concepto.unidad == null">
                                    <td >{{i + 1}}</td>
                                    <td :title="concepto.clave"><b>{{concepto.clave}}</b></td>
                                    <td :title="concepto.descripcion">
                                        <span v-for="n in concepto.nivel">-</span>
                                        <b>{{concepto.descripcion}}</b>
                                    </td>
                                    <td></td>
                                    <td></td>

                                    <td></td>
                                    <td v-if="presupuesto.con_descuento_partidas"></td>
                                    <td v-if="presupuesto.con_descuento_partidas"></td>
                                    <td v-if="presupuesto.con_descuento_partidas"></td>
                                    <td></td>
                                    <td></td>
                                    <td v-if="presupuesto.con_moneda_extranjera"></td>
                                    <td v-if="presupuesto.con_moneda_extranjera"></td>
                                    <td v-if="presupuesto.con_moneda_extranjera"></td>
                                    <td v-if="presupuesto.con_observaciones_partidas"></td>

                                    <td></td>
                                </tr>
                                <tr v-else>
                                    <td >{{i + 1}}</td>
                                    <td :title="concepto.clave">{{ concepto.clave }}</td>
                                    <td :title="concepto.descripcion">
                                        <span v-for="n in concepto.nivel">-</span>
                                        {{concepto.descripcion}}
                                    </td>
                                    <td >{{concepto.unidad}}</td>
                                    <td class="numerico">{{concepto.cantidad_original_format}}</td>

                                    <td class="numerico">{{concepto.cantidad_presupuestada_format}}</td>
                                    <td class="numerico" v-if="presupuesto.con_descuento_partidas">{{concepto.presupuesto.precio_unitario_antes_descuento_format}}</td>
                                    <td class="numerico" v-if="presupuesto.con_descuento_partidas">{{concepto.presupuesto.total_antes_descuento_format}}</td>
                                    <td class="numerico" v-if="presupuesto.con_descuento_partidas">{{concepto.presupuesto.descuento_format}}</td>
                                    <td class="numerico">{{concepto.presupuesto.precio_unitario_despues_descuento_format}}</td>
                                    <td class="numerico">{{concepto.presupuesto.total_despues_descuento_format}}</td>
                                    <td v-if="presupuesto.con_moneda_extranjera">{{concepto.presupuesto.moneda.nombre}}</td>
                                    <td class="numerico" v-if="presupuesto.con_moneda_extranjera">{{concepto.presupuesto.precio_unitario_despues_descuento_partida_mc_format}}</td>
                                    <td class="numerico" v-if="presupuesto.con_moneda_extranjera">{{concepto.presupuesto.total_despues_descuento_partida_mc_format}}</td>
                                    <td v-if="presupuesto.con_observaciones_partidas">{{concepto.presupuesto.observaciones}}</td>

                                    <td :title="concepto.destino.concepto.path" style="text-decoration: underline">{{ concepto.destino.concepto.path_corta }}</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td :colspan ="presupuesto.colspan" style="text-align: right" class="sin_borde">Subtotal antes de descuento:</td>
                                    <td class="numerico">{{presupuesto.subtotal_mc_antes_descuento_global_format}}</td>
                                </tr>
                                <tr>
                                    <td :colspan ="presupuesto.colspan" style="text-align: right" class="sin_borde">% Descuento:</td>
                                    <td class="numerico">{{presupuesto.porcentaje_descuento_format}}</td>
                                </tr>

                                <tr v-for="(subtotal, i) in presupuesto.subtotales_por_moneda.data">
                                    <td :colspan ="presupuesto.colspan" style="text-align: right" class="sin_borde">Subtotal {{subtotal.moneda}}:</td>
                                    <td class="numerico">{{subtotal.subtotal_format}}</td>
                                </tr>
                                <tr>
                                    <td :colspan ="presupuesto.colspan" style="text-align: right" class="sin_borde">TC USD:</td>
                                    <td class="numerico">{{presupuesto.tc_usd_format}}</td>
                                </tr>
                                <tr>
                                    <td :colspan ="presupuesto.colspan" style="text-align: right" class="sin_borde">TC EURO:</td>
                                    <td class="numerico">{{presupuesto.tc_euro_format}}</td>
                                </tr>
                                <tr>
                                    <td :colspan ="presupuesto.colspan" style="text-align: right" class="sin_borde">TC Libra:</td>
                                    <td class="numerico">{{presupuesto.tc_libra_format}}</td>
                                </tr>
                                <tr>
                                    <td :colspan ="presupuesto.colspan" style="text-align: right" class="sin_borde">Moneda Conversión:</td>
                                    <td >{{presupuesto.moneda_conversion}}</td>
                                </tr>
                                <tr>
                                    <td :colspan ="presupuesto.colspan" style="text-align: right" class="sin_borde">Subtotal:</td>
                                    <td class="numerico">{{presupuesto.subtotal_format}}</td>
                                </tr>
                                <tr>
                                    <td :colspan ="presupuesto.colspan" style="text-align: right" class="sin_borde">Tasa de IVA:</td>
                                    <td class="numerico">{{presupuesto.tasa_iva}}</td>
                                </tr>
                                <tr>
                                    <td :colspan ="presupuesto.colspan" style="text-align: right" class="sin_borde">IVA:</td>
                                    <td class="numerico">{{presupuesto.impuesto_format}}</td>
                                </tr>
                                <tr>
                                    <td :colspan ="presupuesto.colspan" style="text-align: right" class="sin_borde">Monto:</td>
                                    <td class="numerico">{{presupuesto.monto_format}}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <br />
                <div class="row" v-if="presupuesto.exclusiones.data.length != 0">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr>
                                        <td class="encabezado" colspan="8"><b>Exclusiones</b></td>
                                    </tr>
                                    <tr>
                                        <th class="index_corto">#</th>
                                        <th width="30%">Descripción</th>
                                        <th>Unidad</th>
                                        <th>Cantidad</th>
                                        <th>Precio Unitario</th>
                                        <th class="money" style="text-align:center;">Moneda</th>
                                        <th>Precio Total</th>
                                    </tr>
                                    </thead>
                                <tbody>
                                    <tr v-for="(exclusion, i) in presupuesto.exclusiones.data">
                                        <td class="index_corto">{{ i + 1 }}</td>
                                        <td>{{exclusion.descripcion}}</td>
                                        <td style="text-align: center">{{exclusion.unidad}}</td>
                                        <td style="text-align:right;">{{exclusion.cantidad_format}}</td>
                                        <td style="text-align:right;">{{exclusion.precio_format}}</td>
                                        <td style="text-align:center;">{{exclusion.moneda}}</td>
                                        <td style="text-align:right;">{{exclusion.total_format}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    import DatosPresupuesto from "./partials/DatosPresupuesto";
    export default {
        name: "presupuesto-show",
        components: {DatosPresupuesto},
        props: ['id'],
        data(){
            return{
                cargando: false,
            }
        },
        mounted() {
            this.find();
        },
        methods: {
            find() {

                this.cargando = true;
                this.$store.commit('contratos/presupuesto/SET_PRESUPUESTO', null);
                return this.$store.dispatch('contratos/presupuesto/find', {
                    id: this.id,
                    params:{include: [
                        'contrato_proyectado',
                        'contratos.presupuesto:id_transaccion_presupuesto('+this.id+')',
                        'contratos.destino',
                        'sucursal',
                        'empresa', 'subtotales_por_moneda','exclusiones']}
                }).then(data => {
                    this.$store.commit('contratos/presupuesto/SET_PRESUPUESTO', data);
                    this.cargando = false;
                });
            }
        },
        computed: {
            presupuesto() {
                return this.$store.getters['contratos/presupuesto/currentPresupuesto'];
            },
            total()
            {
                return '$ ' + (parseFloat(this.presupuesto.subtotal) + parseFloat(this.presupuesto.impuesto)).formatMoney(2,'.',',');
            }
        }
    }
</script>

<style scoped>
table#tabla-conceptos {
    word-wrap: unset;
    width: 100%;
    background-color: white;
    border-color: transparent;
    border-collapse: collapse;
    clear: both;
}

table#tabla-conceptos th, table#tabla-conceptos td {
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

table#tabla-conceptos td.sin_borde {
    border: none;
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
    padding: 1px 5px;
    text-align: left;
    text-overflow: ellipsis;
    -o-text-overflow: ellipsis;
    -ms-text-overflow: ellipsis;
    white-space: nowrap;
}

table col.clave { width: 120px; }
table col.icon { width: 25px; }
table col.monto { width: 115px; }
table col.pct { width: 60px; }
table col.unidad { width: 80px; }
table col.clave  {width: 100px; }

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

.encabezado{
    text-align: center;
    background-color: #f2f4f5
}

table tbody td input.text {
    text-align: right;
}
</style>
