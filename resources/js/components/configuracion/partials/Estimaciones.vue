<template>
    <div class="card" id="configuracion-estimaciones" v-if="$root.can('editar_configuracion_finanzas')">
        <div class="card-header">
            <h3 class="card-title">Configuración Finanzas</h3>
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
            <h5 id="estimaciones">Estimaciones</h5>

            <fieldset class="form-group">
                <div class="row">
                    <legend class="col-form-label col-sm-2 pt-0"><b>Penalización / Devolución Penalización</b></legend>
                    <div class="col-sm-10">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="retencion_antes_iva1" value="1" v-model="form.retencion_antes_iva" :disabled="guardadosPreviamente">
                            <label class="form-check-label"> Antes de IVA</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="retencion_antes_iva0" value="0" v-model="form.retencion_antes_iva" :disabled="guardadosPreviamente">
                            <label class="form-check-label"> Después de IVA</label>
                        </div>
                    </div>
                </div>
            </fieldset>

            <fieldset class="form-group">
                <div class="row">
                    <legend class="col-form-label col-sm-2 pt-0"><b>Retenciones / Devolución de Retenciones</b></legend>
                    <div class="col-sm-10">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="deductiva_antes_iva1" value="1" v-model="form.deductiva_antes_iva" :disabled="guardadosPreviamente">
                            <label class="form-check-label"> Antes de IVA</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="deductiva_antes_iva2" value="0" v-model="form.deductiva_antes_iva" :disabled="guardadosPreviamente">
                            <label class="form-check-label"> Después de IVA</label>
                        </div>
                    </div>
                </div>
            </fieldset>

            <fieldset class="form-group">
                <div class="row">
                    <legend class="col-form-label col-sm-2 pt-0"><b>Préstamos de Materiales (Sin IVA)</b></legend>
                    <div class="col-sm-10">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="amortizacion_antes_iva1" value="1" v-model="form.amortizacion_antes_iva" :disabled="guardadosPreviamente">
                            <label class="form-check-label"> Antes de IVA</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="amortizacion_antes_iva0" value="0" v-model="form.amortizacion_antes_iva" :disabled="guardadosPreviamente">
                            <label class="form-check-label"> Después de IVA</label>
                        </div>
                    </div>
                </div>
            </fieldset>

            <fieldset class="form-group">
                <div class="row">
                    <legend class="col-form-label col-sm-2 pt-0"><b>Retención de Fondo de Garantía (mas IVA)</b></legend>
                    <div class="col-sm-10">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="retencion_antes_iva1" value="1" v-model="form.retencion_antes_iva" :disabled="guardadosPreviamente">
                            <label class="form-check-label"> Antes de IVA</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="retencion_antes_iva0" value="0" v-model="form.retencion_antes_iva" :disabled="guardadosPreviamente">
                            <label class="form-check-label"> Después de IVA</label>
                        </div>
                    </div>
                </div>
            </fieldset>

            <fieldset class="form-group">
                <div class="row">
                    <legend class="col-form-label col-sm-2 pt-0"><b>Descuento por Préstamo de Materiales (sin IVA)</b></legend>
                    <div class="col-sm-10">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="retencion_antes_iva1" value="1" v-model="form.retencion_antes_iva" :disabled="guardadosPreviamente">
                            <label class="form-check-label"> Antes de IVA</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="retencion_antes_iva0" value="0" v-model="form.retencion_antes_iva" :disabled="guardadosPreviamente">
                            <label class="form-check-label"> Después de IVA</label>
                        </div>
                    </div>
                </div>
            </fieldset>

            <fieldset class="form-group">
            <div class="row">
                <legend class="col-form-label col-sm-2 pt-0"><b>Descuento por Otros Prestamos</b></legend>
                <div class="col-sm-10">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="retencion_antes_iva1" value="1" v-model="form.retencion_antes_iva" :disabled="guardadosPreviamente">
                        <label class="form-check-label"> Antes de IVA</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="retencion_antes_iva0" value="0" v-model="form.retencion_antes_iva" :disabled="guardadosPreviamente">
                        <label class="form-check-label"> Después de IVA</label>
                    </div>
                </div>
            </div>
        </fieldset>

            <div class="form-group row">
                <div class="col">
                    <button type="submit" @click="validate" class="btn btn-outline-primary pull-right" :disabled="!cambio">
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
        name: "configuracion-estimaciones",
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
                return Boolean(this.datosContables.FormatoCuenta) && Boolean(this.datosContables.BDContPaq) && Boolean(this.datosContables.NumobraContPaq);
            },
            mensaje() {
                return this.guardadosPreviamente ? 'Los datos no pueden ser modificados porque ya han sido guardados previamente' : 'Una vez guardados los datos no va a ser posible editarlos';
            }
        }
    }
</script>