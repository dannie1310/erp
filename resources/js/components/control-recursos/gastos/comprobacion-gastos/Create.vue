<template>
    <span>
        <div class="card" v-if="cargando">
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
                        <div class="col-md-4">
                            <label for="idserie" class="col-form-label">Serie:</label>
                            <select class="form-control"
                                    :disabled="cargando"
                                    data-vv-as="Serie"
                                    id="idserie"
                                    name="idserie"
                                    :error="errors.has('idserie')"
                                    v-validate="{required: true}"
                                    v-model="idserie">
                                <option value>-- Selecionar --</option>
                                <option v-for="(serie) in series" :value="serie.id">{{ serie.descripcion }}</option>
                            </select>
                            <div style="display:block" class="invalid-feedback" v-show="errors.has('idserie')">{{ errors.first('idserie') }}</div>
                        </div>
                        <div class="col-md-6" v-if="idserie">
                            <label for="id_empleado" class="col-form-label">Buscar Empleado:</label>
                            <select class="form-control"
                                    :disabled="cargando_empleados"
                                    data-vv-as="Empleado"
                                    id="id_empleado"
                                    name="id_empleado"
                                    :class="{'is-invalid': errors.has('id_empleado')}"
                                    v-validate="{required: true}"
                                    v-model="idempleado">
                                <option value>-- Selecionar --</option>
                                <option v-for="(p) in empleados" :value="p.id">{{ p.razon_social }}</option>
                            </select>
                            <div style="display:block" class="invalid-feedback" v-show="errors.has('id_empleado')">{{ errors.first('id_empleado') }}</div>
                            <div v-if="cargando_empleados" class="spinner-border  spinner-border-sm text-primary ml-2" role="status"></div>
                        </div>
                    </div>
                    <hr />
                    <div class="row" v-if="idempleado != ''">
                       <div class="col-12 d-flex justify-content-end">
                           <button type="button" class="btn btn-secondary btn-sm mb-2" :disabled="solicitudes.length == 0"
                                @click="tablaOculta ? mostrarTabla() : ocultarTabla()">
                                <i class="fa" :class="tablaOculta ? 'fa fa-exchange-alt': 'fa-eye-slash'"></i> {{ tablaOculta ? 'Cambiar Solicitud' : 'Ocultar Tabla' }}
                            </button>
                       </div>
                    </div>
                    <div class="row" v-if="idempleado != ''">
                        <div  class="col-md-12 table-responsive-xl" v-if="!tablaOculta">
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead>
                                    <tr>
                                        <th class="index_corto encabezado">#</th>
                                        <th class="c30 encabezado"></th>
                                        <th class="c80 encabezado">Fecha</th>
                                        <th class="c80 encabezado">Solicitud</th>
                                        <th class="c100 encabezado">Estado</th>
                                        <th class="c100 encabezado">Importe</th>
                                        <th class="c100 encabezado">Comprobado</th>
                                        <th class="c100 encabezado">Saldo</th>
                                        <th class="c100 encabezado">Moneda</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr  v-if="cargando_solicitudes">
                                        <th colspan="9"><div class="spinner-border  spinner-border-sm text-primary ml-2" role="status"></div></th>
                                    </tr>
                                    <tr v-else v-for="(partida, i) in solicitudes" :key="partida.id">
                                        <td style="text-align:center; vertical-align:inherit;">{{i+1}}</td>
                                        <td style="text-align: center; vertical-align: inherit">
                                            <input type="radio" :id="'solicitud_' + partida.id " :value="partida.id" v-model="id_solicitud" @change="ocultarTabla"/>
                                        </td>
                                        <td>
                                            {{ partida.fecha_format }}
                                        </td>
                                        <td>
                                            {{ partida.folio_format }}
                                        </td>
                                        <td>
                                            {{ partida.estado_format }}
                                        </td>
                                        <td>
                                            ${{ partida.importe_format }}
                                        </td>
                                        <td>
                                            {{ partida.saldo_format }}
                                        </td>
                                         <td>
                                            {{ partida.total_format }}
                                        </td>
                                        <td>
                                            {{ partida.moneda }}
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row" v-if="cargando_solicitud">
                        <div class="col-md-12">
                            <div class="spinner-border text-success" role="status">
                               <span class="sr-only">Cargando...</span>
                            </div>
                        </div>
                    </div>
                    <div class="row"  v-if="!cargando_solicitud && solicitud != null">
                        <div class="col-md-12">
                            <encabezado  v-bind:solicitud="solicitud" />
                        </div>
                    </div>
                    <div class="row" v-if="!cargando_solicitud && solicitud != null">
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
                    <div class="row" v-if="!cargando_solicitud && solicitud != null">
                        <div class="col-md-7">
                            <div class="form-group error-content">
                                <label class="col-form-label">Nombre:</label>
                                <h6>{{solicitud.empleado_descripcion}} </h6>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group error-content">
                                <label for="id_proyecto">Obra / Proyecto:</label>
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
                    <div class="row" v-if="!cargando_solicitud && solicitud != null">
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
                                <h6>{{solicitud.saldo_format}} </h6>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" v-if="solicitudes.length > 0 && solicitud != null">
                    <button type="button" class="btn btn-secondary" @click="salir">Cerrar</button>
                    <button type="submit" class="btn btn-primary" :disabled="errors.count() > 0"><i class="fa fa-save"></i> Guardar</button>
                </div>
            </form>
        </div>
    </span>
</template>

<script>
import Encabezado from "./partials/Encabezado";
import Datepicker from 'vuejs-datepicker';
import {es} from "vuejs-datepicker/dist/locale";
export default {
    name: "comprobacion-create",
    components: {Encabezado, Datepicker, es},
    data() {
        return {
            cargando: false,
            cargando_empleados: false,
            cargando_solicitudes: false,
            cargando_solicitud: false,
            solicitudes: [],
            id_solicitud: '',
            solicitud: null,
            es : es,
            fechasDeshabilitadas : {},
            fecha_hoy : '',
            fecha_inicial: '',
            fecha_final: '',
            id_proyecto: '',
            proyectos: [],
            series: [],
            idserie: '',
            empleados: [],
            idempleado: '',
            tablaOculta: false,
        }
    },
    mounted() {
        this.getProyectos();
        this.getSeries();
        this.$validator.reset()
        this.fecha_hoy = new Date();
        this.fecha_inicial = new Date();
        this.fecha_final = new Date();
        this.fechasDeshabilitadas.from = new Date();
        this.solicitudes = [];
        this.id_solicitud = '';
        this.solicitud = null;
    },
    methods: {
        getSeries() {
            return this.$store.dispatch('controlRecursos/serie/index', {
                params: {sort: 'descripcion', order: 'asc'}
            })
                .then(data => {
                    this.series = data.data;
                    if(this.series.length == 1)
                    {
                        this.idserie = this.series[0].id;
                    }
                })
                .finally(() => {
                    this.cargando = false;
                })
        },
        getSolicitudes()
        {
            this.cargando_solicitudes = true;
            return this.$store.dispatch('controlRecursos/solicitud-comprobacion/index', {
                params: { sort: 'timestampRegistro', order: 'asc', include: ['comprobacion'], scope: ['documentosParaRegistrar:' + this.idserie + ','+ this.idempleado ]},
            }).then(data => {
                this.solicitudes = data.data;

            }).finally(() => {
                this.cargando_solicitudes = false;
            })
        },
        getEmpleados()
        {
            this.cargando_empleados = true;
            return this.$store.dispatch('controlRecursos/proveedor/index', {
                params: { sort: 'RazonSocial', order: 'asc', scope: ['proveedorDisponibleParaRegistrar:' + this.idserie]},
            }).then(data => {
                this.empleados = data.data;
            }).finally(()=>{
                this.cargando_empleados = false;
            })
        },
        init() {
            this.fecha_inicial = new Date();
            this.fecha_final = new Date();
            this.cargando = true;
            this.solicitudes = [];
            this.id_solicitud = '';
            this.solicitud = null;
        },
        formatoFecha(date) {
            return moment(date).format('DD/MM/YYYY');
        },
        find() {
            this.cargando_solicitud = true;
            return this.$store.dispatch('controlRecursos/solicitud-comprobacion/find', {
                id: this.id_solicitud,
                params:{include: ['']}
            }).then(data => {
                this.solicitud = data
            }).finally(() => {
                this.cargando_solicitud = false;
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
                        swal('¡Error!', 'La fecha de final no puede ser anterior a la fecha de inicial.', 'error')
                    }else {
                        this.store();
                    }
                }
            });
        },
        getProyectos() {
            this.cargando = true;
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
        ocultarTabla() {
            this.tablaOculta = true;
        },
        mostrarTabla() {
            this.tablaOculta = false;
        }
    },
    watch: {
        id_solicitud(value)
        {
            if(value !== '' && value !== null && value !== undefined)
            {
                this.solicitud = null;
                this.find();
            }
        },
        idserie(value)
        {
            if(value !== '' && value !== null && value !== undefined)
            {
                this.$validator.reset();
                this.solicitud = null;
                this.solicitudes = [];
                this.id_solicitud = '';
                this.solicitud = null;
                this.empleados = [];
                this.idempleado = '';
                this.tablaOculta = false;
                this.getEmpleados();
            }
        },
        idempleado(value)
        {
            if(value !== '' && value !== null && value !== undefined)
            {
                this.$validator.reset()
                this.solicitudes = [];
                this.id_solicitud = '';
                this.solicitud = null;
                this.tablaOculta =  false;
                this.getSolicitudes();
            }
        }
    }
}
</script>

<style scoped>

</style>
