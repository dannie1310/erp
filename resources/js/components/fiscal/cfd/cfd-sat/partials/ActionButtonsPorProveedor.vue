<template>
    <div class="btn-group">
        <ComunicadoProveedoresREP v-bind:id="value.id"></ComunicadoProveedoresREP>
        <router-link  :to="{ name: 'envio-comunicado-rep-faltantes', params: {id: value.id}}"  type="button" class="btn btn-sm btn-outline-primary" title="Enviar Comunicado">
            <i class="fa fa-envelope"></i>
        </router-link>
        <router-link  :to="{ name: 'historico-notificacion-rep', params: {id_proveedor: value.id}}"  type="button" class="btn btn-sm btn-outline-primary" title="Consultar histórico de notificaciones">
            <i class="fa fa-history"></i>
        </router-link>
        <rep-pendiente-proveedor-xls v-bind:id="value.id"></rep-pendiente-proveedor-xls>

    </div>
</template>

<script>

    import ComunicadoProveedoresREP from "../ComunicadoProveedoresREP";
    import RepPendienteProveedorXls from "./RepPendienteProveedorXls.vue";
    export default {
        name: "ActionButtonsPorProveedor",
        components: {RepPendienteProveedorXls, ComunicadoProveedoresREP},
        props: ['value']
    }
</script>

<style scoped>

</style>
