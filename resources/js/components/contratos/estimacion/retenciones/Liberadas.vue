<template>
  <span>
        <button type="button" class="btn btn-primary float-right" title="Registrar" @click="init()" v-if="$root.can('registrar_liberacion_estimacion_subcontrato')" >
            <i class="fa fa-plus"></i>
        </button>
        <div class="row">
            <div class="col-md-12">
                <div class="modal fade" ref="modalLiberadas" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-plus"></i> Liberación</h5>
                                <button type="button" class="close" @click="cerrar()" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form role="form" @submit.prevent="validate">
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group row error-content">
                                                <label class="col-md-3 col-form-label">Retención a liberar:</label>
                                                <div class="col-md-9">
                                                    <model-list-select
                                                        :disabled="cargando"
                                                        name="id_retencion"
                                                        v-model="id_retencion"
                                                        v-validate="{required: true}"
                                                        option-value="id"
                                                        :custom-text="retencionDescripcion"
                                                        :list="retenciones"
                                                        :placeholder="!cargando?'Seleccionar o buscar por concepto de la retención':'Cargando...'"
                                                        :isError="errors.has(`id_retencion`)">
                                                    </model-list-select>
                                                    <div class="invalid-feedback" v-show="errors.has('id_retencion')">{{ errors.first('id_retencion') }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group row error-content">
                                                <label for="concepto" class="col-md-3 col-form-label">Concepto: </label>
                                                <div class="col-md-9">
                                                    <textarea
                                                        name="concepto"
                                                        id="concepto"
                                                        class="form-control"
                                                        v-model="concepto"
                                                        v-validate="{required: true, max:1024}"
                                                        data-vv-as="Concepto"
                                                        :class="{'is-invalid': errors.has('concepto')}"
                                                    ></textarea>
                                                    <div class="invalid-feedback" v-show="errors.has('concepto')">{{ errors.first('concepto') }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group row error-content">
                                                <label for="importe" class="col-md-3 col-form-label">Importe: </label>
                                                <div class="col-md-9">
                                                    <input
                                                        :disabled="id_retencion==''"
                                                        type="number"
                                                        step="any"
                                                        name="importe"
                                                        data-vv-as="Importe"
                                                        v-validate="{required: true, decimal:4, min_value:0.01, max_value:retencion.importe_disponible}"
                                                        class="form-control"
                                                        id="importe"
                                                        placeholder="Importe"
                                                        v-model="importe"
                                                        :class="{'is-invalid': errors.has('importe')}">
                                                    <div class="invalid-feedback" v-show="errors.has('importe')">{{ errors.first('importe') }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" @click="cerrar()">Cerrar</button>
                                    <button type="submit" class="btn btn-primary">Registrar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    import {ModelListSelect} from 'vue-search-select';
    export default {
        name: "aplicadas-create",
        components: {ModelListSelect},
        props: ['id'],
        data() {
            return {
                concepto: '',
                importe: '',
                retencion: '',
                cargando: false,
                retenciones: [],
                id_retencion:''
            }
        },
        mounted() {

        },
        methods: {
            retencionDescripcion(item) {
                return `[${item.tipo.tipo_retencion}] [${item.concepto}]- [Restan: ${item.importe_disponible_format}]`
            },
            getRetenciones() {
                this.cargando = true;
                return this.$store.dispatch('subcontratosEstimaciones/retencion/index', {
                    params: {
                        scope: ['disponiblesParaLiberar:' + this.id, 'disponible'],
                        include: 'tipo'
                    }
                })
                    .then(data => {
                        this.retenciones = data.data
                    })
                    .finally(() => {
                        this.cargando = false;
                    });
            },
            cerrar() {
                $(this.$refs.modalLiberadas).modal('hide');
            },
            init() {
                this.concepto = '';
                this.importe = '';
                this.id_retencion = '';
                this.retenciones = [];
                this.$validator.reset();
                $(this.$refs.modalLiberadas).modal('show');
                this.getRetenciones()
            },
            store() {
                this.cargando = true;
                return this.$store.dispatch('subcontratosEstimaciones/retencion-liberacion/store', {
                    id_transaccion: this.id,
                    concepto: this.concepto,
                    importe: this.importe,
                    id_retencion: this.id_retencion
                })
                    .then(data => {
                        this.$store.commit('subcontratosEstimaciones/retencion-liberacion/INSERT_LIBERACION', data);
                        $(this.$refs.modalLiberadas).modal('hide');
                    }).finally(() => {
                        this.cargando = false;
                    });
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.store();
                    }
                });
            },
        },
        watch: {
            id_retencion(value)
            {
                if(value)
                {
                    this.retenciones.filter(retencion => {
                        if(retencion.id === value)
                        {
                            this.retencion = retencion;
                            return this.importe = retencion.importe_disponible;
                        }
                    });
                }
            }
        }
    }
</script>

<style>

</style>
