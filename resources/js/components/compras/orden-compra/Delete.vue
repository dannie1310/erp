<template>
    <span>
        <button @click="init" type="button" class="btn btn-sm btn-outline-danger" title="Eliminar">
            <i class="fa fa-trash"></i>
        </button>
         <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-sn" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-trash"></i> ELIMINAR ORDEN DE COMPRA</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row error-content">
                                            <label for="motivo" class="col-sm-2 col-form-label">Motivo:</label>
                                        <div class="col-sm-10">
                                            <textarea
                                                name="motivo"
                                                id="motivo"
                                                class="form-control"
                                                v-model="motivo"
                                                v-validate="{required: true}"
                                                data-vv-as="Motivo"
                                                :class="{'is-invalid': errors.has('motivo')}"
                                            ></textarea>
                                                <div class="error-label" v-show="errors.has('motivo')">{{ errors.first('motivo') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                <i class="fa fa-times-circle"></i>
                                Cerrar</button>
                            <button type="button" class="btn btn-danger" @click="eliminar"  :disabled="motivo == ''">
                                <i class="fa fa-trash"></i>
                                Eliminar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
    </span>
</template>

<script>
    export default {
        name: "orden-compra-delete",
        props: ['id'],
        data() {
            return {
                motivo: '',
                requisicion: ''
            }
        },
        methods: {
            init(){
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
            },
            eliminar() {
                this.cargando = true;
                return this.$store.dispatch('compras/orden-compra/eliminar', {
                    id: this.id,
                    params: {data: this.$data.motivo}
                })
                    .then(data => {
                        this.motivo = ''
                        this.$store.commit('compras/orden-compra/DELETE_ORDEN', {id: this.id})
                        $(this.$refs.modal).modal('hide');
                        this.$store.dispatch('compras/orden-compra/paginate', {
                            params: {
                                scope: 'areasCompradorasAsignadas', include: ['solicitud','empresa'], sort: 'id_transaccion', order: 'desc'
                            }
                        })
                            .then(data => {
                                this.$store.commit('compras/orden-compra/SET_ORDENES', data.data);
                                this.$store.commit('compras/orden-compra/SET_META', data.meta);
                            })
                    })
                    .finally( ()=>{
                        this.cargando = false;
                    });
            },
            eliminarOC(){
            this.cargando = true;
            let ordenes_c = [];
            ordenes_c.push(this.id);
            return this.$store.dispatch('compras/orden-compra/eliminar', { data:{data:ordenes_c, motivo:this.motivo}}
                ).then(data => {
                this.$emit('created', data);
                $(this.$refs.modal).modal('hide');
            }).finally(()=>{
                this.cargando = false;
                $(this.$refs.modal).modal('hide');
            })

        },
        }
    }
</script>

<style scoped>

</style>
