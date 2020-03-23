<template>
    <span>
        <button @click="find(maquinaria)" type="button" class="btn btn-sm btn-outline-danger" title="Eliminar Maquinaria" v-show="borrar">
            <i class="fa fa-trash"></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-unidad"> <i class="fas fa-trash-alt"></i> ELIMINAR MAQUINARIA</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" v-if="res">
                        <div class="row">
                            <div class="col-md-12">
                                    <div class="form-group row error-content">
                                        <!-- <label for="unidad" class="col-sm-2 offset-1" style="text-align:right;">Unidad: &nbsp;</label>
                                            {{res.unidad}} -->
                                        <label for="unidad" class="col-sm-3" style="text-align:right;">Maquinaria: &nbsp;</label>
                                        {{res.descripcion}}
                                        <label for="unidad" class="col-sm-2 offset-2" style="text-align:right;">Unidad: &nbsp;</label>
                                        {{res.unidad}}
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
    name: "maquinaria-eliminar",
    props: ['maquinaria', 'borrar'],
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
                return this.$store.dispatch('cadeco/material/paginate', { params: {scope:['tipo:8','insumos'], sort: 'descripcion', order: 'asc'}})
                    .then(data => {
                        this.$store.commit('cadeco/material/SET_MATERIALES', data.data);
                        this.$store.commit('cadeco/material/SET_META', data.meta);
                    })
            }).finally( ()=>{
                $(this.$refs.modal).modal('hide');
            });
        },
        find(maquinaria) {
            this.cargando = true;
            this.res = '';
            this.id = maquinaria
                      

                this.$store.commit('cadeco/material/SET_MATERIAL', null);
                return this.$store.dispatch('cadeco/material/find', {
                    id: maquinaria,
                    params: {scope: 'tipo:8'}
                }).then(data => {
                    this.$store.commit('cadeco/material/SET_MATERIAL', data);
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