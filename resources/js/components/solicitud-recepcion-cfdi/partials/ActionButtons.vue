<template>
    <div class="btn-group">
        <router-link  :to="{ name: 'solicitud-recepcion-cfdi-show', params: {id: value.id}}"
                      v-if="$root.can('consultar_solicitud_recepcion_cfdi_proveedor',1)"
                      type="button" class="btn btn-sm btn-outline-secondary" title="Ver">
            <i class="fa fa-eye"></i>
        </router-link>
        <router-link  :to="{ name: 'solicitud-recepcion-cfdi-aprobar', params: {id: value.id}}" v-if="$root.can('aprobar_solicitud_recepcion_cfdi') && value.aprobar"
                      type="button" class="btn btn-sm btn-outline-primary" title="Aprobar">
            <i class="fa fa-check"></i>
        </router-link>
    </div>
</template>

<script>
    export default {
        name: "ActionButtons",
        components: {},
        props: ['value']
    }
</script>

<style scoped>

</style>
