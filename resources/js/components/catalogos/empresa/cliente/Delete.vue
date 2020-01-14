<template>
    <span>
        <button @click="find" type="button" class="btn btn-sm btn-outline-danger" title="delete" v-if="$root.can('eliminar_cliente')">
            <i class="fa fa-trash"></i>
        </button>
         <div class="modal fade" ref="modal" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-eye"></i>Eliminar Cliente</h5>
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
                                            <tr v-if="cliente.efo && (cliente.efo.estado == 2 || cliente.efo.estado == 0)">
                                                <td class="bg-white"><b>Estado en el catálogo de EFOS:</b></td>
                                                <td class="bg-white">
                                                    <small class="badge" :class="{'badge-warning': cliente.efo.estado == 2, 'badge-danger' : cliente.efo.estado == 0 }">
                                                          {{cliente.efo.ctg_estado.descripcion}}
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
                            <button type="button" class="btn btn-danger" @click="eliminar">Eliminar</button>
                        </div>
                    </form>
                </div>
            </div>
         </div>
    </span>
</template>

<script>
    export default {
        name: "cliente-elete",
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
                    params: {include: []}
                }).then(data => {
                    this.$store.commit('cadeco/cliente/SET_CLIENTE', data);
                    this.cliente = data;
                    $(this.$refs.modal).modal('show');
                })
            },
            eliminar() {
                this.cargando = true;
                return this.$store.dispatch('cadeco/cliente/eliminar', {
                    id: this.id
                })
                    .then(data => {
                        this.$store.commit('cadeco/cliente/DELETE_CLIENTE', {id: this.id})
                        $(this.$refs.modal).modal('hide');
                        this.$store.dispatch('cadeco/cliente/paginate', {
                            params: {
                                sort: 'razon_social', order: 'desc'
                            }
                        })
                            .then(data => {
                                this.$store.commit('cadeco/cliente/SET_CLIENTES', data.data);
                                this.$store.commit('cadeco/cliente/SET_META', data.meta);
                            })
                    })
                    .finally( ()=>{
                        this.cargando = false;
                    });
            },
        }
    }
</script>

<style scoped>

</style>
