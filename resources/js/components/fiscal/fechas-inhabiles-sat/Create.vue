<template>
    <span>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">REGISTRAR FECHA  INHÁBILES</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group error-content">
                                        <label for="fecha" class="col-form-label">Fecha:</label>
                                        <datepicker v-model = "fecha"
                                                    name = "fecha"
                                                    :format = "formatoFecha"
                                                    :language = "es"
                                                    :bootstrap-styling = "true"
                                                    class = "form-control"
                                                    v-validate="{required: true}"
                                                    :class="{'is-invalid': errors.has('fecha')}"
                                        ></datepicker>
                                        <div class="invalid-feedback" v-show="errors.has('fecha')">{{ errors.first('fecha') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary" :disabled="errors.count() > 0" @click="validate"><i class="fa fa-save"></i> Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    import Datepicker from 'vuejs-datepicker';
    import {es} from 'vuejs-datepicker/dist/locale';
    export default {
        name: "fecha-inhabiles-create",
        components: {Datepicker, es},
        data() {
            return {
                es: es,
                cargando: false,
                fecha: '',
                fecha_hoy: '',
            }
        },
        mounted() {
            this.fecha_hoy = new Date();
            this.fecha = new Date();
        },
        methods: {
            init() {
                this.fecha = new Date();
                this.cargando = false;
            },
            formatoFecha(date) {
                return moment(date).format('DD/MM/YYYY');
            },
        }
    }
</script>

<style scoped>

</style>
