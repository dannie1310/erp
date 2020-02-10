<template>
    <span>
        <button @click="find(unidad)" type="button" class="btn btn-sm btn-outline-danger" title="Eliminar Unidad" v-show="borrar">
            <i class="fa fa-trash"></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-unidad"> <i class="fas fa-trash-alt"></i> ELIMINAR UNIDAD</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" v-if="res">
                        <div class="row">
                            <div class="col-md-12">
                                    <div class="form-group row error-content">
                                        <label for="unidad" class="col-sm-2 offset-1" style="text-align:right;">Unidad: &nbsp;</label>
                                            {{res.unidad}}
                                        <label for="unidad" class="col-sm-3" style="text-align:right;">Descripción: &nbsp;</label>
                                        {{res.descripcion}}
                                    </div>
                            </div>
                        </div>    
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-danger" @click="destroy">Eliminar</button>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>
<script>
export default {
    name: "eliminar-unidad",
    props: ['unidad', 'borrar'],
    data() {
        return {
            cargando: false,
            id: '',
            res: ''
        }
    },
    methods: {
        destroy() {
            return this.$store.dispatch('cadeco/unidad/delete', {
                id: this.res.unidad,
                params: {data: [this.$data.motivo]}
            })
            .then(() => {
                this.$store.dispatch('cadeco/unidad/paginate', {params: {sort: 'unidad', order: 'asc'}})
                .then(data => {
                    this.$store.commit('cadeco/unidad/SET_UNIDADES', data.data);
                    this.$store.commit('cadeco/unidad/SET_META', data.meta);
                })
            }).finally( ()=>{
                $(this.$refs.modal).modal('hide');
            });
        },
        find(unidad) {
            this.cargando = true;
            this.res = '';
            this.id = unidad.unidad;           

                this.$store.commit('cadeco/unidad/SET_UNIDAD', null);
                return this.$store.dispatch('cadeco/unidad/find', {
                    id: this.id,
                }).then(data => {
                    this.$store.commit('cadeco/unidad/SET_UNIDAD', data);
                    this.res = data;
                    
                    $(this.$refs.modal).modal('show')
                }).finally(() => {
                    this.cargando = false;
                    this.id = '';
                })
        },
    },
}
</script>
<style>
    .icons
    {
        text-align: center;
    }
</style>