<template>
    <span>
        <div class="card" v-if="registro==false && relacion == null && reembolso == null">
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
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12" v-if="relacion.estado == 5">
                        <h4>Documento para Relación</h4>
                    </div>
                    <div class="col-md-12" v-else>
                        <h4>Documento para Reembolso</h4>
                    </div>
                </div>
                <div class="row" v-if="relacion.estado == 5">
                    <div class="col-md-12">
                        <encabezado v-bind:relacion="relacion" />
                        <resumen v-bind:relacion="relacion" />
                    </div>
                </div>
                <div class="row" v-if="relacion.estado == 6">
                    <div class="col-md-12">
                        <encabezado-reembolso v-bind:reembolso="reembolso" />
                        <tabla-datos-reembolso v-bind:reembolso="reembolso" />
                        <hr />
                        <documentos v-bind:documentos="reembolso.documentos" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="pull-right">
                    <button type="submit" class="btn btn-primary" :disabled="errors.count() > 0" @click="validate" v-if="relacion.estado == 5"><i class="fa fa-save"></i> Guardar</button>
                    <button type="submit" class="btn btn-info" :disabled="errors.count() > 0" @click="editar" v-if="relacion.estado == 6"><i class="fa fa-save" ></i> Actualizar</button>
                    <button type="submit" class="btn btn-danger" :disabled="errors.count() > 0" @click="eliminar" v-if="relacion.estado == 6"><i class="fa fa-trash"></i> Eliminar</button>
                    <button type="button" class="btn btn-secondary" v-on:click="salir"><i class="fa fa-angle-left"></i>Regresar</button>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
import Encabezado from './partials/Encabezado';
import EncabezadoReembolso from "./partials/EncabezadoReembolso";
import Resumen from './partials/TablaDatosResumen';
import TablaDatosReembolso from "./partials/TablaDatosReembolso";
import Documentos from './partials/TablaDatosDocumentos';
export default {
    name: "SolicitaReembolsoXSolicitud",
    components: { Encabezado,EncabezadoReembolso, Resumen, Documentos, TablaDatosReembolso },
    props: ['id'],
    data(){
        return{
            cargando: false,
            relacion : null,
            reembolso : null,
            registro: false
        }
    },
    mounted() {
        this.find();
    },
    methods: {
        find() {
            this.cargando = true;
            return this.$store.dispatch('controlRecursos/relacion-gasto/find', {
                id: this.id,
                params:{include: []}
            }).then(data => {
                this.relacion = data
                if(this.relacion.id_documento != null && this.registro == false)
                {
                    this.findReembolso();
                }
                else{
                    this.registro = true;
                }
            })
                .finally(()=> {
                    this.cargando = false;
                })
        },
        salir() {
            this.$router.push({name: 'relacion-gasto'});
        },
        validate() {
            this.$validator.validate().then(result => {
                if (result)
                {
                    /*if(moment(this.relacion.fecha_final_editar).format('YYYY/MM/DD') < moment(this.relacion.fecha_inicial_editar).format('YYYY/MM/DD'))
                    {
                        swal('¡Error!', 'La fecha de final no puede ser posterior a la fecha de inicial.', 'error')
                    }
                    else {*/
                        this.store();
                   // }
                }
            });
        },
        store() {
            return this.$store.dispatch('controlRecursos/relacion-gasto/reembolsoXSolicitud', this.relacion)
                .then((data) => {
                    this.relacion = data
                    this.findReembolso();
                });
        },
        findReembolso() {
            return this.$store.dispatch('controlRecursos/reembolso-gasto-sol/find', {
                id: this.relacion.id_documento,
                params:{include: []}
            }).then(data => {
                this.reembolso = data;
            })
        },
        editar() {
            return this.$store.dispatch('controlRecursos/reembolso-gasto-sol/update',  {
                id: this.reembolso.id,
                data: this.reembolso
            }).then((data) => {
                this.reembolso = data;
            })
        },
        eliminar() {
            return this.$store.dispatch('controlRecursos/relacion-gasto/reembolsoXSolicitud', this.relacion)
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
