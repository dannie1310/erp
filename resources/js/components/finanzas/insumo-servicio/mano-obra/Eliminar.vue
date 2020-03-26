<template>
    <span>
        <button @click="find(mano)" type="button" class="btn btn-sm btn-outline-danger" title="Eliminar Mano de Obra" v-show="borrar">
            <i class="fa fa-trash"></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-mano"><i class="fas fa-trash-alt"></i> ELIMINAR MANO DE OBRA</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" v-if="res">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group row error-content">
                                    <label for="mano" class="col-sm-3 offset-1" style="text-align:right;">Mano de Obra: &nbsp;</label>
                                     {{res.descripcion}}
                                     
                                </div>
                                <label for="mano" class="col-sm-2 offset-1" style="text-align:right;">Unidad: &nbsp;</label>
                                     {{res.unidad}}
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
    name: "mano-obra-eliminar",
    props: ['mano', 'borrar'],
    data() {
        return {
            cargando: false,
            id: '',
            res: ''
        }
    },
    methods: {
        destroy() {
            
            return this.$store.dispatch('cadeco/material/delete', {
                id: this.res.id
            })
            .then(() => {
                return this.$store.dispatch('cadeco/material/paginate', { params: {scope:['manoObra','insumos'], sort: 'descripcion', order: 'asc'}})
                    .then(data => {
                        this.$store.commit('cadeco/material/SET_MATERIALES', data.data);
                        this.$store.commit('cadeco/material/SET_META', data.meta);
                    })
            }).finally( ()=>{
                $(this.$refs.modal).modal('hide');
            });
        },
        find(mano) {
            this.cargando = true;
            this.res = '';
            this.id = mano
                      

                this.$store.commit('cadeco/material/SET_MATERIAL', null);
                return this.$store.dispatch('cadeco/material/find', {
                    id: mano,
                    params: {scope: 'manoObra'}
                }).then(data => {
                    this.$store.commit('cadeco/material/SET_MATERIAL', data);
                    this.res = data;
                    
                    
                    $(this.$refs.modal).modal('show')
                }).finally(() => {
                    this.cargando = false;
                    this.id = '';
                })
        },        
    }
}
</script>
<style>
    .icons
    {
        text-align: center;
    }
</style>