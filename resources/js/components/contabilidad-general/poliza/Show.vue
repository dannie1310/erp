<template>
    <span>
        <div class="modal fade" ref="modalEditPoliza" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-eye"></i> CONSULTA DE PÓLIZA</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        jj
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="closeModal()">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>

export default {
    name: "poliza-show",
    data(){
        return {
        }
    },
    methods: {
        closeModal(){
            this.$store.commit('contabilidadGeneral/poliza/SET_POLIZA', null);
            $(this.$refs.modalEditPoliza).modal('hide');
        },
        init(){
            $(this.$refs.modalEditPoliza).modal('show');
        }
    },

    computed: {
        poliza() {
            return this.$store.getters['contabilidadGeneral/poliza/currentPoliza'];
        }
    },
}
</script>