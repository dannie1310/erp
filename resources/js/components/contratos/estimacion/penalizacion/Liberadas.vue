<template>
  <span>
        <button type="button" class="btn btn-primary float-right" title="Registrar" @click="init()" v-if="$root.can('registrar_liberacion_penalizacion_estimacion_subcontrato')">
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
                                                <label class="col-md-3 col-form-label">Penalización a liberar:</label>
                                                <div class="col-md-9">
                                                    <model-list-select
                                                        :disabled="cargando"
                                                        name="id_penalizacion"
                                                        v-model="id_penalizacion"
                                                        v-validate="{required: true}"
                                                        option-value="id"
                                                        :custom-text="penalizacionDescripcion"
                                                        :list="penalizaciones"
                                                        :placeholder="!cargando?'Seleccionar o buscar por concepto de la penalización':'Cargando...'"
                                                        :isError="errors.has(`id_penalizacion`)">
                                                    </model-list-select>
                                                    <div class="invalid-feedback" v-show="errors.has('id_penalizacion')">{{ errors.first('id_penalizacion') }}</div>
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
                                                        :disabled="cargando"
                                                        type="number"
                                                        step="any"
                                                        name="importe"
                                                        data-vv-as="Importe"
                                                        v-validate="{required: true, decimal:4, min_value:0.01, max_value:penalizacion.importe_disponible}"
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
                penalizacion: '',
                cargando: false,
                penalizaciones: [],
                id_penalizacion:''
            }
        },
        mounted() {

        },
        methods: {
            penalizacionDescripcion(item) {
                return `[${item.concepto}]- [Restan: ${item.importe_disponible_format}]`
            },
            getPenalizaciones() {
                this.cargando = true;
                return this.$store.dispatch('subcontratosEstimaciones/penalizacion/index', {
                    params: {
                        scope: ['disponiblesParaLiberar:' + this.id, 'disponible']
                    }
                })
                    .then(data => {
                        this.penalizaciones = data.data
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
                this.id_penalizacion = '';
                this.penalizaciones = [];
                this.$validator.reset();
                $(this.$refs.modalLiberadas).modal('show');
                this.getPenalizaciones();
            },
            store() {

                this.cargando = true;
                return this.$store.dispatch('subcontratosEstimaciones/penalizacion-liberacion/store', {
                    id_transaccion: this.id,
                    concepto: this.concepto,
                    importe: this.importe,
                    id_penalizacion: this.id_penalizacion
                })
                    .then(data => {
                        this.$store.commit('subcontratosEstimaciones/penalizacion-liberacion/INSERT_LIBERACION', data);
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
            id_penalizacion(value)
            {
                if(value)
                {                    
                    this.penalizaciones.filter(penalizacion => {
                        if(penalizacion.id === value)
                        {
                            this.penalizacion = penalizacion;
                            return this.importe = penalizacion.importe_disponible;
                        }
                    });
                }
            }
        }
    }
</script>

<style>

</style>
