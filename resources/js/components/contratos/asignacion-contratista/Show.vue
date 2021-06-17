<template>
    <span>
        <div v-if="!asignacion">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="spinner-border text-success" role="status">
                                <span class="sr-only">Cargando...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row"  v-if="asignacion">
            <div class="col-12">
                <div class="invoice p-3 mb-3">
                    <div class="row">
                        <div class="col-12">
                            <h5>Asignación: </h5>
                        </div>
                    </div>
                    <div class="row">
                        <div class="table-responsive col-md-12">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td class="bg-gray-light"><b>Folio Asignación:</b></td>
                                        <td class="bg-gray-light">{{asignacion.numero_folio_asignacion}}</td>
                                        <td class="bg-gray-light"><b>Fecha Registro:</b></td>
                                        <td class="bg-gray-light">{{asignacion.fecha_registro}}</td>
                                        <td class="bg-gray-light"><b>Registro:</b></td>
                                        <td class="bg-gray-light">{{asignacion.usuario_registro}}</td>
                                    </tr>
                                    <tr>
                                        <td class="bg-gray-light"><b>Folio Contrato:</b></td>
                                        <td class="bg-gray-light">{{asignacion.contrato.numero_folio_format}}</td>
                                        <td class="bg-gray-light"><b>Fecha:</b></td>
                                        <td class="bg-gray-light">{{asignacion.contrato.fecha}}</td>
                                        <td class="bg-gray-light"><b>Referencia:</b></td>
                                        <td class="bg-gray-light">{{asignacion.contrato.referencia}}</td>
                                    </tr>
                                </tbody>
                            </table>
                            <table class="table table-bordered table-responsive" style="width: 100%">
                                <template v-for="(presupuesto, ip) in asignacion.presupuestosContratista.data">
                                    <tr>
                                        <td colspan="3"></td>
                                        <td  class="encabezado" colspan="7">
                                            {{presupuesto.empresa.razon_social}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="encabezado th_index_corto">#</td>
                                        <td class="encabezado"> Descripcion</td>
                                        <td class="encabezado c120"> Unidad</td>
                                        <td class="encabezado c120">Precio Unitario Antes Descto.</td>
                                        <td class="encabezado c120">% Descuento</td>
                                        <td class="encabezado c120">Precio Unitario</td>
                                        <td class="encabezado c120">Moneda</td>
                                        <td class="encabezado c120">Precio Unitario en Moneda de Conversión</td>
                                        <td class="encabezado c120">Cantidad Asignada</td>
                                        <td class="encabezado c120">Total Asignado en Moneda de Conversión</td>
                                    </tr>
                                    <tr v-for="(partida, ic) in presupuesto.partidas_asignadas.data">
                                        <td>{{ic+1}}</td>
                                        <td>{{partida.concepto.descripcion}}</td>
                                        <td>{{partida.concepto.unidad}}</td>
                                        <td style="text-align: right">{{partida.precio_unitario_antes_descuento_format}}</td>
                                        <td style="text-align: right">{{partida.descuento_format}}</td>
                                        <td style="text-align: right">{{partida.precio_unitario_despues_descuento_format}}</td>
                                        <td >{{partida.moneda.nombre}}</td>
                                        <td style="text-align: right">{{partida.precio_unitario_despues_descuento_partida_mc_format}}</td>
                                        <td style="text-align: right">{{partida.cantidad_asignada}}</td>
                                        <td style="text-align: right">{{partida.importe_asignado_format}}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" style="text-align: right" ><b>TC Dolar:</b></td>
                                        <td style="text-align: right">{{presupuesto.tc_usd_format}}</td>
                                        <td colspan="6">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" style="text-align: right" ><b>TC Euro</b></td>
                                        <td style="text-align: right">{{presupuesto.tc_euro_format}}</td>
                                        <td colspan="6">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" style="text-align: right" ><b>TC Libra</b></td>
                                        <td style="text-align: right">{{presupuesto.tc_libra_format}}</td>
                                        <td colspan="6">&nbsp;</td>
                                    </tr>
                                    <tr><td colspan="10">&nbsp;</td></tr>
                                </template>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>

    export default {
        name: "subcontrato-show",
        props: ['id'],
        mounted(){
            this.find();
        },
        data() {
            return {
                motivo: '',
                cargando: false
            }
        },
        methods: {
            find() {
                this.cargando = true;
                this.$store.commit('contratos/asignacion-contratista/SET_ASIGNACION', null);
                return this.$store.dispatch('contratos/asignacion-contratista/find', {
                    id: this.id,
                    params: {
                        include:
                            [
                                , 'contrato'
                                , 'presupuestosContratista.empresa'
                                , 'presupuestosContratista.partidas_asignadas:id_asignacion('+this.id+')'
                            ]
                    }
                }).then(data => {
                    this.$store.commit('contratos/asignacion-contratista/SET_ASIGNACION', data);
                })
            },
        },
        computed: {
            asignacion() {
                return this.$store.getters['contratos/asignacion-contratista/currentAsignacion'];
            }
        },
    }
</script>

<style scoped>
td.encabezado{
    text-align: center; background-color: #f2f4f5
}

</style>
