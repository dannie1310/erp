<template>
    <span>
        <button @click="abrirHistorico()" class="btn btn btn-primary float-right">
            <i class="fa fa-clock"></i> Histórico
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"><i class="fa fa-clock"></i> CONSULTAR HISTÓRICO ORIGEN</h5>
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
                                        <th scope="col">#</th>
                                        <th scope="col">Clave</th>
                                        <th scope="col">Tipo</th>
                                        <th scope="col">Descripción</th>
                                        <th scope="col">Estatus</th>
                                        <th scope="col">Tipo de Origen</th>
                                        <th scope="col">Registró</th>
                                        <th scope="col">Fecha y Hora registro</th>
                                        <th scope="col">Desactivo</th>
                                        <th scope="col">Fecha y Hora Desactivación</th>
                                        <th scope="col">Motivo de Desactivación</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                     <tr v-for="(historico, i) in historicos">
                                         <td>{{i+1}}</td>
                                         <td>{{historico.clave_format}}</td>
                                         <td>{{historico.tipo}}</td>
                                         <td>{{historico.descripcion}}</td>
                                         <td>{{historico.estado_format}}</td>
                                         <td>{{historico.tipo_origen}}</td>
                                         <td>{{historico.registro}}</td>
                                         <td>{{historico.fecha_registro_format}}</td>
                                         <td>{{historico.desactivo}}</td>
                                         <td>{{historico.fecha_desactivo_format}}</td>
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
        name: "tiro-show-historico",
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
