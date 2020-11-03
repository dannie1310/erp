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
                                            <button type="button" class="btn btn-sm btn-outline-primary" @click="actualizaClave">
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
                                    <button type="button" class="btn btn-primary pull-right" @click="actualizaDatosSeguimiento" >
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
                                                    <button type="button" class="btn btn-sm btn-outline-danger" @click="quitarResponsable(responsable)" >
                                                        <i class="fa fa-trash"></i>
                                                    </button>
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

        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sn" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-plus"></i> AGREGAR RESPONSABLE</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label for="user_id" class="col-md-3 col-form-label">Usuario:</label>
                                    <div class="col-md-9">
                                        <usuario-select
                                            name="user_id"
                                            id="user_id"
                                            data-vv-as="Usuario"
                                            v-validate="{required: true, integer: true}"
                                            v-model="responsable.id_usuario"
                                            :error="errors.has('user_id')"
                                        >
                                        </usuario-select>
                                        <div class="error-label" v-show="errors.has('user_id')">{{ errors.first('user_id') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label for="user_id" class="col-md-3 col-form-label">Responsabilidad:</label>
                                    <div class="col-md-9">
                                        <div class="btn-group flex-wrap btn-group-toggle">
                                            <label class="btn btn-outline-primary" :class="responsable.responsabilidad === Number(llave) ? 'active': ''"  v-for="(responsabilidad, llave) in responsabilidades" :key="llave">
                                                <input type="radio"
                                                       class="btn-group-toggle"
                                                       name="responsabilidad"
                                                       :id="'responsabilidad' + llave"
                                                       :value="llave"
                                                       autocomplete="on"
                                                       v-model.number="responsable.responsabilidad"
                                                >
                                                {{ responsabilidad}}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fa fa-times-circle"></i>
                            Cerrar</button>
                        <button type="button" class="btn btn-primary" @click="guardarResponsable" >
                            <i class="fa fa-save"></i>
                            Guardar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
import Datepicker from 'vuejs-datepicker';
import {es} from 'vuejs-datepicker/dist/locale';
import UsuarioSelect from "../../igh/usuario/Select";

export default {
    name: "edit-concepto",
    props: ['id'],
    components:{Datepicker, UsuarioSelect},
    data() {
        return {
            es:es,
            cargando: true,
            responsable:{
                id_usuario:'',
                responsabilidad:1
            },
            clave:'',
            dato:{
                calificacion:'',
                fecha_fin:  '',
                fecha_inicio: '',
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
            responsabilidades: {
                3: "Responsable",
                2: "Carga",
                1: "Autoriza"
            },
        }
    },
    mounted() {
        this.cargando = true;
        this.dato.fecha_inicio = new Date();
        this.dato.fecha_fin = new Date();
        this.find()
    },
    methods:{
        actualizaClave(){
            return this.$store.dispatch('presupuesto/concepto/actualizaClave',
                {
                    clave:this.clave,
                    id_concepto:this.concepto.id,
                })
                .then((data) => {
                    this.clave = this.concepto.clave_concepto;
                });
        },
        validate() {
            if(moment(this.dato.fecha_fin).format('YYYY/MM/DD') < moment(this.dato.fecha_inicio).format('YYYY/MM/DD'))
            {
                swal('¡Error!', 'La fecha inicial no puede ser posterior a la fecha final.', 'error')
                return false;
            } else if(this.dato.calificacion == ''){
                swal('¡Error!', 'Debe seleccionar la importancia para el concepto', 'error')
                return false;
            } else if(this.dato.revision_diaria == false && this.dato.revision_mensual == false && this.dato.revision_semanal == false){
                swal('¡Error!', 'Debe seleccionar al menos una periodicidad de revisión para el concepto', 'error')
                return false;
            }
            else {
                return true;
            }
        },
        actualizaDatosSeguimiento(){
            if(this.validate()){
                return this.$store.dispatch('presupuesto/concepto/actualizaDatosSeguimiento',
                    {
                        datos:{
                            calificacion : this.dato.calificacion,
                            fecha_fin:  this.dato.fecha_fin,
                            fecha_inicio: this.dato.fecha_inicio,
                            revision_diaria:this.dato.revision_diaria,
                            revision_mensual:this.dato.revision_mensual,
                            revision_semanal:this.dato.revision_semanal,
                        },
                        id:this.concepto.id
                    })
                    .then((data) => {
                        this.dato = data.dato;
                    });
            }
        },
        find() {
            return this.$store.dispatch('presupuesto/concepto/find', {
                id: this.id,
                params: {include: ['dato','responsables.usuario']}
            }).then(data => {
                this.clave = this.concepto.clave_concepto;
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
            $(this.$refs.modal).appendTo('body')
            $(this.$refs.modal).modal('show');
        },
        guardarResponsable(){
            this.$validator.validate().then(result => {
                if (result) {
                    return this.$store.dispatch('presupuesto/concepto/storeResponsable',
                        {
                            id_usuario_responsable:this.responsable.id_usuario,
                            tipo:this.responsable.responsabilidad,
                            id_concepto:this.concepto.id,
                        })
                    .then((data) => {
                        this.responsable.responsabilidad=1;
                        this.responsable.id_usuario='';
                        $(this.$refs.modal).modal('hide');
                    });
                }
            });
        },
        quitarResponsable(responsable){
            return this.$store.dispatch('presupuesto/concepto/quitarResponsable', {
                id: responsable.id,
            }).then((data) => {

            })
        }
    },
    computed: {
        concepto() {
            return this.$store.getters['presupuesto/concepto/currentConcepto']
        },
        responsables() {
            return this.$store.getters['presupuesto/concepto/responsables']
        }
    }
}
</script>

<style scoped>
.form-control{
    padding: 0.25rem 0.2rem;
}

</style>
