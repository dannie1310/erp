<template>
    <div class="card" id="configuracion-estimaciones" v-if="$root.can('editar_configuracion_finanzas_estimaciones')">
        <div class="card-header">
            <h6 class="card-title">Configuración Finanzas</h6>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-widget="collapse">
                    <i class="fa fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="alert alert-warning" v-if="form || bandera == 0">
                <h5><i class="icon fa fa-warning"></i>¡Atención!</h5>
                {{ mensaje }}
            </div>
            <div class="alert alert-warning" v-else>
                <h5><i class="icon fa fa-warning"></i>¡Atención!</h5>
                {{mensaje_store}}
            </div>
            <h5 id="estimaciones">Estimaciones</h5>


            <fieldset class="form-group">
                <div class="row">
                    <legend class="col-form-label col-sm-4 pt-0"><b>Penalización / Devolución Penalización</b></legend>
                    <div class="col-sm-8" v-if="form && bandera == 0">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="penalizacion_antes_iva1" value="1" v-model="form.penalizacion_antes_iva" :disabled="!bandera">
                            <label class="form-check-label"> Antes de IVA</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="penalizacion_antes_iva0" value="0" v-model="form.penalizacion_antes_iva" :disabled="!bandera">
                            <label class="form-check-label"> Después de IVA</label>
                        </div>
                    </div>
                    <div class="col-sm-8" v-else>
                        <div class="form-check form-check-inline">
                            <label class="form-check-label" style="cursor:pointer" >
                                <input class="form-check-input" type="radio" name="penalizacion_antes_iva1" v-model="data.penalizacion_antes_iva" value="1" :disabled="!bandera">
                            Antes de IVA</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <label class="form-check-label" style="cursor:pointer" >
                                <input class="form-check-input" type="radio" name="penalizacion_antes_iva0" v-model="data.penalizacion_antes_iva" value="0" :disabled="!bandera">
                            Después de IVA</label>
                        </div>
                    </div>
                </div>
            </fieldset>

            <fieldset class="form-group">
                <div class="row">
                    <legend class="col-form-label col-sm-4 pt-0"><b>Retenciones / Devolución de Retenciones</b></legend>
                    <div class="col-sm-8" v-if="form && bandera == 0">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="retenciones_antes_iva1" value="1" v-model="form.retenciones_antes_iva" :disabled="!bandera">
                            <label class="form-check-label"> Antes de IVA</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="retenciones_antes_iva0" value="0" v-model="form.retenciones_antes_iva"  :disabled="!bandera">
                            <label class="form-check-label"> Después de IVA</label>
                        </div>
                    </div>
                    <div class="col-sm-8" v-else>
                        <div class="form-check form-check-inline">
                            <label class="form-check-label" style="cursor:pointer" >
                                <input class="form-check-input" type="radio" name="retenciones_antes_iva1" v-model="data.retenciones_antes_iva" value="1" :disabled="!bandera">
                            Antes de IVA</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <label class="form-check-label" style="cursor:pointer" >
                                <input class="form-check-input" type="radio" name="retenciones_antes_iva0" v-model="data.retenciones_antes_iva" value="0" :disabled="!bandera">
                            Después de IVA</label>
                        </div>
                    </div>

                </div>
            </fieldset>

            <fieldset class="form-group">
                <div class="row">
                    <legend class="col-form-label col-sm-4 pt-0"><b>Retención de Fondo de Garantía (mas IVA)</b></legend>
                    <div class="col-sm-8" v-if="form && bandera == 0">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="ret_fon_gar_antes_iva1" value="1" v-model="form.ret_fon_gar_antes_iva" :disabled="!bandera">
                            <label class="form-check-label"> Antes de IVA</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="ret_fon_gar_antes_iva0" value="0" v-model="form.ret_fon_gar_antes_iva" :disabled="!bandera">
                            <label class="form-check-label"> Después de IVA</label>
                        </div>
                    </div>
                    <div class="col-sm-8" v-else>
                        <div class="form-check form-check-inline">
                            <label class="form-check-label" style="cursor:pointer" >
                                <input class="form-check-input" type="radio" name="ret_fon_gar_antes_iva1" v-model="data.ret_fon_gar_antes_iva" value="1" :disabled="!bandera">
                             Antes de IVA</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <label class="form-check-label" style="cursor:pointer" >
                                <input class="form-check-input" type="radio" name="ret_fon_gar_antes_iva0" v-model="data.ret_fon_gar_antes_iva" value="0" :disabled="!bandera">
                             Después de IVA</label>
                        </div>
                    </div>
                </div>
            </fieldset>

            <fieldset class="form-group">
                <div class="row">
                    <legend class="col-form-label col-sm-4 pt-0"><b>Descuento por Préstamo de Materiales (sin IVA)</b></legend>
                    <div class="col-sm-8" v-if="form && bandera == 0">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="desc_pres_mat_antes_iva1" value="1" v-model="form.desc_pres_mat_antes_iva"  :disabled="!bandera">
                            <label class="form-check-label"> Antes de IVA</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="desc_pres_mat_antes_iva0" value="0" v-model="form.desc_pres_mat_antes_iva"  :disabled="!bandera">
                            <label class="form-check-label"> Después de IVA</label>
                        </div>
                    </div>
                    <div class="col-sm-8" v-else>
                        <div class="form-check form-check-inline">
                            <label class="form-check-label" style="cursor:pointer" >
                                <input class="form-check-input" type="radio" name="desc_pres_mat_antes_iva1" v-model="data.desc_pres_mat_antes_iva" value="1" :disabled="!bandera">
                            Antes de IVA</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <label class="form-check-label" style="cursor:pointer" >
                                <input class="form-check-input" type="radio" name="desc_pres_mat_antes_iva0" v-model="data.desc_pres_mat_antes_iva" value="0" :disabled="!bandera">
                            Después de IVA</label>
                        </div>
                    </div>
                </div>
            </fieldset>

            <fieldset class="form-group">
            <div class="row">
                <legend class="col-form-label col-sm-4 pt-0"><b>Descuento por Otros Prestamos</b></legend>
                <div class="col-sm-8" v-if="form && bandera == 0">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="desc_otros_prest_antes_iva1" value="1" v-model="form.desc_otros_prest_antes_iva"  :disabled="!bandera">
                        <label class="form-check-label"> Antes de IVA</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="desc_otros_prest_antes_iva0" value="0" v-model="form.desc_otros_prest_antes_iva"  :disabled="!bandera">
                        <label class="form-check-label"> Después de IVA</label>
                    </div>
                </div>
                <div class="col-sm-8" v-else>
                    <div class="form-check form-check-inline">
                        <label class="form-check-label" style="cursor:pointer" >
                            <input class="form-check-input" type="radio" name="desc_otros_prest_antes_iva1" v-model="data.desc_otros_prest_antes_iva" value="1" :disabled="!bandera">
                        Antes de IVA</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <label class="form-check-label" style="cursor:pointer" >
                            <input class="form-check-input" type="radio" name="desc_otros_prest_antes_iva0" v-model="data.desc_otros_prest_antes_iva" value="0" :disabled="!bandera">
                        Después de IVA</label>
                    </div>
                </div>
            </div>
        </fieldset>

            <div class="form-group row">
                <div class="col">
                    <button type="submit" @click="validate" class="btn btn-outline-primary float-right" :disabled="!cambio || !cambio_store|| this.bandera == 0">
                        <i class="fa fa-save"></i>
                    </button>
                </div>
            </div>
        </div>
        <hr>
        <div class="card-body">
            <configuracion-remesa id="configuracion-remesa"></configuracion-remesa>
        </div>

    </div>
</template>

<script>
    import ConfiguracionRemesa from "./Remesa";
    export default {
        components: {ConfiguracionRemesa},
        props: ['datosEstimaciones'],
        name: "configuracion-estimaciones",
        data() {
            return {
                form: null,
                data:{
                    penalizacion_antes_iva: '',
                    retenciones_antes_iva: '',
                    ret_fon_gar_antes_iva: '',
                    desc_pres_mat_antes_iva: '',
                    desc_otros_prest_antes_iva: '',
                },
                bandera: 1,
                picked: 0,
            }
        },
        mounted() {
            if (typeof this.datosEstimaciones !== 'undefined') {
                this.form = JSON.parse(JSON.stringify(this.datosEstimaciones))
                this.bandera = 0;
            }
        },

        methods: {
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.store()
                    }
                });
            },

            store() {
                this.guardando = true;
                return this.$store.dispatch('finanzas/estimacion/store', {
                    data: this.data
                })
                    .then(data => {
                        if(data) {
                            this.$store.dispatch('cadeco/obras/find', {
                                id: this.currentObra.id_obra,
                                params: { include: ['configuracion', 'datosEstimaciones'] }
                            })
                                .then(data => {
                                    if(data) {
                                        this.$store.commit('auth/setObra', { obra: data });
                                        this.bandera = 0;
                                    }
                                })
                            this.$emit('create:datosEstimaciones', data);
                        }
                    })
                    .finally(() => {
                        this.guardando = false;
                    })
            },
        },

        computed: {
            currentObra(){
                return this.$store.getters['auth/currentObra']
            },
            cambio() {
                return JSON.stringify(this.form) != JSON.stringify(this.datosEstimaciones);
            },
            cambio_store() {
                return Boolean(this.data.penalizacion_antes_iva)&&Boolean(this.data.retenciones_antes_iva)&&Boolean(this.data.desc_pres_mat_antes_iva)&&Boolean(this.data.ret_fon_gar_antes_iva)&&Boolean(this.data.desc_otros_prest_antes_iva);
            },
            guardadosPreviamente() {
                return Boolean(this.datosEstimaciones);
            },
            mensaje() {
                return this.guardadosPreviamente ? 'Los datos no pueden ser modificados porque ya han sido guardados previamente' : 'Una vez guardados los datos no va a ser posible editarlos';
            },
            mensaje_modificado() {
                return 'Los datos no pueden ser modificados porque ya han sido guardados';
            },
            mensaje_store() {
                return 'Una vez guardados los datos no va a ser posible editarlos';
            }
        },
    }
</script>