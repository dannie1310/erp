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
                        <tabla-datos-reembolso-caja v-bind:reembolso="reembolso" />
                        <hr />
                        <documentos v-bind:documentos="reembolso.documentos" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="pull-right">
                    <button type="submit" class="btn btn-danger" :disabled="errors.count() > 0" @click="eliminar"><i class="fa fa-trash"></i> Eliminar</button>
                    <button type="button" class="btn btn-secondary" v-on:click="salir"><i class="fa fa-angle-left"></i>Regresar</button>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
import EncabezadoReembolso from "./partials/EncabezadoReembolso";
import TablaDatosReembolsoCaja from "./partials/TablaDatosReembolsoCaja";
import Documentos from './partials/TablaDatosDocumentos';
export default {
    name: "ReembolsoXCaja",
    components: { EncabezadoReembolso, Documentos, TablaDatosReembolsoCaja },
    props: ['id'],
    data(){
        return{
            cargando: false,
            reembolso : null,
            cajas: [],
        }
    },
    mounted() {
        this.find();
        this.getCajaChica();
    },
    methods: {
        find() {
            this.cargando = true;
            return this.$store.dispatch('controlRecursos/reembolso-caja-chica/find', {
                id: this.id,
                params:{include: []}
            }).then(data => {
                this.reembolso = data;
            }).finally(()=> {
                this.cargando = false;
            })
        },
        getCajaChica() {
            return this.$store.dispatch('controlRecursos/caja-chica/index', {
                params: { scope: 'cajaChica' }
            }).then(data => {
                this.cajas = data.data;
            })
        },
        salir() {
            this.$router.push({name: 'relacion-gasto'});
        },
        eliminar() {
            return this.$store.dispatch('controlRecursos/reembolso-caja-chica/delete', {
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
