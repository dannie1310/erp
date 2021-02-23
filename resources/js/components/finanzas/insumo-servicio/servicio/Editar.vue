<template>
    <span>
        <button @click="find(servicio)" type="button" class="btn btn-sm btn-outline-info" title="Editar Servicio" v-show="update">
            <i class="fa fa-pencil" v-if="!cargando"></i>
            <i class="fa fa-spinner fa-spin" v-else></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-pencil"></i> EDITAR SERVICIO</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row error-content">
                                        <label for="tipo" class="col-sm-2 col-form-label">Familia: </label>
                                        <div class="col-sm-10">
                                            <model-list-select
                                                    :disabled="cargando"
                                                    name="tipo"
                                                    v-validate="{required: true}"
                                                    v-model="service.tipo"
                                                    option-value="nivel"
                                                    option-text="descripcion"
                                                    :list="familias_moys"
                                                    :placeholder="!cargando?'Seleccionar o buscar familia por descripcion':'Cargando...'"
                                                    >
                                            </model-list-select>
                                            <div class="invalid-feedback" v-show="errors.has('tipo')">{{ errors.first('tipo') }}</div>
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
                                                v-model="service.descripcion"
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
                                                :disabled="!service.descripcion"
                                                type="text"
                                                name="nu_parte"
                                                data-vv-as="N° Parte"
                                                v-validate="{required: true}"
                                                class="form-control"
                                                id="nu_parte"
                                                v-model="service.numero_parte"
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
                                                v-model="service.unidad"
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
    
    name: "servicio-editar",
    props: ['servicio', 'update'],
    components: {ModelListSelect},
    data() {
        return {
            cargando: false,
            id: '',
            res: '',
            unidades: [],
            familias_moys: [],
            service: {
                descripcion: '',
                numero_parte: '',
                tipo: '',
                unidad: ''

            }
            
        }
    },
    methods: {
        save() {
            if(this.service.descripcion == this.res.descripcion && this.service.tipo == this.res.nivel_padre && this.service.numero_parte == this.res.numero_parte && this.service.unidad == this.res.unidad)
            {
                swal('¡Error!', 'Favor de ingresar datos actualizados.', 'error')
            }else{

                return this.$store.dispatch('cadeco/material/update', {
                id: this.id,
                data: this.service,
            })
                .then(() => {
                   return this.$store.dispatch('cadeco/material/paginate', { params: {scope:['servicios','insumos'], sort: 'descripcion', order: 'asc'}})
                    .then(data => {
                        this.$store.commit('cadeco/material/SET_MATERIALES', data.data);
                        this.$store.commit('cadeco/material/SET_META', data.meta);
                    })
                   }).finally( ()=>{
                       $(this.$refs.modal).modal('hide');
                   });
            }
        },       
        find(servicio) {
            this.id = '';
            this.getFamiliasMOyS();
            this.getUnidades();
            this.cargando = true;
            this.res = '';
            this.id = servicio;    

                this.$store.commit('cadeco/unidad/SET_UNIDAD', null);
                return this.$store.dispatch('cadeco/material/find', {
                    id: servicio,
                    params: {scope: 'servicios'}
                }).then(data => {

                    this.$store.commit('cadeco/material/SET_MATERIAL', data);
                    this.res = data;
                    this.service.tipo = this.res.nivel_padre;
                    this.service.descripcion = this.res.descripcion;
                    this.service.unidad = this.res.unidad;
                    this.service.numero_parte = this.res.numero_parte;                                        
                    
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
         getFamiliasMOyS(){
                return this.$store.dispatch('cadeco/familia/index', {
                    params: {sort: 'descripcion',  order: 'asc', scope:'tipo:2'}
                })
                    .then(data => {
                        this.familias_moys= data.data;                        
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