<template>
    <span>
        <button @click="find" type="button" class="btn btn-sm btn-outline-secondary" title="Show" v-if="$root.can('consultar_cliente')">
            <i class="fa fa-eye"></i>
        </button>
         <div class="modal fade" ref="modal" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
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
                                                <td class="bg-gray-light"><b>RFC:</b></td>
                                                <td class="bg-gray-light" colspan="1">{{cliente.rfc}}</td>
                                                <td class="bg-gray-light"><b>Razón Social:</b></td>
                                                <td class="bg-gray-light">{{cliente.razon_social}}</td>
                                            </tr>
                                            <tr>
                                                <td class="bg-gray-light"><b>Tipo:</b></td>
                                                <td class="bg-gray-light">{{cliente.tipo}}</td>
                                                <td class="bg-gray-light"><b>Porcentaje de Participación:</b></td>
                                                <td class="bg-gray-light">{{cliente.porcentaje_format}}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row"  v-if="cliente.efo">
                                <div class="col-md-2">
                                    <b>Estado en el catálogo de EFOS:</b>
                                </div>
                                <div class="col-sm-10">
                                    {{cliente.efo.ctg_estado.descripcion}}
                                </div>
                            </div>
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
                    params: {include: []}
                }).then(data => {
                    this.$store.commit('cadeco/cliente/SET_CLIENTE', data);
                    this.cliente = data;
                    $(this.$refs.modal).modal('show');
                })
            }
        }
    }
</script>

<style scoped>

</style>
    }
</script>

<style scoped>

</style>
