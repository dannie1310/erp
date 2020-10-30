<template>
    <span>
        <div v-if="concepto">
            <div class="card">
                <div class="card-header">
                    <div class="row" >
                        <div class="col-md-12">
                            {{ concepto.path }}
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row" >
                        <div class="col-md-3">
                            <div class="card">
                            <div class="card-body">
                            <div class="row" >
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        <label for="clave" class="col-md-4 col-form-label" style="text-align: left">Clave: </label>
                                        <div class="col-md-8 form-inline">
                                            <input type="text" class="form-control" id="clave" v-model="clave" maxlength="140">
                                            <button type="submit" class="btn btn-sm btn-outline-primary">
                                                <i class="fa fa-save"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row" >
                                <div class="col-md-12">

                                    <div class="form-group row">
                                        <label class="col-md-4 col-form-label">Cantidad:</label>
                                        <div class="col-md-8 form-inline">
                                        {{concepto.cantidad_presupuestada_format}} {{concepto.unidad}}
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        <label class="col-md-4 col-form-label">Precio Unitario:</label>
                                        <div class="col-md-8 form-inline">
                                        {{concepto.precio_unitario_format}}
                                        </div>

                                    </div>
                                </div>
                            </div>
                                <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        <label class="col-md-4 col-form-label">Monto Presupuestado:</label>
                                        <div class="col-md-8 form-inline">
                                        {{concepto.monto_presupuestado_format}}
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        <label class="col-md-4 col-form-label">Tipo de Concepto:</label>
                                        <div class="col-md-8 form-inline">
                                        {{concepto.tipo}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="fa fa-chart-line"></i>Datos para seguimiento</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row" >
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label >Importancia:</label>
                                                <div class="row">
                                                        <div class="btn-group flex-wrap btn-group-toggle">
                                                            <label class="btn btn-outline-primary" :class="dato.calificacion === Number(llave) ? 'active': ''"  v-for="(importancia, llave) in importancias" :key="llave">
                                                                <input type="radio"
                                                                       class="btn-group-toggle"
                                                                       name="importancia"
                                                                       :id="'importancia' + llave"
                                                                       :value="llave"
                                                                       autocomplete="on"
                                                                       v-model.number="dato.calificacion"
                                                                >
                                                                {{ importancia}}
                                                            </label>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label >Periodicidad de Revisión:</label>
                                                <div class="row">
                                                        <div class="btn-group flex-wrap btn-group-toggle" data-toggle="buttons">
                                                            <label class="btn btn-outline-primary">
                                                                <input type="checkbox"  autocomplete="off" v-model="dato.revision_diaria"> Diario
                                                            </label>
                                                             <label class="btn btn-outline-primary">
                                                                <input type="checkbox"  autocomplete="off" v-model="dato.revision_semanal"> Semana
                                                            </label>
                                                            <label class="btn btn-outline-primary">
                                                                <input type="checkbox"  autocomplete="off" v-model="dato.revision_mensual"> Mes
                                                            </label>
                                                        </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group error-content">
                                                <label >Rango de Fechas:</label>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <datepicker
                                                            v-model ="dato.fecha_inicio"
                                                            name = "fecha_inicial"
                                                            :format = "formatoFecha"
                                                            :language = "es"
                                                            :bootstrap-styling = "true"
                                                            class = "form-control"
                                                            v-validate="{required: true}"
                                                            :class="{'is-invalid': errors.has('fecha_inicial')}"
                                                        ></datepicker>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <datepicker
                                                            v-model ="dato.fecha_fin"
                                                            name = "fecha_fin"
                                                            :format = "formatoFecha"
                                                            :language = "es"
                                                            :bootstrap-styling = "true"
                                                            class = "form-control"
                                                            v-validate="{required: true}"
                                                            :class="{'is-invalid': errors.has('fecha_fin')}"
                                                        ></datepicker>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="button" class="btn btn-primary pull-right" >
                                        <i class="fa fa-save"></i>
                                        Guardar
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="fa fa-users-cog"></i>Responsables</h3>
                                </div>
                                <div class="card-body">

                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th class="bg-gray-light index_corto">#</th>
                                                <th class="bg-gray-light">Nombre</th>
                                                <th class="bg-gray-light">Responsabilidad</th>
                                                <th class="bg-gray-light icono">
                                                    <button type="button" class="btn btn-sm btn-outline-success" @click="agregarResponsable" :disabled="cargando">
                                                        <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                                                        <i class="fa fa-plus" v-else></i>
                                                    </button>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(responsable, i) in responsables" >
                                                <td>
                                                    {{i+1}}
                                                </td>
                                                <td>
                                                    {{responsable.usuario.nombre}}
                                                </td>
                                                <td>
                                                    {{responsable.responsabilidad}}
                                                </td>
                                                <td>

                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
import Datepicker from 'vuejs-datepicker';
import {es} from 'vuejs-datepicker/dist/locale';

export default {
    name: "edit-concepto",
    props: ['id'],
    components:{Datepicker, es},
    data() {
        return {
            es:es,
            cargando: true,
            responsables:{},
            clave:'',
            /*concepto_edicion :{

            },*/
            dato:{
                calificacion:'',
                fecha_fin:'',
                fecha_inicio:'',
                id:'',
                revision_diaria:false,
                revision_mensual:false,
                revision_semanal:false,
            },
            importancias: {
                5: "5",
                4: "4",
                3: "3",
                2: "2",
                1: "1"
            },
        }
    },
    mounted() {
        this.cargando = true;
        this.find()
    },
    methods:{
        actualizaFechaInicio(e,id){
            this.$store.commit('presupuesto/concepto/UPDATE_CONCEPTO_DATO', {fecha_inicio : e, id : id})
        },
        find() {
            return this.$store.dispatch('presupuesto/concepto/find', {
                id: this.id,
                params: {include: ['dato','responsables.usuario']}
            }).then(data => {
                this.clave = this.concepto.clave_concepto;
                this.responsables = this.concepto.responsables.data;
                if(this.concepto.dato){
                    this.dato = this.concepto.dato;
                }
            }).finally(() => {
                this.cargando = false;
            })
        },
        formatoFecha(date){
            return moment(date).format('DD/MM/YYYY');
        },
        agregarResponsable(){

        }
    },
    computed: {
        concepto() {
            return this.$store.getters['presupuesto/concepto/currentConcepto']
        }
    }
}
</script>

<style scoped>
.form-control{
    padding: 0.25rem 0.2rem;
}

</style>
