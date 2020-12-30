<template>
    <span>
        <button @click="find" type="button" class="btn btn-sm btn-outline-info" title="Editar"  :disabled="cargando">
            <i class="fa fa-pencil" v-if="!cargando"></i>
            <i class="fa fa-spinner fa-spin" v-else></i>
        </button>
        <!-- Modal -->
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">EDICIÓN DEL ALMACÉN</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" v-if="almacen" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row error-content">
                                        <label for="tipo" class="col-md-2 col-form-label">Tipo: </label>
                                        <div class="col-md-10">
                                            <input type="text"
                                                   disabled="true"
                                                   name="tipo"
                                                   data-vv-as="Tipo Almacén"
                                                   class="form-control float-right"
                                                   id="tipo"
                                                   placeholder="Tipo Almacén"
                                                   v-model="almacen.tipo"
                                                   :class="{'is-invalid': errors.has('tipo')}">
                                            <div class="invalid-feedback float-right"   v-show="errors.has('tipo')"><span style="margin-left:5%;">{{ errors.first('tipo') }}</span></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group row error-content">
                                        <label for="descripcion" class="col-md-2 col-form-label">Descripción: </label>
                                        <div class="col-md-10">
                                            <input type="text"
                                                   name="descripcion"
                                                   style="text-transform:uppercase;"
                                                   data-vv-as="Descripción"
                                                   v-validate="{required: true}"
                                                   class="form-control float-right"
                                                   id="descripcion"
                                                   placeholder="Descripción"
                                                   v-model="almacen.descripcion"
                                                   :class="{'is-invalid': errors.has('descripcion')}">
                                            <div class="invalid-feedback float-right"   v-show="errors.has('descripcion')"><span style="margin-left:5%;">{{ errors.first('descripcion') }}</span></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12" v-if="almacen.material">
                                    <div class="form-group row error-content">
                                        <label class="col-md-2 col-form-label">Insumos:</label>
                                        <div class="col-md-10">
                                            <input type="text"
                                                   disabled="true"
                                                   name="material"
                                                   data-vv-as="Material"
                                                   id="material"
                                                   class="form-control float-right"
                                                   v-model="almacen.material.descripcion">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12" v-if="almacen.numero_economico">
                                    <div class="form-group row error-content">
                                        <label for="numero_economico" class="col-md-2 col-form-label">Número de Economico: </label>
                                        <div class="col-md-10">
                                            <input type="text"
                                                   name="numero_economico"
                                                   style="text-transform:uppercase;"
                                                   data-vv-as="Número de Economico"
                                                   v-validate="{required: true}"
                                                   class="form-control float-right"
                                                   id="numero_economico"
                                                   placeholder="Número de Economico"
                                                   v-model="almacen.numero_economico"
                                                   :class="{'is-invalid': errors.has('numero_economico')}">
                                            <div class="invalid-feedback float-right"   v-show="errors.has('numero_economico')"><span style="margin-left:5%;">{{ errors.first('numero_economico') }}</span></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12" v-if="almacen.clasificacion">
                                    <div class="form-group row error-content">
                                        <label for="clasificacion" class="col-md-2 col-form-label">Clasificación:</label>
                                        <div class="col-md-10">
                                            <select class="form-control"
                                                    data-vv-as="Clasificación"
                                                    id="clasificacion"
                                                    name="clasificacion"
                                                    :error="errors.has('clasificacion')"
                                                    v-validate="{required: true}"
                                                    v-model="almacen.clasificacion">
                                                <option value>-- Selecionar --</option>
                                                <option v-for="(clasificacion) in clasificaciones" :value="clasificacion">{{ clasificacion }}</option>
                                            </select>
                                            <div style="display:block" class="invalid-feedback" v-show="errors.has('clasificacion')">{{ errors.first('clasificacion') }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12" v-if="almacen.propiedad">
                                    <div class="form-group row error-content">
                                        <label for="propiedad" class="col-md-2 col-form-label">Propiedad:</label>
                                        <div class="col-md-10">
                                            <select class="form-control"
                                                    data-vv-as="Propiedad"
                                                    id="propiedad"
                                                    name="propiedad"
                                                    :error="errors.has('propiedad')"
                                                    v-validate="{required: true}"
                                                    v-model="almacen.propiedad">
                                                <option value>-- Selecionar --</option>
                                                <option v-for="(p) in propiedades" :value="p">{{ p }}</option>
                                            </select>
                                            <div style="display:block" class="invalid-feedback" v-show="errors.has('propiedad')">{{ errors.first('propiedad') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" v-on:click="salir">
                                <i class="fa fa-angle-left"></i>Regresar
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Guardar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "almacen-edit",
        props: ['id'],
        data() {
            return {
                cargando : false,
                almacen : [],
                clasificaciones : {
                    0 : 'Mayor',
                    1 : 'Menor',
                    2 : 'Transporte'
                },
                propiedades : {
                    0 : 'Propio',
                    1 : 'Terceros',
                    2 : 'Sociedad'
                }
            }
        },
        methods: {
            find() {
                this.cargando = true;
                return this.$store.dispatch('cadeco/almacen/find', {
                    id: this.id,
                    params: {include: 'material'}
                })
                    .then(data => {
                        this.almacen = data
                        $(this.$refs.modal).appendTo('body')
                        $(this.$refs.modal).modal('show');
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },
            validate() {
                this.almacen.descripcion = this.almacen.descripcion.toUpperCase()
                this.$validator.validate().then(result => {
                    if (result) {
                        this.update()
                    }
                });
            },
            update() {
                var data = {}
                data['descripcion'] = this.almacen.descripcion.toUpperCase()
                data['tipo_almacen'] = this.almacen.tipo_almacen
                data['id_material'] = this.almacen.id_material  == '' ? null : this.almacen.id_material
                data['numero_economico'] = this.almacen.numero_economico == '' ? null : this.almacen.numero_economico
                data['clasificacion'] = this.almacen.clasificacion  == '' ? null : this.almacen.clasificacion
                data['propiedad'] = this.almacen.propiedad == '' ? null : this.almacen.propiedad

                return this.$store.dispatch('cadeco/almacen/update', {
                    id: this.id,
                    data: data
                }).then(data => {
                    this.$store.commit('cadeco/almacen/UPDATE_ALMACEN', data);
                    this.salir();
                })
            },
            salir(){
                $(this.$refs.modal).modal('hide');
            },
        },
        watch: {

        }
    }
</script>

<style scoped>

</style>
