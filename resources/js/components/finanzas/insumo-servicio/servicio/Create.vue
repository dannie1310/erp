<template>
    <span>
        <button @click="init" v-if="$root.can('registrar_insumo_servicio')" class="btn btn-app btn-info pull-right" :disabled="cargando">
            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
            <i class="fa fa-plus" v-else></i>
            Registrar Servicio
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-th"></i> REGISTRAR SERVICIO</h5>
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
                                            <FamiliaSelect
                                                :scope="'tipo:2'"
                                                name="tipo"
                                                id="tipo"
                                                data-vv-as="Servicio"
                                                v-validate="{required: true}"
                                                v-model="dato.tipo"
                                                :class="{'is-invalid': errors.has('tipo')}">

                                            </FamiliaSelect>
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
                                                v-validate="{required: true}"
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
                                                v-validate="{required: true}"
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
                                                    <option v-for="unidad in unidades" :value="unidad.id">{{ unidad.descripcion }}</option>
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
    import FamiliaSelect from "../familia/Select";
    export default {
        name: "material-create",
        components: {FamiliaSelect},
        data() {
            return {
                cargando:false,
                unidades: [
                    {id:'M', descripcion: 'M'},
                    {id:'M2', descripcion: 'M2'},
                    {id:'M3', descripcion: 'M3'},
                    {id:'ML', descripcion: 'ML'},
                    {id:'KG', descripcion: 'KG'},
                    {id:'PZA', descripcion: 'PZA'},
                    {id:'TON', descripcion: 'TON'},
                    {id:'JOR', descripcion: 'JOR'},
                    {id:'LOTE', descripcion: 'LOTE'},
                    {id:'PAQ', descripcion: 'PAQ'},
                    {id:'PAR', descripcion: 'PAR'},
                    {id:'CAJA', descripcion: 'CAJA'},
                    {id:'HORA', descripcion: 'HORA'},
                    {id:'BLOCK', descripcion: 'BLOCK'},
                    {id:'LITRO', descripcion: 'LITRO'},
                    {id:'JGO', descripcion: 'JUEGO'},
                    {id:'ROLLO', descripcion: 'ROLLO'},
                    {id:'PULGADA', descripcion: 'PULGADA'}
                ],
                dato: {
                    tipo: '',
                    unidad:'',
                    descripcion: '',
                    nu_parte:'',
                    tipo_material:2,
                    equivalencia:1,
                    marca:1
                }
            }
        },
        methods: {
            init() {
                this.cargando = false;
                this.dato.tipo = null;
                this.dato.unidad = '';
                this.dato.descripcion = '';
                this.dato.nu_parte = '';
                $(this.$refs.modal).modal('show');
            },
            store() {
                return this.$store.dispatch('compras/material-familia/store', this.$data.dato)
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
