<template>
    <span>
        <button @click="find(id)" type="button" class="btn btn-sm btn-outline-danger" title="Eliminar Venta" v-show="borrar">
            <i class="fa fa-trash"></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-th"></i> ELIMINAR VENTA</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                            </div>
                            <div class="col-12">
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
                                            <div class="invalid-feedback" v-show="errors.has('motivo')">{{ errors.first('motivo') }}</div>
                                        </div>
                                    </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-danger" :disabled="errors.count() > 0 || motivo === ''" @click="validate">Eliminar</button>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>
<script>
export default {
    name: "eliminar-venta",
    props: ['id','pagina','borrar'],
    data() {
        return {
            motivo:'',
            cargando: false,
        }
    },
    methods: {
        destroy() {
            return this.$store.dispatch('ventas/venta/delete', {
                id: this.id,
                params: {data: [this.$data.motivo]}
            })
            .then(() => {
                this.$store.dispatch('ventas/venta/paginate', {})
                .then(data => {
                    this.$store.commit('ventas/venta/SET_VENTAS', data.data);
                    this.$store.commit('ventas/venta/SET_META', data.meta);
                })
            }).finally( ()=>{
                $(this.$refs.modal).modal('hide');
            });
        },
        find(id) {
            return this.$store.dispatch('ventas/venta/find', {
                id: id
            }).then(data => {
                this.$store.commit('ventas/venta/SET_VENTA', data);
                $(this.$refs.modal).modal('show')
            })
        },
        validate() {
            this.$validator.validate().then(result => {
                if (result) {
                    if(this.motivo === '') {
                        swal('¡Error!', 'Debe colocar un motivo para realizar la operación.', 'error')
                    }
                    else {
                        this.destroy()
                    }
                }
            });
        },
    },
    computed:{
        venta() {
            return this.$store.getters['ventas/venta/currentVenta'];
        }
    }
}
</script>
<style>
    .icons
    {
        text-align: center;
    }
</style>