<template>
    <span>
        <button type="button" @click="init()" class="btn btn-primary float-right" v-if="$root.can('registrar_descuento_estimacion_subcontrato') || true" >
             Retenciones
        </button>
        <div class="row">
            <div class="col-md-12">
                <div class="modal fade" ref="modalRetenciones" data-backdrop="static" data-keyboard="false">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-plus"></i> Retenciones</h5>
                                <button type="button" class="close" @click="cerrar()" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
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
export default {
    name: "retencion-create",
    components: {},
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
        getLiberaciones(){
            this.cargando = true;
             return this.$store.dispatch('subcontratosEstimaciones/retencion-liberacion/listLiberaciones',{
                id: this.id,
                params:{}})
                .then(data => {
                    this.$store.commit('subcontratosEstimaciones/retencion-liberacion/SET_LIBERACIONES', data.data);
                })
                .finally(() => {
                    this.cargando = false;
                })
        },
        getRetenciones(){
            this.cargando = true;
             return this.$store.dispatch('subcontratosEstimaciones/retencion/listRetenciones',{
                id: this.id,
                params:{}})
                .then(data => {
                    this.$store.commit('subcontratosEstimaciones/retencion/SET_RETENCIONES', data.data);
                })
                .finally(() => {
                    this.cargando = false;
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