<template>
    <span>
        <button @click="find(unidad)" type="button" class="btn btn-sm btn-outline-info" title="Editar Unidad" v-show="editar">
            <i class="fa fa-pencil"></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-unidad"> <i class="fa fa-pencil"></i> EDITAR UNIDAD</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" v-if="res">
                        
                        <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row error-content">
                                        <label for="unidad" class="col-sm-1 col-form-label" style="text-align:right;">Unidad: </label>
                                        <div class="col-sm-2">
                                              <input
                                                type="text"
                                                name="unidad"
                                                data-vv-as="Unidad"
                                                v-validate="{required: true}"
                                                class="form-control"
                                                id="campo1"
                                                v-model="campo1"
                                                placeholder="######"
                                                :class="{'is-invalid': errors.has('unidad')}">
                                            <div class="invalid-feedback" v-show="errors.has('unidad')">{{ errors.first('unidad') }}</div>
                                        </div>                                        
                                        <label for="descripcion" class="col-sm-2 col-form-label" style="text-align:right;">Descripción: </label>
                                        <div class="col-sm-5">
                                            <input
                                                type="text"
                                                name="descripcion"
                                                data-vv-as="Descripción"
                                                v-validate="{required: true}"
                                                class="form-control"
                                                id="campo2"
                                                v-model="campo2"
                                                placeholder="######"
                                                :class="{'is-invalid': errors.has('descripcion')}">
                                            <div class="invalid-feedback" v-show="errors.has('descripcion')">{{ errors.first('descripcion') }}</div>
                                        </div>
                                    </div>

                                </div>

                            </div>




                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary" :disabled="errors.count() > 0 " @click="update">Editar</button>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>
<script>
export default {
    name: "editar-unidad",
    props: ['unidad', 'editar'],
    data() {
        return {
            campo1:'',
            campo2:'',
            cargando: false,
            id: '',
            res: '',
            t: {
            unidad:'',
            descripcion: '',
            }
        }
    },
    methods: {
        update() {
            
            if(this.campo1 == this.res.unidad && this.campo2 == this.res.descripcion)
            {
                swal('¡Error!', 'El campo Unidad ó Descripción no tienen valores actualizados.', 'error')
            }else{
                    this.t.unidad = this.campo1;
                    this.t.descripcion = this.campo2;
                       return this.$store.dispatch('cadeco/unidad/update', {
                       id: this.res.unidad,
                       params: this.$data.t
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
            }
            
        },
        find(unidad) {
            this.cargando = true;
            this.t.unidad = '';
            this.t.descripcion = ';'
            this.res = '';
            this.id = unidad.unidad;           

                this.$store.commit('cadeco/unidad/SET_UNIDAD', null);
                return this.$store.dispatch('cadeco/unidad/find', {
                    id: this.id,
                }).then(data => {
                    this.$store.commit('cadeco/unidad/SET_UNIDAD', data);
                    this.res = data;
                    this.campo1 = this.res.unidad;
                    this.campo2 = this.res.descripcion;

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