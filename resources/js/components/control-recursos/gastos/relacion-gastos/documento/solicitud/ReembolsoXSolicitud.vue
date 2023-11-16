<template>
    <span>
        <div class="card" v-if="reembolso == null">
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
                    <div class="col-md-12">
                        <h4>Documento para Reembolso</h4>
                    </div>
                </div>
                <div class="row">
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
                    <button type="submit" class="btn btn-primary" :disabled="errors.count() > 0" @click="validaDocumentos"><i class="fa fa-save"></i> Registrar Solicitud</button>
                    <button type="submit" class="btn btn-info" :disabled="errors.count() > 0" @click="editar"><i class="fa fa-save" ></i> Actualizar</button>
                    <button type="submit" class="btn btn-danger" :disabled="errors.count() > 0" @click="eliminar"><i class="fa fa-trash"></i> Eliminar</button>
                    <button type="button" class="btn btn-secondary" v-on:click="salir"><i class="fa fa-angle-left"></i>Regresar</button>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
import EncabezadoReembolso from "../reembolso/partials/EncabezadoReembolso";
import TablaDatosReembolso from "../reembolso/partials/TablaDatosReembolso";
import Documentos from '../reembolso/partials/TablaDatosDocumentos';
export default {
    name: "ReembolsoXSolicitud",
    components: { EncabezadoReembolso, Documentos, TablaDatosReembolso },
    props: ['id'],
    data(){
        return{
            cargando: false,
            reembolso : null
        }
    },
    mounted() {
        this.find();
    },
    methods: {
        find() {
            this.cargando = true;
            return this.$store.dispatch('controlRecursos/reembolso-gasto-sol/find', {
                id: this.id,
                params:{include: []}
            }).then(data => {
                this.reembolso = data;
            }).finally(()=> {
                this.cargando = false;
            })
        },
        salir() {
            this.$router.push({name: 'relacion-gasto'});
        },
        validaDocumentos() {
            return this.$store.dispatch('controlRecursos/reembolso-gasto-sol/validaDocumentos', {
                id: this.id,
                params:{include: []}
            }).then(data => {
                this.relacion = data
            })
        },
        editar() {
            if(moment(this.reembolso.fecha_final_editar).format('YYYY/MM/DD') < moment(this.reembolso.fecha_inicio_editar).format('YYYY/MM/DD'))
            {
                swal('¡Error!', 'La fecha de final no puede ser posterior a la fecha de inicial.', 'error')
            }
            else {
                return this.$store.dispatch('controlRecursos/reembolso-gasto-sol/update', {
                    id: this.reembolso.id,
                    data: this.reembolso
                }).then((data) => {
                    this.reembolso = data;
                })
            }
        },
        eliminar() {
            return this.$store.dispatch('controlRecursos/reembolso-gasto-sol/delete', {
                    id: this.id,
                    params: {}
                }).then(() => {
                    this.salir();
                })
        },
    },
}
</script>

<style scoped>

</style>
