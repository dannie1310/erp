<template>
    <span>
        <div  v-if="!fondo">
            <div class="row" >
                <div class="col-md-12">
                    <div class="spinner-border text-success" role="status">
                       <span class="sr-only">Cargando...</span>
                    </div>
                </div>
            </div>
        </div>
        <div v-else>
            <div class="row">
                <div class="col-12">
                    <div class="invoice p-3 mb-3">
                        <div class="row col-md-12">
                            <div class="col-md-6">
                                <h5>Folio: &nbsp; <b>{{fondo.numero_folio_format}}</b></h5>
                            </div>
                            <div class="col-md-6">
                            </div>
                        </div>
                        <div class="table-responsive col-md-12">
                            <table class="table">
                                <tbody>
                                <tr>
                                    <td class="bg-gray-light"><b>Fecha:</b></td>
                                    <td class="bg-gray-light"> {{fondo.fecha_format}} </td>
                                    <td class="bg-gray-light"><b>Fondo:</b></td>
                                    <td class="bg-gray-light">{{fondo.fondo.descripcion}}</td>
                                    <td class="bg-gray-light"><b>Referencia:</b></td>
                                    <td class="bg-gray-light">{{fondo.referencia}}</td>
                                </tr>
                                <tr>
                                    <td class="bg-gray-light"><b>Concepto:</b></td>
                                    <td class="bg-gray-light" colspan="5">{{fondo.concepto ? fondo.concepto.path : '' }}</td>
                                </tr>
                                <tr>
                                    <td class="bg-gray-light"><b>Usuario Registró:</b></td>
                                    <td class="bg-gray-light" colspan="3">{{fondo.usuario_registro}}</td>
                                    <td class="bg-gray-light"><b>Fecha / Hora de Registro:</b></td>
                                    <td class="bg-gray-light">{{fondo.fecha_registro}}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <h6><b>Detalle de las partidas</b></h6>
                            </div>
                        </div>
                        <div class="row">
                            <div class="table-responsive col-md-12">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th class="index_corto">#</th>
                                        <th>Concepto</th>
                                        <th class="numerico">Cantidad</th>
                                        <th class="numerico">Precio</th>
                                        <th class="numerico">Monto</th>
                                        <th class="destino">Destino</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(partida, i) in fondo.partidas.data">
                                        <td style="text-align: center" class="index_corto">{{i+1}}</td>
                                        <td style="text-align: left">{{partida.referencia }}</td>
                                        <td style="text-align: right" class="numerico">{{ partida.cantidad_format }}</td>
                                        <td style="text-align: right" class="numerico">{{ partida.importe_format }}</td>
                                        <td style="text-align: right" class="numerico">$ {{ partida.monto_format }}</td>
                                        <td style="text-align: left" class="destino">{{ partida.concepto ? partida.concepto.descripcion : '' }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-9">
                                <label class="col-form-label">Observaciones: </label>
                                <input
                                    type="text"
                                    disabled="true"
                                    v-model="fondo.observaciones"
                                    class="form-control">
                            </div>
                            <div class="col-md-3" align="left">
                                <div class="table-responsive col-md-12">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table class="table table-borderless">
                                                <tbody>
                                                    <tr>
                                                        <th>Subtotal:</th>
                                                        <td align="right">{{fondo.monto_format}}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>IVA:</th>
                                                        <td align="right">{{fondo.impuesto_format}}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Total:</th>
                                                        <td align="right">{{fondo.total_format}}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="salir">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "comprobante-fondo-show",
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
                this.$store.commit('finanzas/comprobante-fondo/SET_FONDO', null);
                return this.$store.dispatch('finanzas/comprobante-fondo/find', {
                    id: this.id,
                    params:{
                        include: ['fondo', 'partidas.concepto', 'concepto']
                    }
                }).then(data => {
                    this.$store.commit('finanzas/comprobante-fondo/SET_FONDO', data);
                })
                    .finally(()=> {
                        this.cargando = false;
                    })
            },
            salir(){
                this.$router.push({name: 'comprobante-fondo'});
            }
        },
        computed: {
            fondo() {
                return this.$store.getters['finanzas/comprobante-fondo/currentFondo']
            },
        }
    }
</script>

<style scoped>

</style>
