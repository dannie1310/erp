<template>
    <span>
        <button @click="init" v-if="$root.can('registrar_solicitud_movimiento_fondo_garantia') && tipo_boton ==1" class="btn btn-app float-right" >
            <i class="fa fa-plus"></i> Registrar Solicitud
        </button>

        <span>
        <button @click="init"  v-if="$root.can('registrar_solicitud_movimiento_fondo_garantia') && tipo_boton ==2" :disabled="cargando" type="button" class="btn btn-sm btn-outline-success" title="Nueva Solicitud de Movimiento">
            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
            <i class="fa fa-file-text" style="padding:2px" v-else></i>
        </button>
        </span>

        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"><i class="fa fa-edit" style="padding-right:3px"></i>Registrar Solicitud de Movimiento a Fondo de Garantía</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row">
                                 <!-- Fecha -->
                                <div class="offset-md-8 col-md-1">
                                     <label for="fecha">Fecha:</label>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group error-content">
                                        <input
                                                type="date"
                                                name="fecha"
                                                id="fecha"
                                                class="form-control"
                                                v-model="fecha"
                                                v-validate="{required: true, date_format: 'yyyy-MM-dd'}"
                                                data-vv-as="Fecha"
                                                :class="{'is-invalid': errors.has('fecha')}"
                                        >
                                        <div class="invalid-feedback" v-show="errors.has('fecha')">{{ errors.first('fecha') }}</div>
                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <!-- Referencia -->
                                <div class="col-md-2">
                                    <label for="referencia">Referencia:</label>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group error-content">
                                    <input
                                                type="text"
                                                name="referencia"
                                                id="referencia"
                                                class="form-control"
                                                v-model="referencia"
                                                v-validate="{required: true}"
                                                data-vv-as="Referecia"
                                                :class="{'is-invalid': errors.has('referencia')}"
                                        >
                                        <div class="invalid-feedback" v-show="errors.has('referencia')">{{ errors.first('referencia') }}</div>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <label >Tipo:</label>
                                </div>
                                <div class="col-md-3">
                                    <div class="btn-group btn-group-toggle">
                                        <label class="btn btn-outline-secondary" :class="id_tipo_solicitud === Number(key) ? 'active': ''" v-for="(tipo_solicitud, key) in tipos_solicitud" :key="key">
                                            <input type="radio"
                                                   class="btn-group-toggle"
                                                   name="id_tipo_solicitud"
                                                   :id="'tipo_solicitud_' + key"
                                                   :value="key"
                                                   autocomplete="off"
                                                   v-model.number="id_tipo_solicitud">
                                            {{ tipo_solicitud }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="row" v-if="objFondoGarantia.subcontrato">
                                <!-- Subcontrato -->
                                <div class="col-md-2">
                                    <label for="id_fondo_garantia">Subcontrato:</label>
                                </div>
                                <div class="col-md-6">
                                    {{objFondoGarantia.subcontrato.numero_folio_format}} - Referencia: [{{objFondoGarantia.subcontrato.referencia}}]
                                </div>
                            </div>
                            <div class="row" v-if="!objFondoGarantia.subcontrato">
                                <!-- Subcontrato -->
                                <div class="col-md-2">
                                    <label for="id_fondo_garantia">Subcontrato:</label>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group error-content">
                                        <subcontrato-select
                                                scope="conFondo"
                                                name="id_fondo_garantia"
                                                id="id_fondo_garantia"
                                                data-vv-as="Subcontrato"
                                                v-validate="{required: true}"
                                                v-model="id_fondo_garantia"
                                                :error="errors.has('id_subcontrato')">
                                            ></subcontrato-select>
                                        <div class="error-label" v-show="errors.has('id_fondo_garantia')">{{ errors.first('id_fondo_garantia') }}</div>
                                    </div>
                                 </div>
                            </div>

                            <div class="row">
                                <!-- Importe -->
                                <div class="offset-md-8 col-md-1">
                                     <label for="importe">Importe:</label>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group error-content">
                                        <input
                                                type="number"
                                                step="any"
                                                class="form-control"
                                                id="importe"
                                                name="importe"
                                                v-model="importe"
                                                v-validate="{required: true, decimal: true}"
                                                data-vv-as="Importe"
                                                :class="{'is-invalid': errors.has('importe')}"
                                        >
                                        <div class="invalid-feedback" v-show="errors.has('importe')">{{ errors.first('importe') }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                            <!-- Observaciones -->
                                <div class="col-md-12">
                                    <div class="form-group error-content">
                                        <label for="observaciones">Observaciones</label>
                                        <textarea
                                                name="observaciones"
                                                id="observaciones"
                                                class="form-control"
                                                v-model="observaciones"
                                                v-validate="{required: true}"
                                                data-vv-as="Observaciones"
                                                :class="{'is-invalid': errors.has('observaciones')}"
                                        ></textarea>
                                        <div class="invalid-feedback" v-show="errors.has('observaciones')">{{ errors.first('observaciones') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </div>
                    </form>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div>
    </span>
</template>

<script>
    import SubcontratoSelect from "../../../cadeco/subcontrato/Select";
    export default {
        name: "solicitud-movimiento-fondo-garantia-create",
        components: {SubcontratoSelect},
        props: {'tipo_boton':{}, objFondoGarantia : {
            type: Object,
                default: () => ({})
            }},
        data() {
            return {
                id_fondo_garantia: '',
                id_tipo_solicitud: '',
                fecha: '',
                referencia: '',
                importe: '',
                observaciones: '',
                tipos_solicitud: {
                    1: "Liberación",
                    2: "Descuento"
                },
                cargando: false
            }
        },
        mounted() {
            /*this.getFondosGarantia()*/


        },

        methods: {
            init() {
                this.cargando = true;

                this.id_fondo_garantia = (this.objFondoGarantia.subcontrato)?this.objFondoGarantia.subcontrato.id:'';
                this.id_tipo_solicitud = 1;
                this.fecha = '';
                this.referencia = '';
                this.importe = '';
                this.observaciones = '';
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
                this.cargando = false;
                this.$validator.reset()
            },

            getFondosGarantia() {
                return this.$store.dispatch('contratos/fondo-garantia/fetch');
            },

            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.store()
                    }
                });
            },

            store() {
                return this.$store.dispatch('contratos/solicitud-movimiento-fg/store', this.$data)
                    .then((data) => {
                        $(this.$refs.modal).modal('hide');
                        this.$emit('created', data);
                    })
            }
        },

        computed: {
            fondosGarantia() {
                return this.$store.getters['contratos/fondo-garantia/fondosGarantia']
            },
        }
    }
</script>

<style scoped>
    .btn-primary {
        background-color: #00c0ef;
        border-color: #00acd6;
        color: #FFF;
    }
    button:checked{
        background-color: #5bc0de;
    }
    .btn-primary:hover {
        background-color: #5bc0de;
        border-color: #46b8da;
    }

    .error-label {
        width: 100%;
        margin-top: 0.25rem;
        font-size: 80%;
        color: #dc3545;
    }
</style>
