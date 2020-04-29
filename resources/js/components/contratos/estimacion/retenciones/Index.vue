<template>
    <span>
        <button type="button" @click="init()" class="btn btn-primary float-right" :disabled="cargando" v-if="$root.can(['registrar_retencion_estimacion_subcontrato', 'registrar_liberacion_estimacion_subcontrato', 'eliminar_retencion_estimacion_subcontrato', 'eliminar_liberacion_estimacion_subcontrato'])" >
            Retenciones
        </button>
        <div class="row">
            <div class="col-md-12">
                <div class="modal fade" ref="modalRetenciones" data-backdrop="static" data-keyboard="false">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle"><i class="fa fa-spin fa-spinner" v-if="cargando"></i> Retenciones</h5>
                                <button type="button" class="close" @click="cerrar()" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body" v-if="retenciones && liberaciones">
                                <div class="col-md-12 mt-2 text-left" >
                                    <label class="text-secondary ">Aplicadas </label>
                                    <AplicadasCreate v-bind:id="id"></AplicadasCreate>
                                    <hr style="color: #0056b2; margin-top:auto;" width="90%" size="10" />
                                </div>
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th style="width:25%;">Tipo</th>
                                            <th style="width:20%;">Importe</th>
                                            <th style="width:45%;">Concepto</th>
                                            <th style="width:10%;"></th>
                                        </tr>
                                    </thead>
                                    <tbody v-if="retenciones">
                                        <tr v-for="(retencion,i) in retenciones">
                                            <td>{{retencion.tipo_retencion}}</td>
                                            <td class="text-right">{{retencion.importe_format}}</td>
                                            <td>{{retencion.concepto}}</td>
                                            <td class="icono">
                                                <button type="button" class="btn btn-sm btn-outline-danger" title="Eliminar" @click="destroyRetencion(retencion.id)" v-if="$root.can('eliminar_retencion_estimacion_subcontrato')">
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
                                            <th style="width:20%;">Importe</th>
                                            <th style="width:70%;">Concepto</th>
                                            <th style="width:10%;"></th>
                                        </tr>
                                    </thead>
                                    <tbody v-if="retenciones">
                                        <tr v-for="(liberacion,i) in liberaciones">
                                            <td class="text-right">{{liberacion.importe_format}}</td>
                                            <td>{{liberacion.concepto}}</td>
                                            <td class="icono">
                                                <button type="button" class="btn btn-sm btn-outline-danger" title="Eliminar" @click="destroyLiberacion(liberacion.id)" v-if="$root.can('eliminar_liberacion_estimacion_subcontrato')">
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
    name: "retencion-index",
    components: {AplicadasCreate, LiberadasCreate},
    props: ['id'],
    data() {
        return {
            cargando:false,
        }
    },
    mounted() {
    },
    methods: {
        cerrar(){
            $(this.$refs.modalRetenciones).modal('hide');
        },
        destroyRetencion(id){
            return this.$store.dispatch('subcontratosEstimaciones/retencion/delete', id)
                .then(() => {
                    this.$store.commit('subcontratosEstimaciones/retencion/DELETE_RETENCION', id)
                })
        },
        destroyLiberacion(id){
            return this.$store.dispatch('subcontratosEstimaciones/retencion-liberacion/delete', id)
                .then(() => {
                    this.$store.commit('subcontratosEstimaciones/retencion-liberacion/DELETE_LIBERACION', id)
                })
        },
        getLiberaciones(){
            this.cargando = true;
            this.$store.commit('subcontratosEstimaciones/retencion-liberacion/SET_LIBERACIONES', null);
            return this.$store.dispatch('subcontratosEstimaciones/retencion-liberacion/index',{
                params:{ scope : ['porEstimacion:'+this.id]}})
                .then(data => {
                    this.$store.commit('subcontratosEstimaciones/retencion-liberacion/SET_LIBERACIONES', data.data);
                })
                .finally(() => {
                    this.cargando = false;
                })
        },
        getRetenciones(){
            this.cargando = true;
            this.$store.commit('subcontratosEstimaciones/retencion/SET_RETENCIONES', null);
            return this.$store.dispatch('subcontratosEstimaciones/retencion/index',{
                params:{ scope : ['porEstimacion:'+this.id]}})
                .then(data => {
                    this.$store.commit('subcontratosEstimaciones/retencion/SET_RETENCIONES', data.data);
                })
        },
        init(){
            this.getRetenciones();
            this.getLiberaciones();
            $(this.$refs.modalRetenciones).modal('show');
        },
    },
    computed: {
        retenciones() {
            return this.$store.getters['subcontratosEstimaciones/retencion/retenciones']
        },
        liberaciones() {
            return this.$store.getters['subcontratosEstimaciones/retencion-liberacion/liberaciones']
        },
    }
}
</script>

<style>

</style>
