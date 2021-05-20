<template>
    <div class="btn-group">
        <router-link  :to="{ name: 'solicitud-recepcion-cfdi-show', params: {id: value.id}}"
                      v-if="$root.can('consultar_solicitud_recepcion_cfdi_proveedor',1)"
                      type="button" class="btn btn-sm btn-outline-secondary" title="Ver">
            <i class="fa fa-eye"></i>
        </router-link>
        <router-link  :to="{ name: 'solicitud-recepcion-cfdi-cancelar', params: {id: value.id}}"
                      v-if="$root.can('cancelar_solicitud_recepcion_cfdi',1) && value.cancelar"
                      type="button" class="btn btn-sm btn-outline-danger" title="Cancelar">
            <i class="fa fa-ban"></i>
        </router-link>
        <FormatoSolicitudRecepcion v-bind:id="value.id"></FormatoSolicitudRecepcion>
    </div>
</template>

<script>
    import FormatoSolicitudRecepcion from "../../FormatoSolicitudRecepcion";
    export default {
        name: "ActionButtons",
        components: {FormatoSolicitudRecepcion},
        props: ['value']
    }
</script>

<style scoped>

</style>
