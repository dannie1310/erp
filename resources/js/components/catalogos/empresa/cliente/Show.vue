<template>
    <span>
        <button @click="find" type="button" class="btn btn-sm btn-outline-secondary" title="Show" v-if="$root.can('consultar_cliente')">
            <i class="fa fa-eye"></i>
        </button>
         <div class="modal fade" ref="modal" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-eye"></i> Cliente</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row">
                                <div class="table-responsive col-md-12">
                                    <table class="table table-striped">
                                        <tbody>
                                            <tr>
                                                <td class="bg-white"><b>RFC:</b></td>
                                                <td class="bg-white">{{cliente.rfc}}</td>
                                            </tr>
                                            <tr>
                                                <td class="bg-white"><b>Razón Social:</b></td>
                                                <td class="bg-white">{{cliente.razon_social}}</td>
                                            </tr>
                                            <tr>
                                                <td class="bg-white"><b>Tipo:</b></td>
                                                <td class="bg-white">{{cliente.tipo}}</td>
                                            </tr>
                                            <tr>
                                                <td class="bg-white"><b>Porcentaje de Participación:</b></td>
                                                <td class="bg-white">{{cliente.porcentaje_format}}</td>
                                            </tr>
                                            <tr>
                                                <td class="bg-white"><b>Fecha de Registro:</b></td>
                                                <td class="bg-white">{{cliente.fecha_registro_format}}</td>
                                            </tr>
                                            <tr v-if="cliente.usuario_registro">
                                                <td class="bg-white"><b>Usuario que Registró:</b></td>
                                                <td class="bg-white">{{cliente.usuario_registro.nombre}}</td>
                                            </tr>
                                            <tr v-if="cliente.efo && (cliente.efo.estado.id == 2 || cliente.efo.estado.id == 0)">
                                                <td class="bg-white"><b>Estado en el catálogo de EFOS:</b></td>
                                                <td class="bg-white">
                                                    <small class="badge" :class="{'badge-warning': cliente.efo.estado.id == 2, 'badge-danger' : cliente.efo.estado.id == 0 }">
                                                          {{cliente.efo.estado.descripcion}}
                                                    </small>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        </div>
                    </form>
                </div>
            </div>
         </div>
    </span>
</template>

<script>
    export default {
        name: "cliente-show",
        props: ['id'],
        data() {
            return {
                cliente : []
            }
        },
        methods: {
            find() {
                this.$store.commit('cadeco/cliente/SET_CLIENTE', null);
                return this.$store.dispatch('cadeco/cliente/find', {
                    id: this.id,
                    params: {include: ['usuario_registro']}
                }).then(data => {
                    this.$store.commit('cadeco/cliente/SET_CLIENTE', data);
                    this.cliente = data;
                    $(this.$refs.modal).modal('show');
                })
            }
        }
    }
</script>
