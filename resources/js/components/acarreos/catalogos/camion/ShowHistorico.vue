<template>
    <span>
        <button @click="abrirHistorico()" class="btn btn-sm btn-primary float-right">
            <i class="fa fa-clock"></i> Histórico
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"><i class="fa fa-clock"></i> CONSULTAR HISTÓRICO CAMIÓN</h5>
                        <button type="button" class="close" @click="salir" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div v-if="cargando">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="spinner-border text-success" role="status">
                                        <span class="sr-only">Cargando...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else>
                            <div class="col-md-12 table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Economico</th>
                                        <th>Sindicato</th>
                                        <th>Empresa</th>
                                        <th>Placas del Camión</th>
                                        <th>Placas de la Caja</th>
                                        <th>Marca</th>
                                        <th>Modelo</th>
                                        <th>Propietario</th>
                                        <th>Operador</th>
                                        <th>Aseguradora</th>
                                        <th>Poliza de Seguro</th>
                                        <th>Vigencia Seguro</th>
                                        <th>Cubicación Real</th>
                                        <th>Cubicación para Pago</th>
                                        <th>Estatus</th>
                                        <th>Registró</th>
                                        <th>Fecha y Hora registro</th>
                                        <th>Desactivo</th>
                                        <th>Fecha y Hora Desactivación</th>
                                        <th>Motivo de Desactivación</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                     <tr v-for="(historico, i) in historicos">
                                         <td>{{i+1}}</td>
                                         <td>{{historico.economico}}</td>
                                         <td>{{historico.sindicato}}</td>
                                         <td>{{historico.empresa}}</td>
                                         <td>{{historico.placa}}</td>
                                         <td>{{historico.placa_caja}}</td>
                                         <td>{{historico.marca}}</td>
                                         <td>{{historico.modelo}}</td>
                                         <td>{{historico.propietario}}</td>
                                         <td>{{historico.operador}}</td>
                                         <td>{{historico.aseguradora}}</td>
                                         <td>{{historico.poliza_seguro}}</td>
                                         <td>{{historico.vigencia_poliza}}</td>
                                         <td>{{historico.cubicacion_real}}</td>
                                         <td>{{historico.cubicacion_pago}}</td>
                                         <td>{{historico.estado_format}}</td>
                                         <td>{{historico.nombre_registro}}</td>
                                         <td>{{historico.fecha_registro}}</td>
                                         <td>{{historico.nombre_desactivo}}</td>
                                         <td>{{historico.fecha_desactivo}}</td>
                                         <td>{{historico.motivo}}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="salir">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
      </span>
</template>

<script>
    export default {
        name: "camion-show-historico",
        props: ['id', 'historicos'],
        data() {
            return {
                cargando: false
            }
        },
        methods: {
            abrirHistorico()
            {
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
            },
            salir() {
                $(this.$refs.modal).modal('hide');
            },
        }
    }
</script>

<style scoped>

</style>
