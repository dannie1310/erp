<template>
    <span>
        <div class="row">
            <div class="col-12">
                <div class="invoice p-3 mb-3">
                    <div class="row">
                        <div class="col-12">
                            <h4>
                                <i class="fa fa-list"></i> Registro de Contrato Proyectado
                            </h4>
                        </div>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <!-- Seccion de datos iniciales -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-2 offset-md-10">
                                        <div class="form-group error-content">
                                            <div class="form-group">
                                                <label><b>Fecha</b></label>
                                                <datepicker v-model = "fecha"
                                                            name = "fecha"
                                                            :language = "es"
                                                            :format = "formatoFecha"
                                                            :bootstrap-styling = "true"
                                                            class = "form-control">
                                                </datepicker>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                    <!-- <div class="col-md-4  text-left" >
                                        <label class="text-secondary"> </label>
                                        <hr style="color: #0056b2; margin-top:auto;" width="95%" size="10" />
                                    </div> -->
                                    <div class="col-md-4 offset-md-8 mt-3 text-left" >
                                        <label class="text-secondary">Fechas Límite </label>
                                        <hr style="color: #0056b2; margin-top:auto;" width="95%" size="10" />
                                    </div>
                               


                                <div class="col-md-8">
                                    <div class="form-group error-content">
                                        <label for="numero">Referencia:</label>
                                        <input type="text" class="form-control"
                                                name="referencia"
                                                data-vv-as="Referencia"
                                                v-model="referencia"
                                                v-validate="{required: true}"
                                                :class="{'is-invalid': errors.has('referencia')}"
                                                id="referencia"
                                                placeholder="Referencia">
                                        <div class="invalid-feedback" v-show="errors.has('referencia')">{{ errors.first('referencia') }}</div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group error-content">
                                        <div class="form-group">
                                            <label><b>Fecha Cotización</b></label>
                                            <datepicker v-model = "fecha_cotizacion"
                                                        name = "fecha_cotizacion"
                                                        :language = "es"
                                                        :format = "formatoFecha"
                                                        :bootstrap-styling = "true"
                                                        class = "form-control">
                                            </datepicker>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group error-content">
                                        <div class="form-group">
                                            <label><b>Fecha Contratación</b></label>
                                            <datepicker v-model = "fecha_contrato"
                                                        name = "fecha_contrato"
                                                        :language = "es"
                                                        :format = "formatoFecha"
                                                        :bootstrap-styling = "true"
                                                        class = "form-control">
                                            </datepicker>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Seccion de partidas -->
                            <div class="row">

                            </div>




                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Guardar</button>
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
        name: "contrato-proyectado-create",
        components: {Datepicker},
        data() {
            return {
                es: es,
                cargando: false,
                fecha: '',
                fecha_cotizacion: '',
                fecha_contrato: '',
                referencia: '',
            }
        },
        methods: {
            formatoFecha(date){
                return moment(date).format('DD/MM/YYYY');
            },
        },
    }
</script>