<template>
    <span v-if="solicitud">
            <br />
        <div  class="row">
            <div class="table-responsive col-12">
                <table class="table table-striped">
                    <tbody>
                        <tr>
                            <td class="bg-gray-light">
                                <b>Folio: </b>{{solicitud.numero_folio_format}}
                            </td>
                            <td class="bg-gray-light">
                                <b>Tipo: </b>{{solicitud.tipo_solicitud}}
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
        <span v-if="solicitud.id_tipo ==1">

            <div class="row" >
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th class="index">#</th>
                                    <th class="money">Fecha</th>
                                    <th class="fecha">Tipo</th>
                                    <th class="fecha">Folio</th>
                                    <th class="money"></th>
                                    <th class="referencia_input">Referencia</th>
                                    <th>Concepto</th>
                                    <th class="index_corto">#BD</th>
                                    <th class="index_corto">#P</th>
                                    <th class="index_corto">#M</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template v-for="(partida, i) in solicitud.partidas_activas.data">
                                    <tr style="background-color:rgba(0, 0, 0, 0.1)">
                                        <td>{{i+1}}</td>
                                        <td>{{partida.fecha_format}}</td>
                                        <td>{{partida.tipo_format}}</td>
                                        <td>{{partida.folio}}</td>
                                        <td></td>
                                        <td>{{partida.referencia}}</td>
                                        <td>{{partida.concepto}}</td>
                                        <td>{{partida.numero_bd}}</td>
                                        <td>{{partida.numero_polizas}}</td>
                                        <td>{{partida.numero_movimientos}}</td>
                                    </tr>
                                    <tr v-for="(poliza, j) in partida.polizas_autorizadas.data">
                                        <td>{{j+1}}</td>
                                        <td style="text-align: right" > {{poliza.monto_format}}</td>
                                        <td colspan="2">{{poliza.bd_contpaq}}</td>
                                        <td colspan="2">{{poliza.empresa_contpaq}}</td>
                                        <td colspan="3">{{poliza.concepto_original}}</td>
                                        <td>{{poliza.numero_movimientos}}</td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-secondary pull-right"  @click="regresar"><i class="fa fa-angle-left"></i>Regresar</button>
                        <button type="button" class="btn btn-success pull-right"  @click="aplicar" v-if="solicitud.estado == 1"><i class="fa fa-arrow-alt-circle-down"></i>Aplicar</button>
                    </div>
                </div>
            </div>
        </span>
        <span v-else-if="solicitud.id_tipo==2">
            <div class="row"  >
                <div class="col-md-12">
                    <h6>-Base de Datos: {{solicitud.base_datos}} -Cantidad de Partidas: {{solicitud.numero_partidas}} -Cantidad de Pólizas: {{solicitud.numero_polizas}} -Cantidad de Movimientos: {{solicitud.numero_movimientos}} </h6>
                </div>
            </div>
            <div class="row"  >
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr style="background-color:rgba(0, 0, 0, 0.1)">
                                    <th class="index_corto">#</th>
                                    <th class="fecha_hora">Póliza</th>
                                    <th >Núm. Movto.</th>
                                    <th >Campo</th>
                                    <th >Valor Original</th>
                                    <th >Valor Propuesto</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template v-for="(partida, i) in solicitud.partidas_activas.data">
                                    <tr >
                                        <td>{{i+1}}</td>
                                        <td>{{partida.diferencia.identificador_poliza}}</td>
                                        <td>{{partida.diferencia.numero_movimiento}}</td>
                                        <td>{{partida.diferencia.campo}}</td>
                                        <td>{{partida.diferencia.valor_a}}</td>
                                        <td>{{partida.diferencia.valor_b}}</td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-secondary pull-right"  @click="regresar"><i class="fa fa-angle-left"></i>Regresar</button>
                        <button type="button" class="btn btn-success pull-right"  @click="aplicar" v-if="solicitud.estado == 1"><i class="fa fa-arrow-alt-circle-down"></i>Aplicar</button>
                    </div>
                </div>
            </div>
        </span>
        <span v-else-if="solicitud.id_tipo==3">
            <div class="row"  >
                <div class="col-md-12">
                    <h6>-Base de Datos: {{solicitud.base_datos}} -Cantidad de Partidas: {{solicitud.numero_partidas}} -Cantidad de Pólizas: {{solicitud.numero_polizas}} -Cantidad de Movimientos: {{solicitud.numero_movimientos}} </h6>
                </div>
            </div>
            <div class="row"  >
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr style="background-color:rgba(0, 0, 0, 0.1)">
                                    <th class="index_corto">#</th>
                                    <th colspan="10" >Póliza</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template v-for="(partida, i) in solicitud.partidas_activas.data">
                                    <tr style="background-color: #555555; color: #ffffff">
                                        <td>{{i+1}}</td>
                                        <td colspan="10">{{partida.diferencia.identificador_poliza}}</td>
                                    </tr>
                                    <tr >
                                        <td></td>
                                        <td colspan="5" style="text-align: center">Orden Original</td>
                                        <td colspan="5" style="text-align: center">Orden Propuesto</td>
                                    </tr>
                                    <tr >
                                        <td></td>
                                        <td>No. Movto.</td>
                                        <td>Código</td>
                                        <td>Cuenta</td>
                                        <td>Cargo</td>
                                        <td>Abono</td>

                                        <td>No. Movto.</td>
                                        <td>Código</td>
                                        <td>Cuenta</td>
                                        <td>Cargo</td>
                                        <td>Abono</td>
                                    </tr>
                                    <tr v-for="(movimiento, j) in partida.diferencia.movimientos">
                                        <td></td>
                                        <td>{{ movimiento.no_movto_a }}</td>
                                        <td>{{ movimiento.codigo_a }}</td>
                                        <td>{{ movimiento.cuenta_a }}</td>
                                        <td style="text-align: right">{{ movimiento.cargo_a }}</td>
                                        <td style="text-align: right">{{ movimiento.abono_a }}</td>

                                        <td>{{ movimiento.no_movto_b }}</td>
                                        <td>{{ movimiento.codigo_b }}</td>
                                        <td>{{ movimiento.cuenta_b }}</td>
                                        <td style="text-align: right">{{ movimiento.cargo_b }}</td>
                                        <td style="text-align: right">{{ movimiento.abono_b }}</td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-secondary pull-right"  @click="regresar"><i class="fa fa-angle-left"></i>Regresar</button>
                        <button type="button" class="btn btn-success pull-right"  @click="aplicar" v-if="solicitud.estado == 1"><i class="fa fa-arrow-alt-circle-down"></i>Aplicar</button>
                    </div>
                </div>
            </div>
        </span>
        <span v-else-if="solicitud.id_tipo==4">
            <div class="row"  >
                <div class="col-md-12">
                    <h6>-Base de Datos: {{solicitud.base_datos}} -Cantidad de Cuentas: {{solicitud.numero_cuentas}}  </h6>
                </div>
            </div>
            <div class="row"  >
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr style="background-color:rgba(0, 0, 0, 0.1)">
                                    <th class="index_corto">#</th>
                                    <th class="fecha_hora">Código Cuenta</th>
                                    <th >Descripción Original</th>
                                    <th >Descripción Propuesta</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template v-for="(partida, i) in solicitud.partidas_activas.data">
                                    <tr >
                                        <td>{{i+1}}</td>
                                        <td>{{partida.diferencia.codigo_cuenta}}</td>
                                        <td>{{partida.diferencia.valor_a}}</td>
                                        <td>{{partida.diferencia.valor_b}}</td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-secondary pull-right"  @click="regresar"><i class="fa fa-angle-left"></i>Regresar</button>
                        <button type="button" class="btn btn-success pull-right"  @click="aplicar" v-if="solicitud.estado == 1"><i class="fa fa-arrow-alt-circle-down"></i>Aplicar</button>
                    </div>
                </div>
            </div>
        </span>
    </span>
</template>

<script>
    export default {
        name: "Show",
        props: ['id'],
        data() {
            return {
                cargando: false,
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
                        include: ['partidas_activas.polizas_autorizadas', 'partidas_activas.diferencia'],
                    }
                }).then(data => {
                    this.$store.commit('contabilidadGeneral/solicitud-edicion-poliza/SET_SOLICITUD', data);
                }) .finally(() => {
                    this.cargando = false;
                })
            },
            regresar() {
                this.$router.push({name: 'solicitud-edicion-poliza'});
            },
            aplicar() {
                let self = this;
                return this.$store.dispatch('contabilidadGeneral/solicitud-edicion-poliza/aplicar', {
                    id: this.id,
                }).then(data => {
                    this.find();
                })
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