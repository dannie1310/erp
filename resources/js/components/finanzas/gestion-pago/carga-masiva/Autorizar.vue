<template>
    <div class="row">
        <div class="col-12">
            <div class="invoice p-3 mb-3">
                <div class="row">
                    <div class="col-12">
                        <h4> <i class="fa fa-check-circle"></i> AUTORIZACIÓN DE LAYOUTS REGISTRADOS</h4>
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
                                    {{layout.monto_format}}
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
                                    <b>Fecha de Carga:</b>
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
                                <th>Documento</th>
                                <th>Fecha</th>
                                <th>Vencto.</th>
                                <th>Moneda</th>
                                <th>Importe</th>
                                <th>Saldo</th>
                                <th>Beneficiario</th>
                                <th>Cuenta Cargo</th>
                                <th>Fecha Pago</th>
                                <th>Referencia Pago</th>
                                <th>Monto Pagado<br>(Moneda Documento)</th>
                                <th>Tipo Cambio</th>
                                <th>Monto Pagado<br>(Moneda Cuenta)</th>
                                <th>Estado</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(doc, i) in layout.partidas.data">
                                <td>{{i+1}}</td>
                                <td v-if="doc.factura">{{doc.factura.referencia}}</td>
                                <td v-else-if="doc.solicitud">{{doc.solicitud.referencia}}</td>
                                <td v-if="doc.factura">{{doc.factura.fecha_format}}</td>
                                <td v-else-if="doc.solicitud">{{doc.solicitud.fecha_format}}</td>
                                <td v-if="doc.factura">{{doc.factura.vencimiento_format}}</td>
                                <td v-else-if="doc.solicitud">{{doc.solicitud.vencimiento_format}}</td>
                                <td v-if="doc.factura">{{doc.factura.moneda}}</td>
                                <td v-else-if="doc.solicitud">{{doc.solicitud.moneda}}</td>
                                <td v-if="doc.factura" style="text-align: right">{{doc.factura.monto_format}}</td>
                                <td v-else-if="doc.solicitud" style="text-align: right">{{doc.solicitud.monto_format}}</td>
                                <td v-if="doc.factura" style="text-align: right">{{doc.factura.saldo_format}}</td>
                                <td v-else-if="doc.solicitud" style="text-align: right">{{doc.solicitud.saldo_format}}</td>
                                <td v-if="doc.factura">{{doc.factura.empresa.razon_social}}</td>
                                <td v-else-if="doc.solicitud.empresa">{{doc.solicitud.empresa.razon_social}}</td>
                                <td v-else-if="doc.solicitud.fondo">{{doc.solicitud.fondo.descripcion}}</td>
                                <td>{{doc.cuenta_cargo}}</td>
                                <td>{{doc.fecha_pago_format}}</td>
                                <td>{{doc.referencia_pago}}</td>
                                <td style="text-align: right">{{doc.monto_pagado_documento_format}}</td>
                                <td style="text-align: right">{{doc.tipo_cambio}}</td>
                                <td style="text-align: right">{{doc.monto_pagado_format}}</td>
                                <td v-if="doc.id_transaccion_pago===null">
                                    <small class="badge badge-primary">Por Autorizar</small>
                                </td>
                                <td v-else>
                                    <small class="badge badge-success">Pagado</small>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                                </div>

                <div class="modal-footer">
                    <button  type="button" class="btn btn-secondary float-right" @click="index" >Cerrar</button>
                    <div>
                        <button v-if="layout.estado.estado==0" @click="autorizar" title="Autorizar" class="btn btn-primary float-right">Autorizar</button>
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
                    params: { include: ['partidas.solicitud.fondo','usuario', 'usuario_autorizo', 'estado', 'partidas','partidas.solicitud.empresa','partidas.factura','partidas.factura.empresa', 'partidas.moneda']},
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
