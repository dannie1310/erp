<template>
    <span>
        <div  v-if="!pago">
            <div class="row" >
                <div class="col-md-12">
                    <div class="spinner-border text-success" role="status">
                        <span class="sr-only">Cargando...</span>
                    </div>
                </div>
            </div>
        </div>
        <div v-else>
            <div class="card">
                <div class="card-body">
                    <div class="row">
                            <div class="col-md-6">
                                <h5>Folio: &nbsp; <b>{{pago.numero_folio_format}}</b></h5>
                            </div>
                            <div class="col-md-6" align="right">
                                <h5>Fecha: <b>{{pago.fecha_format}}</b></h5>
                            </div>
                        </div>
                    <div class="row">
                        <div class="table-responsive col-md-12">
                            <table class="table">
                                <tbody>
                                <tr>
                                    <td class="bg-gray-light"><b>Beneficiario:</b></td>
                                    <td class="bg-gray-light" colspan="3">{{pago.empresa ? pago.empresa.razon_social : pago.destino}}</td>
                                    <td class="bg-gray-light"><b>Cuenta:</b></td>
                                    <td class="bg-gray-light">{{pago.cuenta.numero}} ( {{pago.cuenta.abreviatura}} )</td>
                                </tr>
                                <tr>
                                    <td class="bg-gray-light"><b>Concepto:</b></td>
                                    <td class="bg-gray-light" colspan="5">{{pago.observaciones.toLocaleUpperCase()}}</td>
                                </tr>
                                <tr>
                                    <td class="bg-gray-light"><b>Estado:</b></td>
                                    <td class="bg-gray-light">{{pago.estado_string}}</td>
                                    <td class="bg-gray-light"><b>Tipo:</b></td>
                                    <td class="bg-gray-light">{{pago.tipo_pago}}</td>
                                    <td class="bg-gray-light"><b>Importe Pagado:</b></td>
                                    <td class="bg-gray-light">{{pago.monto_format}}</td>
                                </tr>
                                <tr v-if="pago.usuario">
                                    <td class="bg-gray-light"><b>Usuario Registró:</b></td>
                                    <td class="bg-gray-light" colspan="5">{{pago.usuario.nombre}}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <hr>
                    <div class="row" v-if="pago.antecedente || pago.ordenesPago">
                            <div class="col-md-12">
                                <h6><b>Transacción Pagada</b></h6>
                            </div>
                    </div>
                    <div class="row" v-if="pago.antecedente">
                        <div class="table-responsive col-md-12">
                            <table class="table">
                                <tbody>
                                <tr>
                                    <td class="bg-gray-light"><b>Folio:</b></td>
                                    <td class="bg-gray-light">{{pago.antecedente.numero_folio}}</td>
                                    <td class="bg-gray-light"><b>Fecha:</b></td>
                                    <td class="bg-gray-light">{{pago.antecedente.fecha.substr(0, 10)}}</td>
                                    <td class="bg-gray-light"><b>Tipo:</b></td>
                                    <td class="bg-gray-light">{{pago.tipo_antecedente}}</td>
                                    <td class="bg-gray-light"><b>Importe:</b></td>
                                    <td class="bg-gray-light">{{ '$ '+parseFloat(pago.antecedente.monto).formatMoney(2)}}</td>
                                </tr>
                                <tr>
                                    <td class="bg-gray-light"><b>Observaciones:</b></td>
                                    <td class="bg-gray-light" colspan="7">{{pago.antecedente.observaciones}}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row" v-if="pago.ordenesPago">
                        <div class="table-responsive col-md-12">
                            <table class="table">
                                <tbody v-for="(partida, i) in pago.ordenesPago.data" >
                                <tr>
                                    <td class="bg-gray-light"><b>Folio:</b></td>
                                    <td class="bg-gray-light">{{partida.factura.numero_folio}}</td>
                                    <td class="bg-gray-light"><b>Fecha:</b></td>
                                    <td class="bg-gray-light">{{partida.factura.fecha.substr(0, 10)}}</td>
                                    <td class="bg-gray-light"><b>Tipo:</b></td>
                                    <td class="bg-gray-light">{{partida.factura.tipo}}</td>
                                    <td class="bg-gray-light"><b>Importe:</b></td>
                                    <td class="bg-gray-light">{{partida.factura.monto_format}}</td>
                                </tr>
                                <tr>
                                    <td class="bg-gray-light"><b>Observaciones:</b></td>
                                    <td class="bg-gray-light" colspan="8">{{partida.factura.observaciones}}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" v-on:click="salir">
                        <i class="fa fa-angle-left"></i>
                        Regresar
                    </button>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
export default {
    name: "pago-show",
    props: ['id'],
    mounted() {
        this.find();
    },
    methods: {
        salir(){
            this.$router.push({name: 'pago'});
        },
        find() {
            this.$store.commit('finanzas/pago/SET_PAGO', null);
            return this.$store.dispatch('finanzas/pago/find', {
                id: this.id,
                params: {include: ['moneda', 'cuenta', 'empresa', 'usuario', 'antecedente', 'ordenesPago.factura']}
            })
                .then(data => {
                    this.$store.commit('finanzas/pago/SET_PAGO', data);
                    this.cargando = false;
                })
                .finally(() => {
                    this.cargando = false;
                })
        },
    },
    computed: {
        pago() {
            return this.$store.getters['finanzas/pago/currentPago']
        }
    }
}
</script>

<style scoped>

</style>
