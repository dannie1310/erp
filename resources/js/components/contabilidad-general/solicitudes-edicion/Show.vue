<template>
    <span v-if="solicitud">
        <div class="row">
            <div class="col-md-11">
                 <ImpresionPolizas v-bind:id="id"></ImpresionPolizas>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-primary pull-right"  @click="descargar"><i class="fa fa-download"></i>Descargar</button>
            </div>
        </div>
        <br />
        <div  class="row">
            <div class="table-responsive col-12">
                <table class="table table-striped">
                    <tbody>
                        <tr>
                            <td class="bg-gray-light">
                                <b>Folio:</b>
                            </td>
                            <td class="bg-gray-light">
                                {{solicitud.numero_folio_format}}
                            </td>

                            <td class="bg-gray-light"><b>Estado:</b><br> </td>
                            <td class="bg-gray-light">{{solicitud.estado_format}}</td>
                        </tr>
                        <tr>
                            <td class="bg-gray-light">
                                <b>Registró:</b>
                            </td>
                            <td class="bg-gray-light">
                                {{solicitud.usuario_registro}}
                            </td>
                            <td class="bg-gray-light">
                                <b>Fecha y Hora de Registro:</b>
                            </td>
                            <td class="bg-gray-light">
                                {{solicitud.fecha_hora_registro_format}}
                            </td>
                        </tr>
                        <tr v-if="solicitud.usuario_autorizo">
                            <td class="bg-gray-light">
                                <b>Autorizó:</b>
                            </td>
                            <td class="bg-gray-light">
                                {{solicitud.usuario_autorizo}}
                            </td>
                            <td class="bg-gray-light">
                                <b>Fecha y Hora de Autorización:</b>
                            </td>
                            <td class="bg-gray-light">
                                {{solicitud.fecha_hora_autorizacion_format}}
                            </td>
                        </tr>
                        <tr v-if="solicitud.usuario_rechazo">
                            <td class="bg-gray-light">
                                <b>Rechazó:</b>
                            </td>
                            <td class="bg-gray-light">
                                {{solicitud.usuario_rechazo}}
                            </td>
                            <td class="bg-gray-light">
                                <b>Fecha y Hora de Rechazo:</b>
                            </td>
                            <td class="bg-gray-light">
                                {{solicitud.fecha_hora_rechazo_format}}
                            </td>
                        </tr>
                        <tr v-if="solicitud.usuario_aplico">
                            <td class="bg-gray-light">
                                <b>Aplicó:</b>
                            </td>
                            <td class="bg-gray-light">
                                {{solicitud.usuario_aplico}}
                            </td>
                            <td class="bg-gray-light">
                                <b>Fecha y Hora de Aplicación:</b>
                            </td>
                            <td class="bg-gray-light">
                                {{solicitud.fecha_hora_aplicacion_format}}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
            <div class="row" >
                <div class="col-md-12">
                    <h6>-Cantidad de Partidas: {{solicitud.numero_partidas}} -Cantidad de Pólizas: {{solicitud.numero_polizas}} -Cantidad de Movimientos: {{solicitud.numero_movimientos}} -Cantidad de Bases de Datos: {{solicitud.numero_bd}} </h6>
                </div>
            </div>
            <div class="row" >
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th class="index_corto">#</th>
                                    <th class="fecha">Fecha</th>
                                    <th class="fecha">Tipo</th>
                                    <th class="fecha">Folio</th>
                                    <th class="money"></th>
                                    <th>Concepto</th>
                                    <th class="referencia_input">Referencia</th>
                                    <th class="index_corto">#BD</th>
                                    <th class="index_corto">#P</th>
                                    <th class="index_corto">#M</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template v-for="(partida, i) in solicitud.partidas.data">
                                    <tr style="background-color:rgba(0, 0, 0, 0.1)">
                                        <td>{{i+1}}</td>
                                        <td>{{partida.fecha_format}}</td>
                                        <td>{{partida.tipo_format}}</td>
                                        <td>{{partida.folio}}</td>
                                        <td></td>
                                        <td>{{partida.concepto}}</td>
                                        <td>{{partida.referencia}}</td>
                                        <td>{{partida.numero_bd}}</td>
                                        <td>{{partida.numero_polizas}}</td>
                                        <td>{{partida.numero_movimientos}}</td>
                                    </tr>
                                    <tr v-for="(poliza, j) in partida.polizas.data">
                                        <td>
                                            <i :class="poliza.class_estado"></i>
                                        </td>
                                        <td style="text-align: right">{{j+1}}</td>
                                        <td colspan="2">{{poliza.bd_contpaq}}</td>
                                        <td >{{poliza.monto_format}}</td>
                                        <td colspan="4">{{poliza.concepto_original}}</td>
                                        <td>{{poliza.movimientos.data.length}}</td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-secondary pull-right"  @click="regresar"><i class="fa fa-angle-left"></i>Regresar</button>
                    </div>
                </div>
            </div>
        </span>
</template>

<script>
    import ImpresionPolizas from "./partials/ImpresionPolizas";
    export default {
        name: "Show",
        props: ['id'],
        components: {ImpresionPolizas},
        data() {
            return {
                cargando: false,
                descargando: false,
            }
        },
        mounted() {
            this.find()
        },
        methods:{
            find() {
                this.cargando = true;
                this.$store.commit('contabilidadGeneral/solicitud-edicion-poliza/SET_SOLICITUD', null);
                return this.$store.dispatch('contabilidadGeneral/solicitud-edicion-poliza/find', {
                    id: this.id,
                    params: {
                        include: ['partidas.polizas.movimientos'],
                    }
                }).then(data => {
                    this.$store.commit('contabilidadGeneral/solicitud-edicion-poliza/SET_SOLICITUD', data);
                }) .finally(() => {
                    this.cargando = false;
                })
            },
            descargar() {
                this.descargando = true;
                return this.$store.dispatch('contabilidadGeneral/solicitud-edicion-poliza/descargaXLS', {
                    id: this.id
                }).then(data => {
                    this.$emit('success')
                }) .finally(() => {
                    this.descargando = false;
                })
            },
            regresar() {
                this.$router.push({name: 'solicitud-edicion-poliza'});
            },
        },
        computed: {
            solicitud() {
                return this.$store.getters['contabilidadGeneral/solicitud-edicion-poliza/currentSolicitud']
            }
        }
    }
</script>

<style scoped>

</style>
