<template>
    <span>
        <button @click="find(material)" type="button" class="btn btn-sm btn-outline-info" title="Editar Material" v-show="update">
            <i class="fa fa-pencil" v-if="!cargando"></i>
            <i class="fa fa-spinner fa-spin" v-else></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-pencil"></i> EDITAR MATERIAL</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row error-content">
                                        <label for="tipo" class="col-md-2 col-form-label">Familia: </label>
                                        <div class="col-md-10" v-if="$root.can('editar_familia_material') && $root.can('editar_insumo_material')">
                                            <model-list-select
                                                    :disabled="cargando"
                                                    name="tipo"
                                                    v-validate="{required: true}"
                                                    v-model="insumo.tipo"
                                                    option-value="nivel"
                                                    option-text="descripcion"
                                                    :list="materiales"
                                                    :placeholder="!cargando?'Seleccionar o buscar familia por descripcion':'Cargando...'"
                                                    >
                                            </model-list-select>
                                            <div class="invalid-feedback" v-show="errors.has('tipo')">{{ errors.first('tipo') }}</div>
                                        </div>
                                        <div class="col-md-10" v-else>
                                            <label>{{res.descripcion_familia}}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                               <div class="col-md-12">
                                    <div class="form-group row error-content">
                                        <label for="descripcion" class="col-sm-2 col-form-label">Descripción:</label>
                                        <div class="col-sm-10">
                                            <input
                                                type="text"
                                                name="descripcion"
                                                data-vv-as="Descripcion"
                                                v-validate="{required: true}"
                                                class="form-control"
                                                id="descripcion"
                                                v-model="insumo.descripcion"
                                                placeholder="Descripcion"
                                                :class="{'is-invalid': errors.has('descripcion')}">
                                            <div class="invalid-feedback" v-show="errors.has('descripcion')">{{ errors.first('descripcion') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row error-content">
                                        <label for="nu_parte" class="col-sm-2 col-form-label">N° Parte:</label>
                                        <div class="col-sm-5">
                                            <input
                                                :disabled="!insumo.descripcion"
                                                type="text"
                                                name="nu_parte"
                                                data-vv-as="N° Parte"
                                                v-validate="{required: true}"
                                                class="form-control"
                                                id="nu_parte"
                                                v-model="insumo.numero_parte"
                                                placeholder="######"
                                                :class="{'is-invalid': errors.has('nu_parte')}">
                                            <div class="invalid-feedback" v-show="errors.has('nu_parte')">{{ errors.first('nu_parte') }}</div>
                                        </div>
                                        <label for="unidad" class="col-sm-1 col-form-label">Unidad: </label>
                                        <div class="col-sm-2">
                                            <select
                                                type="text"
                                                name="unidad"
                                                data-vv-as="Unidad"
                                                v-validate="{required: true}"
                                                class="form-control"
                                                id="unidad"
                                                v-model="insumo.unidad"
                                                :class="{'is-invalid': errors.has('unidad')}"
                                            >
                                                    <option value>--Unidad--</option>
                                                    <option v-for="unidad in unidades" :value="unidad.unidad">{{ unidad.descripcion }}</option>
                                            </select>
                                            <div class="invalid-feedback" v-show="errors.has('unidad')">{{ errors.first('unidad') }}</div>
                                        </div>
                                    </div>

                                </div>

                            </div>

                        </div>
                        <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                <button type="submit" class="btn btn-primary":disabled="errors.count() > 0 ">Actualizar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </span>
</template>
<script>
        import {ModelListSelect} from 'vue-search-select';
export default {

    name: "material-editar",
    props: ['material', 'update'],
    components: {ModelListSelect},
    data() {
        return {
            cargando: false,
            id: '',
            res: '',
            unidades: [],
            materiales: [],
            insumo: {
                descripcion: '',
                numero_parte: '',
                tipo: '',
                unidad: ''
            }

        }
    },
    methods: {
        save() {
            if(this.insumo.descripcion == this.res.descripcion && this.insumo.tipo == this.res.nivel_padre && this.insumo.numero_parte == this.res.numero_parte && this.insumo.unidad == this.res.unidad)
            {
                swal('¡Error!', 'Favor de ingresar datos actualizados.', 'error')
            }else{

                return this.$store.dispatch('cadeco/material/update', {
                id: this.id,
                data: this.insumo,
            })
                .then(() => {
                   return this.$store.dispatch('cadeco/material/paginate', { params: {scope:['materiales','insumos'], sort: 'descripcion', order: 'asc'}})
                    .then(data => {
                        this.$store.commit('cadeco/material/SET_MATERIALES', data.data);
                        this.$store.commit('cadeco/material/SET_META', data.meta);
                    })
                   }).finally( ()=>{
                       $(this.$refs.modal).modal('hide');
                   });
            }
        },
        find(material) {
            this.id = '';
            this.getMateriales();
            this.getUnidades();
            this.cargando = true;
            this.res = '';
            this.id = material;

                this.$store.commit('cadeco/unidad/SET_UNIDAD', null);
                return this.$store.dispatch('cadeco/material/find', {
                    id: material,
                    params: {scope: 'materiales'}
                }).then(data => {

                    this.$store.commit('cadeco/material/SET_MATERIAL', data);
                    this.res = data;
                    this.insumo.tipo = this.res.nivel_padre;
                    this.insumo.descripcion = this.res.descripcion;
                    this.insumo.unidad = this.res.unidad;
                    this.insumo.numero_parte = this.res.numero_parte;

                    $(this.$refs.modal).modal('show')
                }).finally(() => {
                    this.cargando = false;
                })
        },
        getUnidades() {
                return this.$store.dispatch('cadeco/unidad/index', {
                    params: {sort: 'unidad',  order: 'asc'}
                })
                    .then(data => {
                        this.unidades= data.data;
                    })
        },
         getMateriales(){
                return this.$store.dispatch('cadeco/familia/index', {
                    params: {sort: 'descripcion',  order: 'asc', scope:'tipo:1'}
                })
                    .then(data => {
                        this.materiales = data.data;
                    })
        },
        validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.save()
                    }
                });
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
