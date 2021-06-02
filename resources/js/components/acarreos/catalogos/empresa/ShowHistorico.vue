<template>
    <span>
        <button @click="abrirHistorico()" class="btn btn-sm btn-primary float-right">
            <i class="fa fa-clock"></i> Histórico
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"><i class="fa fa-clock"></i> CONSULTAR HISTÓRICO EMPRESA</h5>
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
                                        <th scope="col">Razón Social</th>
                                        <th scope="col">RFC</th>
                                        <th scope="col">Estatus</th>
                                        <th scope="col">Registró</th>
                                        <th scope="col">Fecha y Hora Registro</th>
                                        <th scope="col">Desactivó</th>
                                        <th scope="col">Fecha y Hora Desactivación</th>
                                        <th scope="col">Motivo de Desactivación</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                     <tr v-for="(historico, i) in historicos">
                                         <td>{{i+1}}</td>
                                         <td>{{historico.razon_social}}</td>
                                         <td>{{historico.rfc}}</td>
                                         <td><span class="badge" :style="{'background-color': historico.estado_color}">{{ historico.estado_format }}</span></td>
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
        name: "empresa-show-historico",
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
