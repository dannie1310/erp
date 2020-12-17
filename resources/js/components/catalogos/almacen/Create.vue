<template>
    <span>
        <button @click="init" v-if="$root.can('registrar_almacen_material')||$root.can('registrar_almacen_maquinaria')||$root.can('registrar_almacen_maquina_controladora_insumo')||
        $root.can('registrar_almacen_mano_obra')||$root.can('registrar_almacen_servicio')||$root.can('registrar_almacen_herramienta')" class="btn btn-app btn-info float-right">
            <i class="fa fa-plus"></i> Registrar
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Registrar Almacén</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row error-content">
                                        <label for="tipo_almacen" class="col-md-2 col-form-label">Tipo:</label>
                                        <div class="col-md-10">
                                            <select class="form-control"
                                                    data-vv-as="Tipo"
                                                    id="tipo_almacen"
                                                    name="tipo_almacen"
                                                    :error="errors.has('tipo_almacen')"
                                                    v-validate="{required: true}"
                                                    v-model="tipo_almacen">
                                                <option value>-- Selecionar --</option>
                                                <option v-for="(tipo) in tipos_almacenes" :value="tipo.id_tipo">{{ tipo.descripcion }}</option>
                                            </select>
                                            <div style="display:block" class="invalid-feedback" v-show="errors.has('tipo_almacen')">{{ errors.first('tipo_almacen') }}</div>
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
                                                   v-model="descripcion"
                                                   :class="{'is-invalid': errors.has('descripcion')}">
                                            <div class="invalid-feedback float-right"   v-show="errors.has('descripcion')"><span style="margin-left:5%;">{{ errors.first('descripcion') }}</span></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12" v-if="tipo_almacen == 2">
                                    <div class="form-group row error-content">
                                        <label for="id_material" class="col-md-2 col-form-label">Insumos:</label>
                                        <div class="col-md-10">
                                            <select class="form-control"
                                                    data-vv-as="Insumos"
                                                    id="id_material"
                                                    name="id_material"
                                                    :error="errors.has('id_material')"
                                                    v-validate="{required: true}"
                                                    v-model="id_material">
                                                <option value>-- Selecionar --</option>
                                                <option v-for="(material) in materiales.data" :value="material.id">{{ material.descripcion }}</option>
                                            </select>
                                            <div style="display:block" class="invalid-feedback" v-show="errors.has('id_material')">{{ errors.first('id_material') }}</div>
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
        name: "almacen-create",
        data() {
            return {
                descripcion : '',
                tipo_almacen : '',
                id_material : null,
                tipos_almacenes : [],
                materiales : []
            }
        },
        mounted() {
            this.tipos();
        },
        computed: {

        },
        methods: {
            init() {
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
                this.$validator.reset();
                this.descripcion = '';
                this.tipo_almacen = '';
                this.id_material = '';
            },
            store(){
                this.descripcion = this.descripcion.toUpperCase();
                return this.$store.dispatch('cadeco/almacen/store', this.$data)
                    .then((data) => {
                        this.$emit('created', data);
                        $(this.$refs.modal).modal('hide');
                    })
            },
            getMateriales(){
                return this.$store.dispatch('cadeco/material/index', {
                    params: {scope: 'tipo:8', sort: 'descripcion', order: 'asc'}
                }).then(data => {
                    this.materiales = data;
                });
            },
            tipos() {
                if(this.$root.can('registrar_almacen_material'))
                {
                    this.tipos_almacenes.push({
                        id_tipo :  "0",
                        descripcion : "Materiales",
                    });
                }
                if(this.$root.can('registrar_almacen_maquinaria'))
                {
                    this.tipos_almacenes.push({
                        id_tipo :  "1",
                        descripcion : "Maquina",
                    });
                }
                if(this.$root.can('registrar_almacen_maquina_controladora_insumo'))
                {
                    this.tipos_almacenes.push({
                        id_tipo :  "2",
                        descripcion : "Maquina Controladora de Insumos",
                    });
                    this.getMateriales();
                    this.id_material = "";
                }
                if(this.$root.can('registrar_almacen_mano_obra'))
                {
                    this.tipos_almacenes.push({
                        id_tipo :  "3",
                        descripcion : "Mano de Obra",
                    });
                }
                if(this.$root.can('registrar_almacen_servicio'))
                {
                    this.tipos_almacenes.push({
                        id_tipo :  "4",
                        descripcion : "Servicios",
                    });
                }
                if(this.$root.can('registrar_almacen_herramienta'))
                {
                    this.tipos_almacenes.push({
                        id_tipo :  "5",
                        descripcion : "Herramientas",
                    });
                }
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        if(this.tipo_empresa === ''){
                            swal('¡Error!', 'Seleccione un Tipo de Almacén.', 'error')
                        }else{
                            this.store()
                        }
                    }
                });
            },
            salir(){
                $(this.$refs.modal).modal('hide');
            },
        }
    }
</script>

<style>

</style>
