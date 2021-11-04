<template>
    <div class="card" id="configuracion-contable" v-if="$root.can('editar_configuracion_contable')">
        <div class="card-header">
            <h6 class="card-title">Configuración Contable</h6>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-widget="collapse">
                    <i class="fa fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body" v-if="form">
            <div class="alert alert-warning">
                <h5><i class="icon fa fa-warning"></i>¡Atención!</h5>
                {{ mensaje }}
            </div>

            <fieldset class="form-group">
                <div class="row">
                    <legend class="col-form-label col-sm-2 pt-0"><b>Afectación Contable de Almacenes</b></legend>
                    <div class="col-sm-10">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="manejo_almacenes1" value="1" v-model="form.manejo_almacenes" :disabled="guardadosPreviamente">
                            <label class="form-check-label"> Si</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="manejo_almacenes0" value="0" v-model="form.manejo_almacenes" :disabled="guardadosPreviamente">
                            <label class="form-check-label"> No</label>
                        </div>
                    </div>
                </div>
            </fieldset>

            <fieldset class="form-group">
                <div class="row">
                    <legend class="col-form-label col-sm-2 pt-0"><b>Aplicación de Costo</b></legend>
                    <div class="col-sm-10">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="costo_en_tipo_gasto1" value="1" v-model="form.costo_en_tipo_gasto" :disabled="guardadosPreviamente">
                            <label class="form-check-label"> Por Tipo de Gasto</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="costo_en_tipo_gasto0" value="0" v-model="form.costo_en_tipo_gasto" :disabled="guardadosPreviamente">
                            <label class="form-check-label"> Por Conceptos Presupuesto</label>
                        </div>
                    </div>
                </div>
            </fieldset>

            <hr>
            <h5 id="contpaq">ContPaq</h5>

            <div class="form-group row">
                <label for="BDContPaq" class="col-sm-2 col-form-label">Base de Datos CONTPAQ</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="BDContPaq" v-model="form.BDContPaq" :disabled="guardadosPreviamente"
                           v-validate="{ max: 255 }"
                           name="BDContPaq"
                           data-vv-as="Base de Datos CONTPAQ"
                           :class="{'is-invalid': errors.has('BDContPaq')}"
                    >
                    <div class="invalid-feedback" v-show="errors.has('BDContPaq')">{{ errors.first('BDContPaq') }}</div>
                </div>
            </div>

            <div class="form-group row">
                <label for="NumobraContPaq" class="col-sm-2 col-form-label">Número de Obra CONTPAQ</label>
                <div class="col-sm-4">
                    <input type="number" class="form-control" id="NumobraContPaq" v-model="form.NumobraContPaq" :disabled="guardadosPreviamente"
                           v-validate="{ integer: true }"
                           name="NumobraContPaq"
                           data-vv-as="Número de Obra CONTPAQ"
                           :class="{'is-invalid': errors.has('NumobraContPaq')}"
                    >
                    <div class="invalid-feedback" v-show="errors.has('NumobraContPaq')">{{ errors.first('NumobraContPaq') }}</div>
                </div>
<!--            </div>

            <div class="form-group row">-->
                <label for="FormatoCuenta" class="col-sm-2 col-form-label">Formato de Cuentas</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" id="FormatoCuenta" v-model="form.FormatoCuenta" :disabled="guardadosPreviamente"
                           v-validate="{ regex: '^\#[\#\-]+\#$' }"
                           name="FormatoCuenta"
                           data-vv-as="Formato de Cuentas"
                           :class="{'is-invalid': errors.has('FormatoCuenta')}"
                    >
                    <div class="invalid-feedback" v-show="errors.has('FormatoCuenta')">{{ errors.first('FormatoCuenta') }}</div>
                </div>
            </div>
            <div class="form-group row">
                <div class="col">
                    <button type="submit" @click="validate" class="btn btn-outline-primary float-right" :disabled="!cambio || guardadosPreviamente">
                        <i class="fa fa-save"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['datosContables'],
        name: "configuracion-contable",
        data() {
            return {
                form: null
            }
        },
        mounted() {
            this.form = JSON.parse(JSON.stringify(this.datosContables))
        },

        methods: {
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.update()
                    }
                });
            },

            update() {
                this.guardando = true;
                return this.$store.dispatch('contabilidad/datos-contables/update', {
                    id: this.datosContables.id,
                    data: this.form
                })
                    .then(data => {
                        if(data) {
                            this.$store.dispatch('cadeco/obras/find', {
                                id: this.datosContables.id_obra,
                                params: { include: ['configuracion', 'datosContables'] }
                            })
                                .then(data => {
                                    if(data) {
                                        this.$store.commit('auth/setObra', { obra: data });
                                    }
                                })
                            this.$emit('update:datosContables', data);
                        }
                    })
                    .finally(() => {
                        this.guardando = false;
                    })
            }
        },

        computed: {
            cambio() {
                return JSON.stringify(this.form) != JSON.stringify(this.datosContables);
            },
            guardadosPreviamente() {
                return Boolean(parseInt(this.datosContables.estado));
            },
            mensaje() {
                return this.guardadosPreviamente ? 'Los datos no pueden ser modificados porque ya han sido guardados previamente' : 'Una vez guardados los datos no va a ser posible editarlos';
            }
        }
    }
</script>