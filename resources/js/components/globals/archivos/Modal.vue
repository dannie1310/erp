<template>
    <span>
        <button @click="abrir()" type="button" class="btn btn-outline-primary" v-if="relacionados && $root.can('consultar_archivos_transaccion')" title="Ver Archivos">
            <i class="fa fa-folder-open"></i>
            Ver Archivos
        </button>
        <button @click="abrir()" type="button" class="btn btn-sm btn-outline-primary" title="Ver Archivos" v-else-if="$root.can('consultar_archivos_transaccion')">
            <i class="fa fa-folder-open"></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-folder-open"></i> Archivos</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" style="height: 800px" ref="body">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fa fa-times-circle"></i>
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
export default {
    name: "ModalArchivos",
    props: ['id', 'url', 'relacionados','transaccion'],
    methods: {
        abrir(){
            var url = this.url.replace("{id}", this.id);
            $(this.$refs.body).html('<iframe src="'+url+'"  frameborder="0" height="100%" width="100%">Archivos</iframe>');
            $(this.$refs.modal).appendTo('body')
            $(this.$refs.modal).modal('show');
        }
    }
}
</script>

<style scoped>

</style>
