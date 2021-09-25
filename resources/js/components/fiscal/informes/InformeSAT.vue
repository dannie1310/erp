<template>
    <span>
        <div class="row" v-if="cargando">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="spinner-border text-success" role="status">
                           <span class="sr-only">Cargando...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card" v-else-if="cargando== false">
            <div class="card-body">
                <div class="row" >
                    <div class="col-md-12 table-responsive">
                        <table class="table table-sm table-fs-sm">
                            <thead >
                                <tr>
                                    <th rowspan="2" class="index_corto">#</th>
                                    <th rowspan="2" class="c100">RFC</th>
                                    <th rowspan="2">Razón Social</th>
                                    <th rowspan="2" class="sin_borde"></th>
                                    <th colspan="2">CFDI Tipo I</th>

                                    <th rowspan="2" class="sin_borde"></th>
                                    <th colspan="2">CFDI Tipo E</th>

                                    <th rowspan="2" class="sin_borde"></th>
                                    <th colspan="2">Neto CFDI</th>

                                    <th rowspan="2" class="sin_borde"></th>
                                    <th colspan="3">Contabilidad GHI</th>
                                </tr>
                                <tr>

                                    <th class="c80">Neto Tipo I</th>
                                    <th class="c80">Total Con IVA</th>

                                    <th class="c80">Neto Tipo E</th>
                                    <th class="c80">Total Con IVA</th>

                                    <th class="c80">Neto CFDI</th>
                                    <th class="c80">Total Con IVA</th>

                                    <th class="c80">Cantidad Cuentas Relacionadas</th>
                                    <th class="c80">Pasivos Registrados</th>
                                    <th class="c80">Diferencias</th>

                                </tr>

                            </thead>
                            <tbody>
                            <template v-for="(partida, i) in informe">
                                <tr class="sin_borde">
                                <td>
                                    {{i + 1}}
                                </td>
                                <td>
                                    {{partida.rfc}}
                                </td>
                                <td>
                                    {{partida.razon_social}}
                                </td>
                                <td class="sin_borde">
                                    &nbsp;
                                </td>
                                <td style="text-align: right">
                                    <span v-if="parseFloat(partida.neto_subtotal_i) != 0">
                                        ${{parseFloat(partida.neto_subtotal_i).formatMoney(2,".",",") }}
                                    </span>
                                    <span v-else>-</span>
                                </td>
                                <td style="text-align: right">
                                    <span v-if="parseFloat(partida.neto_total_i) != 0">
                                        ${{parseFloat(partida.neto_total_i).formatMoney(2,".",",") }}
                                    </span>
                                    <span v-else>-</span>
                                </td>

                                <td class="sin_borde">
                                    &nbsp;
                                </td>
                                <td style="text-align: right">
                                    <span v-if="parseFloat(partida.neto_subtotal_e) != parseFloat('0')">
                                        ${{parseFloat(partida.neto_subtotal_e).formatMoney(2,".",",") }}
                                    </span>
                                    <span v-else>-</span>
                                </td>
                                <td style="text-align: right">
                                    <span v-if="parseFloat(partida.neto_total_e) != 0">
                                        ${{parseFloat(partida.neto_total_e).formatMoney(2,".",",") }}
                                    </span>
                                    <span v-else>-</span>
                                </td>

                                <td class="sin_borde">
                                    &nbsp;
                                </td>
                                <td style="text-align: right">
                                    ${{parseFloat(partida.neto_subtotal_sat).formatMoney(2,".",",") }}
                                </td>
                                <td style="text-align: right">
                                    ${{parseFloat(partida.neto_total_sat).formatMoney(2,".",",") }}
                                </td>

                                <td class="sin_borde">
                                    &nbsp;
                                </td>
                                <td style="text-align: right; text-decoration: underline" :style="parseFloat(partida.cantidad_cuentas)>0?`cursor : pointer`:``" v-on:click="verCuentas(partida)" v-if="parseFloat(partida.cantidad_cuentas)>0">
                                     {{parseFloat(partida.cantidad_cuentas) }}
                                </td>
                                 <td style="text-align: right" v-else>
                                    -
                                </td>
                                <td style="text-align: right">
                                    ${{parseFloat(partida.importe_movimientos_pasivo).formatMoney(2,".",",") }}
                                </td>
                                <td style="text-align: right">
                                    ${{parseFloat(partida.diferencia).formatMoney(2,".",",") }}
                                </td>

                            </tr>

                            </template>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Cuentas</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-sm table-fs-sm">
                                        <thead>
                                        <tr>
                                            <th class="index_corto">#</th>
                                            <th>Cuenta</th>
                                            <th>Monto</th>
                                        </tr>
                                        </thead>

                                        <tr v-for="(cuenta, i ) in cuentas">
                                            <td>{{i+1}}</td>
                                            <td>{{cuenta.codigo_cuenta}}</td>
                                            <td style="text-align: right">${{parseFloat(cuenta.importe_movimiento).formatMoney(2,".",",") }}</td>
                                        </tr>
                                    </table>

                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
export default {
    name: "Informe",
    data() {
        return {
            informe : [],
            cuentas : [],
            cargando: false,
        }
    },
    mounted() {
        this.getInforme();
    },
    props: ['id'],
    methods: {
        getInforme() {
            this.cargando = true;
            return this.$store.dispatch('fiscal/cfd-sat/obtenerInformeSATLP2020', {
                id:this.id

            })
            .then(data => {
                this.informe = data.informe;
                //this.getMovimientos(this.informe.data[0])
            })
            .finally(() => {
                this.cargando = false;
            });
        },
        getMovimientos(item)
        {

            this.$router.push({name: 'cuenta-saldo-negativo-detalle-movimientos', params: {aniomes: item.anio+'-'+item.mes}});
        },
        verCuentas(partida)
        {

            return this.$store.dispatch('fiscal/cfd-sat/obtenerCuentasInformeSATLP2020', {
                id: partida.id_proveedor_sat

            })
                .then(data => {
                    this.cuentas = data.informe;

                })
                .finally(() => {
                    $(this.$refs.modal).appendTo('body')
                    $(this.$refs.modal).modal('show');
                });

        }
    },
    computed: {
        anio_seleccionado(){
            return this.$store.getters['contabilidadGeneral/cuenta-saldo-negativo/anioSeleccionado'];
        },

        mes_seleccionado(){
            return this.$store.getters['contabilidadGeneral/cuenta-saldo-negativo/mesSeleccionado'];
        },

    },
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
}
table.table-fs-sm{
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

.sin_borde{
    border:none; background-color:#FFF
}

</style>
