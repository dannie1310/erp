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
                                    <div class="form-group error-content">
                                        <label >Clave:</label>
                                        {{concepto.clave_concepto}}
                                    </div>
                                </div>
                            </div>
                            <div class="row" >
                                <div class="col-md-12">
                                    <div class="form-group error-content">
                                        <label >Cantidad:</label>
                                        {{concepto.cantidad_presupuestada}} {{concepto.unidad}}

                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group error-content">
                                        <label >Precio Unitario:</label>
                                        {{concepto.precio_unitario}}

                                    </div>
                                </div>
                            </div>
                                <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group error-content">
                                        <label >Monto Presupuestado:</label>
                                        {{concepto.monto_presupuestado}}

                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group error-content">
                                        <label >Tipo de Concepto:</label>
                                        {{concepto.tipo}}

                                    </div>
                                </div>
                            </div>
                            </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row" >
                                        <div class="col-md-6">
                                            <div class="form-group error-content">
                                                <label >Importancia:</label>
                                                <div class="row">
                                                    <div class="btn-group btn-group-toggle">
                                                        <label class="btn btn-outline-secondary" :class="dato.calificacion === Number(llave) ? 'active': ''"  v-for="(importancia, llave) in importancias" :key="llave">
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
                                            <div class="form-group error-content">
                                                <label >Periodicidad de Revisión:</label>
                                                <div class="row">
                                                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                                        <label class="btn btn-outline-secondary">
                                                            <input type="checkbox"  autocomplete="off" v-model="dato.revision_diaria"> Diario
                                                        </label>
                                                         <label class="btn btn-outline-secondary">
                                                            <input type="checkbox"  autocomplete="off" v-model="dato.revision_semanal"> Semana
                                                        </label>
                                                        <label class="btn btn-outline-secondary">
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
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="row">

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
                params: {include: ['dato']}
            }).then(data => {
                //this.concepto_edicion = this.$store.getters['presupuesto/concepto/currentConcepto'];
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
    },
    computed: {
        concepto() {
            return this.$store.getters['presupuesto/concepto/currentConcepto']
        }
    }
}
</script>

<style scoped>

</style>
