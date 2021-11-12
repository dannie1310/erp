<template>
    <span>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <tabla-datos v-bind:id="id" @cargaFinalizada="iniciar"/>
                        <hr />
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group row error-content">
                                    <label for="motivo" class="col-md-2 col-form-label">Motivo:</label>
                                    <div class="col-md-10">
                                        <textarea
                                            name="motivo"
                                            id="motivo"
                                            class="form-control"
                                            v-model="motivo"
                                            v-validate="{required: true}"
                                            data-vv-as="Motivo"
                                            :class="{'is-invalid': errors.has('motivo')}"
                                        ></textarea>
                                        <div class="invalid-feedback" v-show="errors.has('motivo')">{{ errors.first('motivo') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="pull-right">
                            <button type="button" class="btn btn-secondary" v-on:click="salir">
                                <i class="fa fa-angle-left"></i>Regresar
                            </button>
                            <button type="submit" class="btn btn-danger" @click="validate" :disabled="errors.count() > 0 || motivo == '' || fin_carga == 0">
                                <i class="fa fa-trash"></i>
                                Eliminar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    import TablaDatos from "./partials/TablaDatos";
    export default {
        name: "delete-avance-obra",
        props: ['id'],
        components: { TablaDatos },
        data(){
            return{
                fin_carga: 0,
                motivo : ''
            }
        },
        methods: {
            iniciar() {
                this.fin_carga = 1;
            },
            salir() {
                this.$router.push({name: 'avance-obra'});
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        if(this.motivo == '') {
                            swal('¡Error!', 'Debe colocar un motivo para realizar la operación.', 'error')
                        }
                        else {
                            this.eliminar()
                        }
                    }
                });
            },
            eliminar() {
                this.cargando = true;
                return this.$store.dispatch('controlObra/avance-obra/eliminar', {
                    id: this.id,
                    params: {data: this.motivo}
                })
                .then(data => {
                   this.salir();
                })
                .finally( ()=>{
                    this.cargando = false;
                });
            },
        }
    }
</script>

<style scoped>

</style>
