<template>
    <span>
        <button @click="init" v-if="$root.can('agregar_conteos_manuales')" class="btn btn-app btn-info pull-right" :disabled="cargando">
            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
            <i class="fa fa-plus" v-else></i>
            Registrar Conteo
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-th"></i> REGISTRAR CONTEO MANUAL</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                     <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row error-content">
                                        <label for="id_marbete" class="col-sm-3 col-form-label">Marbete: </label>
                                         <div class="col-sm-9">
                                            <select
                                                    type="text"
                                                    name="id_marbete"
                                                    data-vv-as="Marbete"
                                                    v-validate="{required: true}"
                                                    class="form-control"
                                                    id="id_marbete"
                                                    v-model="dato.id_marbete"
                                                    :class="{'is-invalid': errors.has('id_marbete')}"
                                            >
                                                    <option value>-- Seleccione un marbete --</option>
                                                    <option v-for="marbete in marbetes" :value="marbete.id">{{ marbete.folio_marbete }}</option>
                                            </select>
                                            <div class="invalid-feedback" v-show="errors.has('dato.id_marbete')">{{ errors.first('id_marbete') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row error-content">
                                        <label for="tipo_conteo" class="col-sm-3 col-form-label">Tipo de Conteo: </label>
                                         <div class="col-sm-9">
                                            <select
                                                    type="text"
                                                    name="tipo_conteo"
                                                    data-vv-as="Beneficiario"
                                                    v-validate="{required: true}"
                                                    class="form-control"
                                                    id="tipo_conteo"
                                                    v-model="dato.tipo_conteo"
                                                    :class="{'is-invalid': errors.has('dato.tipo_conteo')}"
                                            >
                                                    <option value>-- Seleccione un conteo --</option>
                                                    <option v-for="conteo in conteos" :value="conteo.id">{{ conteo.descripcion }}</option>
                                            </select>
                                            <div class="invalid-feedback" v-show="errors.has('dato.tipo_conteo')">{{ errors.first('tipo_conteo') }}</div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row error-content">
                                        <label for="cantidad_usados" class="col-sm-3 col-form-label">Cantidad Usados</label>
                                        <div class="col-sm-9">
                                            <input
                                                    type="number"
                                                    name="cantidad_usados"
                                                    data-vv-as="Cantidad Usados"
                                                    v-validate="{required: true}"
                                                    class="form-control"
                                                    id="cantidad_usados"
                                                    placeholder="Cantidad Usados"
                                                    v-model="dato.cantidad_usados"
                                                    :class="{'is-invalid': errors.has('dato.cantidad_usados')}">
                                            <div class="invalid-feedback" v-show="errors.has('dato.cantidad_usados')">{{ errors.first('cantidad_usados') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                             <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row error-content">
                                        <label for="cantidad_nuevo" class="col-sm-3 col-form-label">Cantidad Nuevos</label>
                                        <div class="col-sm-9">
                                            <input
                                                    type="number"
                                                    name="cantidad_nuevos"
                                                    data-vv-as="Cantidad Nuevos"
                                                    v-validate="{required: true}"
                                                    class="form-control"
                                                    id="cantidad_nuevo"
                                                    placeholder="Cantidad Nuevos"
                                                    v-model="dato.cantidad_nuevo"
                                                    :class="{'is-invalid': errors.has('dato.cantidad_nuevo')}">
                                            <div class="invalid-feedback" v-show="errors.has('dato.cantidad_nuevo')">{{ errors.first('cantidad_nuevo') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                             <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row error-content">
                                        <label for="cantidad_inservible" class="col-sm-3 col-form-label">Cantidad Inservibles</label>
                                        <div class="col-sm-9">
                                            <input
                                                    type="number"
                                                    name="cantidad_inservible"
                                                    data-vv-as="Cantidad Inservible"
                                                    v-validate="{required: true}"
                                                    class="form-control"
                                                    id="cantidad_inservible"
                                                    placeholder="Cantidad Inservible"
                                                    v-model="dato.cantidad_inservible"
                                                    :class="{'is-invalid': errors.has('dato.cantidad_inservible')}">
                                            <div class="invalid-feedback" v-show="errors.has('dato.cantidad_inservible')">{{ errors.first('cantidad_inservible') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                             <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row error-content">
                                        <label for="total" class="col-sm-3 col-form-label">Total</label>
                                        <div class="col-sm-9">
                                            <input
                                                    type="number"
                                                    name="total"
                                                    data-vv-as="Total"
                                                    v-validate="{required: true}"
                                                    class="form-control"
                                                    id="total"
                                                    placeholder="Total"
                                                    v-model="dato.total"
                                                    :class="{'is-invalid': errors.has('dato.total')}">
                                            <div class="invalid-feedback" v-show="errors.has('dato.total')">{{ errors.first('total') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                             <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row error-content">
                                        <label for="iniciales" class="col-sm-3 col-form-label">Iniciales</label>
                                        <div class="col-sm-9">
                                            <input
                                                    type="text"
                                                    name="iniciales"
                                                    data-vv-as="Iniciales"
                                                    v-validate="{required: true}"
                                                    class="form-control"
                                                    id="iniciales"
                                                    placeholder="Iniciales"
                                                    v-model="dato.iniciales"
                                                    :class="{'is-invalid': errors.has('dato.iniciales')}">
                                            <div class="invalid-feedback" v-show="errors.has('dato.iniciales')">{{ errors.first('iniciales') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row error-content">
                                        <label for="observaciones" class="col-sm-3 col-form-label">Observaciones: </label>
                                        <div class="col-sm-9">
                                            <textarea
                                                    name="observaciones"
                                                    id="observaciones"
                                                    class="form-control"
                                                    v-model="dato.observaciones"
                                                    data-vv-as="Observaciones"
                                                    :class="{'is-invalid': errors.has('dato.observaciones')}"
                                            ></textarea>
                                            <div class="invalid-feedback" v-show="errors.has('dato.observaciones')">{{ errors.first('observaciones') }}</div>
                                        </div>
                                    </div>
                                </div>
                             </div>
                        </div>
                         <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                <button type="submit" class="btn btn-primary">Registrar</button>
                        </div>
                     </form>
                </div>
            </div>
          </div>
    </span>
</template>

<script>
    export default {
        name: "conteo-create",
        data() {
            return {
                cargando: false,
                marbetes:[],
                conteos:[],
                dato:{
                    id_marbete:'',
                    tipo_conteo:'',
                    cantidad_usados:'',
                    cantidad_nuevo:'',
                    cantidad_inservible:'',
                    total:'',
                    iniciales:'',
                    observaciones:''
                   }
            }
        },
        mounted(){
            this.getMarbete();
            this.getConteo();
        },
        methods:{
            init() {
                this.cargando = true;
                this.dato.id_marbete = '';
                this.dato.tipo_conteo = '';
                this.dato.cantidad_usados='';
                this.dato.cantidad_nuevo='';
                this.dato.cantidad_inservible='';
                this.dato.total='';
                this.dato.iniciales='';
                this.dato.observaciones='';
                $(this.$refs.modal).modal('show');
                this.$validator.reset();
                this.cargando = false;
            },
            getMarbete(){
                this.marbetes = [];
                return this.$store.dispatch('almacenes/marbete/index', {
                    params: {
                        sort: 'folio', order: 'desc'
                    }
                })
                    .then(data => {
                        this.marbetes = data.data;
                    })
            },
            getConteo(){
                this.conteos = [];
                return this.$store.dispatch('almacenes/ctg-tipo-conteo/index', {
                    params: {
                        sort: 'id', order: 'asc'
                    }
                })
                    .then(data => {
                        this.conteos = data.data;
                    })
            },

            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        if(this.dato.cantidad_usados <= 0 || this.dato.cantidad_nuevos <= 0 || this.dato.cantidad_inservibles <= 0 || this.dato.total <= 0){
                            swal('¡Error!', 'Error al registrar cantidad, favor de revisar la información y registrar la cantidad nuevamente.', 'error')
                        }
                        else {
                            this.dato.iniciales = this.dato.iniciales.toUpperCase();
                            this.store()
                        }
                    }
                });
            },
            store() {
                return this.$store.dispatch('almacenes/conteo/store', this.$data.dato)
                    .then(data => {
                        this.$emit('created', data);
                        $(this.$refs.modal).modal('hide');
                    }).finally( ()=>{
                        this.cargando = false;
                    });
            },
        }
    }
</script>

<style>
    .error-label {
        width: 100%;
        margin-top: 0.25rem;
        font-size: 80%;
        color: #dc3545;
    }
</style>