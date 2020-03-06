<template>
    <span>
        <button @click="find" type="button" class="btn btn-sm btn-outline-danger" title="Eliminar" v-if="$root.can('eliminar_destajista')">
            <i class="fa fa-trash"></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">ELIMINAR DESTAJISTA</h5>
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
                                                <td class="bg-white">{{destajista.rfc}}</td>
                                            </tr>
                                            <tr>
                                                <td class="bg-white"><b>Razón Social:</b></td>
                                                <td class="bg-white">{{destajista.razon_social}}</td>
                                            </tr>
                                            <tr>
                                                <td class="bg-white"><b>Condición de Pago (días):</b></td>
                                                <td class="bg-white">{{destajista.dias_credito}}</td>
                                            </tr>
                                            <tr>
                                                <td class="bg-white"><b>Fecha de Registro:</b></td>
                                                <td class="bg-white">{{destajista.fecha_registro_format}}</td>
                                            </tr>
                                            <tr v-if="destajista.usuario_registro">
                                                <td class="bg-white"><b>Usuario que Registró:</b></td>
                                                <td class="bg-white">{{destajista.usuario_registro.nombre}}</td>
                                            </tr>
                                            <tr v-if="destajista.efo && (destajista.efo.estado.id == 2 || destajista.efo.estado.id == 0)">
                                                <td class="bg-white"><b>Estado en el catálogo de EFOS:</b></td>
                                                <td class="bg-white">
                                                    <small class="badge" :class="{'badge-warning': destajista.efo.estado.id == 2, 'badge-danger' : destajista.efo.estado.id == 0 }">
                                                          {{destajista.efo.estado.descripcion}}
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
        name: "destajista-delete",
        props: ['id'],
        data() {
            return {
                destajista : []
            }
        },
        methods: {
            find() {
                this.$store.commit('cadeco/destajista/SET_DESTAJISTA', null);
                return this.$store.dispatch('cadeco/destajista/find', {
                    id: this.id,
                    params: {include: ['usuario_registro']}
                }).then(data => {
                    this.$store.commit('cadeco/destajista/SET_DESTAJISTA', data);
                    this.destajista = data;
                    $(this.$refs.modal).appendTo('body')
                    $(this.$refs.modal).modal('show');
                })
            },
            eliminar() {
                this.cargando = true;
                return this.$store.dispatch('cadeco/destajista/eliminar', {
                    id: this.id
                })
                    .then(data => {
                        this.$store.commit('cadeco/destajista/DELETE_DESTAJISTA', {id: this.id})
                        $(this.$refs.modal).modal('hide');
                        this.$store.dispatch('cadeco/destajista/paginate', {
                            params: {
                                sort: 'id_empresa', order: 'desc'
                            }
                        })
                            .then(data => {
                                this.$store.commit('cadeco/destajista/SET_DESTAJISTAS', data.data);
                                this.$store.commit('cadeco/destajista/SET_META', data.meta);
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
