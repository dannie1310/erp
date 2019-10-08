<template>
    <div class="row">
        <div class="col-12">
            <div class="invoice p-3 mb-3">
                <div class="row">
                    <div class="col-12">
                        <h4> <i class="fa fa-list-alt"></i> AUTORIZACIÓN DE LAYOUTS REGISTRADOS</h4>
                    </div>
                </div>
                <!--                <div class="modal-body">-->
                <div class="row">
                    <div class="table-responsive col-12">
                        <table class="table table-striped">
                            <tbody>
                            <tr>
                                <td class="bg-gray-light">
                                    <b>Folio:</b>
                                </td>
                                <td class="bg-gray-light">
                                    {{ layout.id }}
                                </td>
                                <td class="bg-gray-light">
                                    <!--                                    $ 4000554-->
                                </td>
                                <td class="bg-gray-light">
                                    <b>Monto Total de Layout:</b>
                                </td>
                                <td class="bg-gray-light">
                                    $&nbsp; {{(parseFloat(layout.monto)).formatMoney(2,'.',',')}}
                                </td>
                                <td class="bg-gray-light">
<!--                                    <b>Monto de Está Dispersion:</b>-->
                                </td>

                                <td class="bg-gray-light"><b>Estado:</b><br> </td>

                                <td class="bg-gray-light"><estatus-label :value="layout.estado"></estatus-label></td>
                            </tr>


                            <tr>
                                <td colspan="2" class="bg-gray-light">
                                    <b>Registró:</b>
                                </td>
                                <td colspan="2" class="bg-gray-light">
                                 {{ layout.usuario.nombre }}
                                </td>
                                <td colspan="2" class="bg-gray-light">
                                    <b>Fecha de Registro de Carga:</b>
                                </td>
                                <td colspan="2" class="bg-gray-light">
                                    {{ layout.fecha_registro }}
                                </td>
                            </tr>
                            <tr v-if="layout.usuario_autorizo">

                                <td colspan="2" class="bg-gray-light">
                                    <b>Autorizó:</b>
                                </td>
                                <td colspan="2" class="bg-gray-light">
                                    {{ layout.usuario_autorizo.nombre }}
                                </td>
                                <td colspan="2" class="bg-gray-light">
                                    <b>Fecha de Autorización:</b>
                                </td>
                                <td colspan="2" class="bg-gray-light">
                                    {{ layout.fecha_autorizacion }}
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
                <h5><i class="fa fa-list" style="padding-right: 3px"></i>Partidas del Layout</h5>
                <div v-if="" class="row">
                    <div  class="col-12 table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Concepto</th>
                                <th>Beneficiario</th>
                                <th>Importe Documento</th>
                                <th>Cuenta Cargo</th>
                                <th>Fecha Pago</th>
                                <th>Tipo Cambio</th>
                                <th>Importe Pagado</th>
                                <th>Referencia Pago</th>
                                <th>Estado</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(doc, i) in layout.partidas.data">
                                <td>{{i+1}}</td>
                                <td v-if="doc.factura">{{doc.factura.observaciones}}</td>
                                <td v-else-if="doc.solicitud_pago_anticipado">{{doc.solicitud_pago_anticipado.observaciones}}</td>
                                <td v-if="doc.factura">{{doc.factura.empresa.razon_social}}</td>
                                <td v-else-if="doc.solicitud_pago_anticipado">{{doc.solicitud_pago_anticipado.empresa.razon_social}}</td>
                                <td>{{doc.monto_transaccion_format}}</td>
                                <td>{{doc.cuenta_cargo}}</td>
                                <td>{{doc.fecha_pago}}</td>
                                <td>{{doc.tipo_cambio}}</td>
                                <td>{{doc.monto_transaccion_format}}</td>
                                <td>{{doc.referencia_pago}}</td>
                                <td v-if="doc.id_transaccion_pago===null"><small class="badge-primary">Aplicado</small></td>
                                <td v-else><small class="badge-success">Pagado</small></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                                </div>

                <div class="modal-footer">
                    <button  type="button" class="btn btn-secondary pull-right" @click="index" >Cerrar</button>
                    <div>
                        <button v-if="layout.estado.estado==0" @click="autorizar" title="Autorizar" class="btn btn-primary pull-right">Autorizar</button>
                    </div>
                </div>
            </div>
        </div>

</template>

<script>
    import EstatusLabel from "./partials/CargaMasivaEstatus";
    export default {
        name: "Autorizar",
        components: {EstatusLabel},
        props: ['value'],
        data() {
            return {
                id:'',
            }
        },
        mounted() {
            this.id = this.$route.params.id;
            this.find()

        },
        methods: {
            find() {
                this.$store.commit('finanzas/carga-masiva-pago/SET_LAYOUT', null);
                return this.$store.dispatch('finanzas/carga-masiva-pago/find', {
                    params: { include: ['usuario', 'usuario_autorizo', 'estado', 'partidas','partidas.solicitud_pago_anticipado.empresa','partidas.factura.empresa', 'partidas.moneda']},
                    id: this.id
                }).then(data => {
                    this.$store.commit('finanzas/carga-masiva-pago/SET_LAYOUT', data);
                })
            },
            autorizar() {
                return this.$store.dispatch('finanzas/carga-masiva-pago/autorizar', {
                    id: this.id
                }).then(data => {
                   this.find();
                   this.layout();
                })
            },
            index(){
                this.$router.push({name: 'carga-masiva'});
            }

        },
        computed: {
            layout() {
                return this.$store.getters['finanzas/carga-masiva-pago/currentLayout']
            }

        }
    }
</script>

<style scoped>

</style>
|
