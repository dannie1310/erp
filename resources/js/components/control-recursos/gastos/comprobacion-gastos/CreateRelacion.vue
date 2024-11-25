<template>
    <span>
        <div class="card" v-if="solicitud == null">
            <div class="card-body">
                <div class="row" >
                    <div class="col-md-12">
                        <div class="spinner-border text-success" role="status">
                           <span class="sr-only">Cargando...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card" v-else>
            <form role="form" @submit.prevent="validate">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <encabezado  v-bind:solicitud="solicitud" />
                            <hr />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group error-content">
                                <label>Empresa a Facturar:</label>
                                <h6>{{solicitud.empresa_descripcion}}</h6>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group error-content">
                                <label class="col-form-label">Ubicación:</label>
                                <h6>{{solicitud.ubicacion}}</h6>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group error-content">
                                <label class="col-form-label">Departamento:</label>
                                <h6>{{solicitud.departamento}}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-7">
                            <div class="form-group error-content">
                                <label class="col-form-label">Nombre:</label>
                                <h6>{{solicitud.empleado_descripcion}} </h6>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group error-content">
                                <label for="id_proyecto">Proyecto:</label>
                                <select class="form-control"
                                        data-vv-as="Proyecto"
                                        id="id_proyecto"
                                        name="id_proyecto"
                                        :class="{'is-invalid': errors.has('id_proyecto')}"
                                        v-validate="{required: true}"
                                        v-model="id_proyecto">
                                    <option value>-- Selecionar --</option>
                                    <option v-for="(p) in proyectos" :value="p.id">{{ p.ubicacion }}</option>
                                </select>
                                <div style="display:block" class="invalid-feedback" v-show="errors.has('id_proyecto')">{{ errors.first('id_proyecto') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group error-content">
                                <label for="fecha_inicial">Del:</label>
                                <datepicker v-model = "fecha_inicial"
                                            name = "fecha_inicial"
                                            :format = "formatoFecha"
                                            data-vv-as="Fecha Inicial"
                                            :language = "es"
                                            :bootstrap-styling = "true"
                                            class = "form-control"
                                            v-validate="{required: true}"
                                            :disabled-dates="fechasDeshabilitadas"
                                            :class="{'is-invalid': errors.has('fecha_inicial')}"/>
                                <div class="invalid-feedback" v-show="errors.has('fecha_inicial')">{{ errors.first('fecha_inicial') }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group error-content">
                                <label for="fecha_final">Al:</label>
                                <datepicker v-model = "fecha_final"
                                            name = "fecha_final"
                                            data-vv-as="Fecha Final"
                                            :format = "formatoFecha"
                                            :language = "es"
                                            :bootstrap-styling = "true"
                                            class = "form-control"
                                            v-validate="{required: true}"
                                            :disabled-dates="fechasDeshabilitadas"
                                            :class="{'is-invalid': errors.has('fecha_final')}"/>
                                <div class="invalid-feedback" v-show="errors.has('fecha_final')">{{ errors.first('fecha_final') }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group error-content">
                                <label class="col-form-label">Importe:</label>
                                <h6>{{solicitud.importe_format}} </h6>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="salir">Cerrar</button>
                    <button type="submit" class="btn btn-primary" :disabled="errors.count() > 0" @click="validate"><i class="fa fa-save"></i> Guardar</button>
                </div>
            </form>
        </div>
    </span>
</template>

<script>
import Encabezado from "./partials/Encabezado";
import TablaDatos from "./partials/TablaDatos";
import Datepicker from 'vuejs-datepicker';
import {es} from "vuejs-datepicker/dist/locale";
export default {
    name: "solicitud-create",
    components: {Encabezado, TablaDatos, Datepicker, es},
    props: ['id'],
    data() {
        return {
            cargando: false,
            solicitud: null,
            es : es,
            fechasDeshabilitadas : {},
            fecha_hoy : '',
            fecha_inicial: '',
            fecha_final: '',
            id_proyecto: '',
            proyectos: []
        }
    },
    mounted() {
        this.getProyectos();
        this.find();
        this.$validator.reset()
        this.fecha_hoy = new Date();
        this.fecha_inicial = new Date();
        this.fecha_final = new Date();
        this.fechasDeshabilitadas.from = new Date();
    },
    methods: {
        init() {
            this.fecha_inicial = new Date();
            this.fecha_final = new Date();
            this.cargando = true;
        },
        formatoFecha(date) {
            return moment(date).format('DD/MM/YYYY');
        },
        find() {
            this.cargando = true;
            return this.$store.dispatch('controlRecursos/solicitud-comprobacion/find', {
                id: this.id,
                params:{include: ['']}
            }).then(data => {
                this.solicitud = data
               // this.cuentas =  this.relacion.reembolsos.data[0].proveedor.cuentas.data;
                //this.solicitante =  this.relacion.reembolsos.data[0].id_solicitante;
            })
        },
        salir() {
            this.$router.push({name: 'comprobacion-gasto'});
        },
        validate() {
            this.$validator.validate().then(result => {
                if (result)
                {
                    if(moment(this.fecha_final).format('YYYY/MM/DD') < moment(this.fecha_inicial).format('YYYY/MM/DD'))
                    {
                        swal('¡Error!', 'La fecha de final no puede ser posterior a la fecha de inicial.', 'error')
                    }else {
                        this.store();
                    }
                }
            });
        },
        getProyectos() {
            return this.$store.dispatch('controlRecursos/ubicacion-relacion/index', {
                params: { }
            }).then(data => {
                this.proyectos = data.data;
            })
        },
        store() {
            var datos = {};
            datos["fecha_inicial"] = this.$data.fecha_inicial;
            datos["fecha_final"] = this.$data.fecha_final;
            datos["id_proyecto"] = this.$data.id_proyecto;
            datos["solicitud"] = this.$data.solicitud;
            return this.$store.dispatch('controlRecursos/comprobacion/store', datos)
                .then((data) => {
                    this.$emit('created', data)
                    this.salir();
                });
        },
    },
}
</script>

<style scoped>

</style>
