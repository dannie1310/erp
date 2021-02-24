<template>
    <span>
        <nav>
            <div class="row">
                 <div class="col-12">
                     <div class="invoice p-3 mb-3">
                         <form role="form" @submit.prevent="validate">
                             <div class="modal-body">
                                 <div class="row">
                                     <div class="col-md-8"></div>
                                     <div class="col-md-4">
                                         <div class="form-group error-content">
                                             <label class="col-form-label">Fecha:</label>
                                             <datepicker v-model = "fecha"
                                                        name = "fecha"
                                                        :format = "formatoFecha"
                                                        :language = "es"
                                                        :bootstrap-styling = "true"
                                                        class = "form-control"
                                                        v-validate="{required: true}"
                                                        :disabled-dates="fechasDeshabilitadas"
                                                        :class="{'is-invalid': errors.has('fecha')}"
                                             />
                                             <div class="invalid-feedback" v-show="errors.has('fecha')">{{ errors.first('fecha') }}</div>
                                         </div>
                                     </div>
                                 </div>
                                 <div class="col-md-12">
                                     <div class="form-group error-content">
                                        <label for="id_fondo">Fondo</label>
                                         <fondo
                                            name = "id_fondo"
                                            id = "id_fondo"
                                            v-model="id_fondo"
                                         ></fondo>
                                        <!-- <fondo-select
                                            scope="sinCuenta"
                                            name="id_fondo"
                                            id="id_fondo"
                                            data-vv-as="Fondo"
                                            v-validate="{required: true}"
                                            v-model="id_fondo"
                                            :error="errors.has('id_fondo')">
                                        ></fondo-select>-->
                                        <div class="error-label" v-show="errors.has('id_fondo')">{{ errors.first('id_fondo') }}</div>
                                    </div>
                                 </div>
                             </div>
                         </form>
                     </div>
                 </div>
            </div>
        </nav>
    </span>
</template>

<script>
    import Datepicker from 'vuejs-datepicker';
    import {es} from 'vuejs-datepicker/dist/locale';
    import Fondo from '../../cadeco/fondo/Select';
    export default {
        name: "comprobante-fondo-create",
        components: {Datepicker, es, Fondo},
        data() {
            return {
                cargando : false,
                es : es,
                fechasDeshabilitadas : {},
                fecha : '',
                fecha_hoy : '',
                id_fondo : ''
            }
        },
        mounted() {
            this.$validator.reset()
            this.fecha_hoy = new Date();
            this.fecha = new Date();
            this.fechasDeshabilitadas.from = new Date();
            this.id_fondo = ''
        },
        methods : {
            init() {
                this.fecha = new Date();
                this.cargando = true;
            },
            formatoFecha(date)
            {
                return moment(date).format('DD/MM/YYYY');
            },
        }
    }
</script>

<style scoped>

</style>
