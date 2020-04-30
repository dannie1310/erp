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
                                <div class="col-md-4">
                                     <div class="form-group error-content">
                                        <label for="id_area">Área Subcontratante</label>
                                        <select
                                                type="text"
                                                name="id_area"
                                                data-vv-as="Area"
                                                v-validate="{required: true}"
                                                class="form-control"
                                                id="id_area"
                                                v-model="id_area"
                                                :class="{'is-invalid': errors.has('id_area')}"
                                        >
                                        <option  value selected>--- Seleccione Área Subcontratante ---</option>
                                        <option v-for="area in areas_subcontratantes" :value="area.id">{{ `${area.descripcion} ` }}</option>
                                        </select>
                                        <div class="invalid-feedback" v-show="errors.has('id_area')">{{ errors.first('id_area') }}</div>
                                    </div>
                                </div>
                            </div>
                             <div class="col-md-12  text-left" >
                                <label class="text-secondary"> </label>
                                <hr style="color: #0056b2; margin-top:auto;" width="95%" size="20" />
                            </div>
                            <!-- Seccion de partidas -->
                            <div class="row">
                                 <div  class="col-12">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th style="width:2%"></th>
                                                    <th style="width:2%"></th>
                                                    <th style="width:13%">Clave</th>
                                                    <th style="width:13%">Insumo</th>
                                                    <th style="width:24%">Descripción</th>
                                                    <th style="width:13%">Unidad</th>
                                                    <th style="width:13%">Cantidad</th>
                                                    <th style="width:15%">Destinos</th>
                                                    <th style="width:5%"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>A</td>
                                                    <td>B</td>
                                                    <td>CLAVE</td>
                                                    <td>INSUMO</td>
                                                    <td>DESCRIPCION</td>
                                                    <td>Select UNIDAD</td>
                                                    <td>CANTIDAD</td>
                                                    <td>Select DESTINOS</td>
                                                    <td>OPT</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
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
                areas_subcontratantes:[],
                id_area:'',
                partidas:[],
            }
        },
        mounted(){
            this.getAreaSub();
        },
        methods: {
            formatoFecha(date){
                return moment(date).format('DD/MM/YYYY');
            },
            getAreaSub() {
                this.areas_disponibles = [];
                return this.$store.dispatch('configuracion/area-subcontratante/index')
                    .then(data => {
                        this.areas_subcontratantes = data.sort((a, b) => (a.descripcion > b.descripcion) ? 1 : -1);
                    });
            },
        },
    }
</script>