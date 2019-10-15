<template>
    <div class="row">
        <div class="col-12">
            <div class="invoice p-3 mb-3">
                <div class="row">
                    <div class="col-12">
                        <h4> <i class="fa fa-list-alt"></i> CARGA MASIVA DE LAYOUT DE PAGOS </h4>
                    </div>
                </div>
                <div v-if="layout" class="row">
                    <div class="table-responsive col-12">
                        <table class="table table-striped">
                            <tbody>
                            <tr>
                                <td class="bg-gray-light">
                                    <b>Folio:</b>
                                </td>
                                <td class="bg-gray-light">
                                    {{layout.id}}
                                </td>

                                <td class="bg-gray-light">
                                    <b>Monto Layout:</b>
                                </td>
                                <td class="bg-gray-light text-right">
                                    {{layout.monto_layout_pagos}}
                                </td>
                                <td class="bg-gray-light" colspan="2"><b>Estado:</b><br> </td>
                                <td class="bg-gray-light"><estatus-label :value="layout.estado"></estatus-label></td>
                            </tr>
                            <tr>
                                <td colspan="2" class="bg-gray-light">
                                    <b>Registró:</b>
                                </td>
                                <td colspan="2" class="bg-gray-light">
                                    {{layout.usuario.nombre}}
                                </td>
                                <td colspan="2" class="bg-gray-light">
                                    <b>Fecha de Registro:</b>
                                </td>
                                <td colspan="2" class="bg-gray-light">
                                    {{layout.fecha_registro}}
                                </td>
                            </tr>
                            <tr v-if="layout.usuario_autorizo">
                                <td colspan="2" class="bg-gray-light">
                                    <b>Autorizó:</b>
                                </td>
                                <td colspan="2" class="bg-gray-light">
                                    {{layout.usuario_autorizo.nombre}}
                                </td>
                                <td colspan="2" class="bg-gray-light">
                                    <b>Fecha de Autorización:</b>
                                </td>
                                <td colspan="2" class="bg-gray-light">
                                    {{layout.fecha_autorizizacion}}
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
                <h5><i class="fa fa-list" style="padding-right: 3px"></i>Partidas de la Carga de layout</h5>
                <div v-if="layout" class="row">
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
                                <td v-else-if="doc.solicitud">{{doc.solicitud.observaciones}}</td>
                                <td v-if="doc.factura">{{doc.factura.empresa.razon_social}}</td>
                                <td v-else-if="doc.solicitud.empresa">{{doc.solicitud.empresa.razon_social}}</td>
                                <td v-else-if="doc.solicitud.fondo">{{doc.solicitud.fondo.descripcion}}</td>
                                <td>{{doc.monto_transaccion_format}}</td>
                                <td>{{doc.cuenta_cargo}}</td>
                                <td>{{doc.fecha_pago}}</td>
                                <td>{{doc.tipo_cambio}}</td>
                                <td>{{doc.monto_pagado_format}}</td>
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
            </div>
        </div>
    </div>
</template>

<script>
    import EstatusLabel from "./partials/CargaMasivaEstatus";
    import Index from "./Index";
    export default {
        name: "pago-masivo-show",
        components: {EstatusLabel},
        props: ['id'],
        data() {
            return {
                cargando: false,
            }
        },
        mounted() {
            this.$Progress.start();
            this.find()
                .finally(() => {
                    this.$Progress.finish();
                })
        },
        methods: {
            find() {
                this.cargando = true;
                this.$store.commit('finanzas/carga-masiva-pago/SET_LAYOUT', null);
                return this.$store.dispatch('finanzas/carga-masiva-pago/find', {
                    id: this.id,
                    params: {
                        include: ['partidas.solicitud.empresa','partidas.solicitud.fondo','partidas.factura.empresa','usuario','usuario_autorizo','estado','partidas.moneda'],
                    }
                }).then(data => {
                    this.$store.commit('finanzas/carga-masiva-pago/SET_LAYOUT', data);
                }) .finally(() => {
                    this.cargando = false;
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
