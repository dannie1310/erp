<template>
    <span>
        <button type="button" @click="init()" class="btn btn-primary float-right" :disabled="cargando" v-if="$root.can('registrar_penalizacion_estimacion_subcontrato') || $root.can('registrar_liberacion_penalizacion_estimacion_subcontrato') || $root.can('eliminar_penalizacion_estimacion_subcontrato') || $root.can('eliminar_liberacion_penalizacion_estimacion_subcontrato')" >
            Penalizaciones
        </button>
        <div class="row">
            <div class="col-md-12">
                <div class="modal fade" ref="modalPenalizaciones" data-backdrop="static" data-keyboard="false">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle"><i class="fa fa-spin fa-spinner" v-if="cargando"></i> Penalizaciones</h5>
                                <button type="button" class="close" @click="cerrar()" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body" v-if="penalizaciones && liberaciones">
                                <div class="col-md-12 mt-2 text-left" >
                                    <label class="text-secondary ">Aplicadas </label>
                                    <AplicadasCreate v-bind:id="id"></AplicadasCreate>
                                    <hr style="color: #0056b2; margin-top:auto;" width="90%" size="10" />
                                </div>
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th class="index_corto">#</th>
                                            <th style="width:5%;"></th>
                                            <th style="width:20%;">Importe</th>
                                            <th>Concepto</th>
                                            <th style="width:10%;"></th>
                                        </tr>
                                    </thead>
                                    <tbody v-if="penalizaciones">
                                        <tr v-for="(penalizacion,i) in penalizaciones">
                                            <td class="icono">{{i+1}}</td>
                                            <td></td>
                                            <td class="text-right">{{penalizacion.importe_format}}</td>
                                            <td class="text-center">{{penalizacion.concepto}}</td>
                                            <td class="icono">
                                                <button type="button" class="btn btn-sm btn-outline-danger" :title="(penalizacion.liberada == true ) ? 'Penalización liberada':'Eliminar'" @click="destroyPenalizacion(penalizacion.id)" v-if="$root.can('eliminar_penalizacion_estimacion_subcontrato')">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="col-md-12" >
                                    <br>
                                    <hr style="margin-top:auto;border: 1px solid grey;" width="100%" size="20" />
                                    <br>
                                </div>
                                <div class="col-md-12 mt-2 text-left" >
                                    <label class="text-secondary ">Liberadas </label>
                                    <LiberadasCreate v-bind:id="id"></LiberadasCreate>
                                    <hr style="color: #0056b2; margin-top:auto;" width="90%" size="10" />
                                </div>
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th class="index_corto">#</th>
                                            <th style="width:5%;"></th>
                                            <th style="width:20%;">Importe</th>
                                            <th>Concepto</th>
                                            <th style="width:10%;"></th>
                                        </tr>
                                    </thead>
                                    <tbody v-if="penalizaciones">
                                        <tr v-for="(liberacion,i) in liberaciones">
                                            <td class="icono">{{i+1}}</td>
                                            <td></td>
                                            <td class="text-right">{{liberacion.importe_format}}</td>
                                            <td style="text-align:center;">{{liberacion.concepto}}</td>
                                            <td class="icono">
                                                <button type="button" class="btn btn-sm btn-outline-danger" title="Eliminar" @click="destroyLiberacion(liberacion.id)" v-if="$root.can('eliminar_liberacion_penalizacion_estimacion_subcontrato')">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" @click="cerrar()">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
import AplicadasCreate from './Aplicadas';
import LiberadasCreate from './Liberadas';
export default {
    name: "penalizacion-index",
    components: {AplicadasCreate, LiberadasCreate},
    props: ['id'],
    data() {
        return {
            cargando:false,
        }
    },
    methods: {
        cerrar(){
            $(this.$refs.modalPenalizaciones).modal('hide');
        },
        destroyPenalizacion(id){

            return this.$store.dispatch('subcontratosEstimaciones/penalizacion/delete', id)
                .then(() => {
                    this.$store.commit('subcontratosEstimaciones/penalizacion/DELETE_PENALIZACION', id)
                })
        },
        destroyLiberacion(id){

            return this.$store.dispatch('subcontratosEstimaciones/penalizacion-liberacion/delete', id)
                .then(() => {
                    this.$store.commit('subcontratosEstimaciones/penalizacion-liberacion/DELETE_LIBERACION', id)
                })
        },
        getLiberaciones(){
            this.cargando = true;
            this.$store.commit('subcontratosEstimaciones/penalizacion-liberacion/SET_LIBERACIONES', null);
            return this.$store.dispatch('subcontratosEstimaciones/penalizacion-liberacion/index',{
                params:{ scope : ['porEstimacion:'+this.id]}})
                .then(data => {
                    this.$store.commit('subcontratosEstimaciones/penalizacion-liberacion/SET_LIBERACIONES', data.data);
                })
                .finally(() => {
                    this.cargando = false;
                })
        },
        getPenalizaciones(){
            this.cargando = true;
            this.$store.commit('subcontratosEstimaciones/penalizacion/SET_PENALIZACIONES', null);
            return this.$store.dispatch('subcontratosEstimaciones/penalizacion/index',{
                params:{ scope : ['porEstimacion:'+this.id]}})
                .then(data => {
                    this.$store.commit('subcontratosEstimaciones/penalizacion/SET_PENALIZACIONES', data.data);
                })
        },
        init(){
            this.getPenalizaciones();
            this.getLiberaciones();
            $(this.$refs.modalPenalizaciones).modal('show');
        },
    },
    computed: {
        penalizaciones() {
            return this.$store.getters['subcontratosEstimaciones/penalizacion/penalizaciones']
        },
        liberaciones() {
            return this.$store.getters['subcontratosEstimaciones/penalizacion-liberacion/liberaciones']
        },
    }
}
</script>

<style>

</style>
