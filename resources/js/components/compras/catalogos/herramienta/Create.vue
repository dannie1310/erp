<template>
    <span>
        <button @click="init" v-if="$root.can('registrar_insumo_herramienta_equipo')" class="btn btn-app float-right" :disabled="cargando">
            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
            <i class="fa fa-plus" v-else></i>
            Registrar
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-plus"></i> REGISTRAR HERRAMIENTA / EQUIPO</h5>
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
                                                    v-validate="{required: true}"
                                                    name="tipo"
                                                    v-model="dato.tipo"
                                                    option-value="nivel"
                                                    option-text="descripcion"
                                                    :list="familias_hye"
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
                                                :disabled="!dato.tipo"
                                                type="text"
                                                name="descripcion"
                                                data-vv-as="Descripcion"
                                                v-validate="{required: true, max:250}"
                                                class="form-control"
                                                id="descripcion"
                                                placeholder="Descripcion"
                                                v-model="dato.descripcion"
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
                                                :disabled="!dato.tipo"
                                                type="text"
                                                name="nu_parte"
                                                data-vv-as="N° Parte"
                                                v-validate="{required: true, max:16}"
                                                class="form-control"
                                                id="nu_parte"
                                                placeholder="######"
                                                v-model="dato.nu_parte"
                                                :class="{'is-invalid': errors.has('nu_parte')}">
                                            <div class="invalid-feedback" v-show="errors.has('nu_parte')">{{ errors.first('nu_parte') }}</div>
                                        </div>
                                        <label for="unidad" class="col-sm-1 col-form-label">Unidad: </label>
                                        <div class="col-sm-2">
                                            <select
                                                :disabled="!dato.tipo"
                                                type="text"
                                                name="unidad"
                                                data-vv-as="Unidad"
                                                v-validate="{required: true}"
                                                class="form-control"
                                                id="unidad"
                                                v-model="dato.unidad"
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
                                <button type="submit" class="btn btn-primary":disabled="errors.count() > 0 ">Registrar</button>
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
        name: "herramienta-create",
        components: {ModelListSelect},
        data() {
                return {
                    cargando:false,
                    unidades: [],
                    familias_hye:[],
                    dato: {
                        tipo: '',
                        unidad:'',
                        descripcion: '',
                        nu_parte:'',
                        tipo_material:4,
                        equivalencia:1,
                        marca:1
                    }
                }
        },
        mounted() {
            this.getUnidades();
            this.getFamiliasHyE();
        },
        methods: {
            init() {
                  this.cargando = false;
                    this.dato.tipo = null;
                    this.dato.unidad = '';
                    this.dato.descripcion = '';
                    this.dato.nu_parte = '';

                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
            },
            getUnidades() {
                return this.$store.dispatch('cadeco/unidad/index', {
                    params: {sort: 'unidad',  order: 'asc'}
                })
                    .then(data => {
                        this.unidades= data.data;
                    })
            },
            getFamiliasHyE(){
                return this.$store.dispatch('cadeco/familia/index', {
                    params: {sort: 'descripcion',  order: 'asc', scope:'tipo:4'}
                })
                    .then(data => {
                        this.familias_hye= data.data;
                    })
            },
            store() {
                return this.$store.dispatch('cadeco/material/store', this.$data.dato)
                    .then(data => {
                        this.$emit('created', data);
                        $(this.$refs.modal).modal('hide');
                    }).finally( ()=>{
                        this.cargando = false;
                        this.tipo = '';
                        this.descripcion = '';

                    });
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                            this.store()
                    }
                });
            },
        }
    }
</script>

<style scoped>

</style>
