<template>
    <div class="btn-group">
        <router-link  :to="{ name: 'solicitud-recepcion-cfdi-show-proyecto', params: {id: value.id}}"
                      v-if="$root.can('consultar_solicitud_recepcion_cfdi_proyecto')"
                      type="button" class="btn btn-sm btn-outline-secondary" title="Ver">
            <i class="fa fa-eye"></i>
        </router-link>

        <router-link  :to="{ name: 'solicitud-recepcion-cfdi-aprobar', params: {id: value.id}}" v-if="$root.can('aprobar_solicitud_recepcion_cfdi') && value.aprobar"
                      type="button" class="btn btn-sm btn-outline-primary" title="Aprobar">
            <i class="fa fa-check"></i>
        </router-link>
        <router-link  :to="{ name: 'solicitud-recepcion-cfdi-rechazar', params: {id: value.id}}" v-if="$root.can('rechazar_solicitud_recepcion_cfdi') && value.aprobar"
                      type="button" class="btn btn-sm btn-outline-danger" title="Rechazar">
            <i class="fa fa-times"></i>
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
