<template>
    <span v-if="solicitud_edit">
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
                                {{solicitud_edit.numero_folio_format}}
                            </td>

                            <td class="bg-gray-light"><b>Estado:</b><br> </td>
                            <td class="bg-gray-light">{{solicitud_edit.estado_format}}</td>
                        </tr>
                        <tr>
                            <td class="bg-gray-light">
                                <b>Registró:</b>
                            </td>
                            <td class="bg-gray-light">
                                {{solicitud_edit.usuario_registro}}
                            </td>
                            <td class="bg-gray-light">
                                <b>Fecha y Hora de Registro:</b>
                            </td>
                            <td class="bg-gray-light">
                                {{solicitud_edit.fecha_hora_registro_format}}
                            </td>
                        </tr>
                        <tr v-if="solicitud_edit.usuario_autorizo">
                            <td class="bg-gray-light">
                                <b>Autorizó:</b>
                            </td>
                            <td class="bg-gray-light">
                                {{solicitud_edit.usuario_autorizo}}
                            </td>
                            <td class="bg-gray-light">
                                <b>Fecha y Hora de Autorización:</b>
                            </td>
                            <td class="bg-gray-light">
                                {{solicitud_edit.fecha_hora_autorizacion_format}}
                            </td>
                        </tr>
                        <tr v-if="solicitud_edit.usuario_rechazo">
                            <td class="bg-gray-light">
                                <b>Rechazó:</b>
                            </td>
                            <td class="bg-gray-light">
                                {{solicitud_edit.usuario_rechazo}}
                            </td>
                            <td class="bg-gray-light">
                                <b>Fecha y Hora de Rechazo:</b>
                            </td>
                            <td class="bg-gray-light">
                                {{solicitud_edit.fecha_hora_rechazo_format}}
                            </td>
                        </tr>
                        <tr v-if="solicitud_edit.usuario_aplico">
                            <td class="bg-gray-light">
                                <b>Aplicó:</b>
                            </td>
                            <td class="bg-gray-light">
                                {{solicitud_edit.usuario_aplico}}
                            </td>
                            <td class="bg-gray-light">
                                <b>Fecha y Hora de Aplicación:</b>
                            </td>
                            <td class="bg-gray-light">
                                {{solicitud_edit.fecha_hora_aplicacion_format}}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
            <div class="row" >
                <div class="col-md-12">
                    <h6>-Cantidad de Partidas: {{solicitud_edit.numero_partidas}} -Cantidad de Pólizas: {{solicitud_edit.numero_polizas}} -Cantidad de Movimientos: {{solicitud_edit.numero_movimientos}} -Cantidad de Bases de Datos: {{solicitud_edit.numero_bd}} </h6>
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
                                    <th class="money">Importe</th>
                                    <th>Concepto</th>
                                    <th class="referencia_input">Referencia</th>
                                    <th class="index_corto">#BD</th>
                                    <th class="index_corto">#P</th>
                                    <th class="index_corto">#M</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template v-for="(partida, i) in solicitud_edit.partidas.data">
                                    <tr style="background-color:rgba(0, 0, 0, 0.1)">
                                        <td>{{i+1}}</td>
                                        <td>{{partida.fecha_format}}</td>
                                        <td>{{partida.tipo_format}}</td>
                                        <td>{{partida.folio}}</td>
                                        <td>{{partida.importe_format}}</td>
                                        <td>{{partida.concepto}}</td>
                                        <td>{{partida.referencia}}</td>
                                        <td>{{partida.numero_bd}}</td>
                                        <td>{{partida.numero_polizas}}</td>
                                        <td>{{partida.numero_movimientos}}</td>
                                    </tr>
                                    <tr v-for="(poliza, j) in partida.polizas.data">
                                        <td>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" :id="`cumplido${i}-${j}`"  v-model="poliza.estado">
                                                <label :for="`cumplido${i}-${j}`" class="custom-control-label" v-model="poliza.estado"></label>
                                            </div>
                                        </td>
                                        <td style="text-align: right">{{j+1}}</td>
                                        <td colspan="2">{{poliza.bd_contpaq}}</td>
                                        <td colspan="5">{{poliza.concepto_original}}</td>
                                        <td>{{poliza.movimientos.data.length}}</td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
            <button type="button" class="btn btn-secondary pull-right"  @click="regresar"><i class="fa fa-angle-left"></i>Regresar</button>
            <button type="button" class="btn btn-success pull-right"  @click="autorizar" v-if="solicitud_edit.estado == 0"><i class="fa fa-check"></i>Autorizar</button>
            <button type="button" class="btn btn-danger pull-right"  @click="rechazar" v-if="solicitud_edit.estado == 0"><i class="fa fa-ban"></i>Rechazar</button>
        </span>
</template>

<script>
    export default {
        name: "Show",
        props: ['id'],
        data() {
            return {
                cargando : false,
                solicitud_edit : false
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
                    this.solicitud_edit = data;
                    //this.$store.commit('contabilidadGeneral/solicitud-edicion-poliza/SET_SOLICITUD', data);

                }) .finally(() => {
                    this.cargando = false;
                })
            },
            regresar() {
                this.$router.push({name: 'solicitud-edicion-poliza'});
            },
            autorizar() {
                let self = this;
                return this.$store.dispatch('contabilidadGeneral/solicitud-edicion-poliza/autorizar', {
                    id: this.id,
                    partidas:self.solicitud_edit.partidas.data
                }).then(data => {
                    this.find();
                })
            },
            rechazar() {
                let self = this;
                return this.$store.dispatch('contabilidadGeneral/solicitud-edicion-poliza/rechazar', {
                    id: this.id,
                }).then(data => {
                    this.find();
                })
            },
        },
    }
</script>

<style scoped>

</style>