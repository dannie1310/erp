<template>
    <span>
        <button @click="find(id)" type="button" class="btn btn-sm btn-outline-danger" title="Eliminar Factura" v-show="borrar">
            <i class="fa fa-trash"></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-unidad"> <i class="fas fa-trash-alt"></i> ELIMINAR UNIDAD</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" v-if="factura">
                        <div class="row">
                            <div class="col-md-12">
                                    <div class="form-group row error-content">
                                        <label for="unidad" class="col-sm-3" style="text-align:right;">Unidad: &nbsp;</label>
                                            eliminar
                                    </div>
                                    <div class="form-group row error-content">
                                        <label for="unidad" class="col-sm-3" style="text-align:right;">Descripción: &nbsp;</label>
                                            descripcion de la unidad 
                                    </div>
                            </div>
                        </div>
                    

                                         


                    
                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-danger" @click="validate">Eliminar</button>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>
<script>
export default {
    name: "eliminar-unidad",
    props: ['id', 'borrar'],
    data() {
        return {
            motivo:'',
            cargando: false,
        }
    },
    methods: {
        destroy() {
            return this.$store.dispatch('finanzas/factura/delete', {
                id: this.id,
                params: {data: [this.$data.motivo]}
            })
            .then(() => {
                this.$store.dispatch('finanzas/factura/paginate', {params: {sort: 'id_transaccion', order: 'desc', include: ['contra_recibo','empresa']}})
                .then(data => {
                    this.$store.commit('finanzas/factura/SET_FACTURAS', data.data);
                    this.$store.commit('finanzas/factura/SET_META', data.meta);
                })
            }).finally( ()=>{
                $(this.$refs.modal).modal('hide');
            });
        },
        find(id) {
            this.motivo = '';
            this.cargando = true;
            // $(this.$refs.modal).modal('show')

                this.$store.commit('finanzas/factura/SET_FACTURA', null);
                return this.$store.dispatch('finanzas/factura/find', {
                    id: 117337,
                }).then(data => {
                    this.$store.commit('finanzas/factura/SET_FACTURA', data);
                    $(this.$refs.modal).modal('show')
                }).finally(() => {
                    this.cargando = false;
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
        factura() {
                return this.$store.getters['finanzas/factura/currentFactura']
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