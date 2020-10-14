<template>
    <div class="btn-group">
        <router-link  :to="{ name: 'poliza-show', params: {id: value.id}}" v-if="$root.can('consultar_prepolizas_generadas')" target="_blank" type="button" class="btn btn-sm btn-primary" title="Ver">
            <i style="width:40px;" >{{value.numero_folio}}</i>
        </router-link>
    </div>
</template>

<script>
    export default {
        name: "action-buttons",
        props: ['value'],
    }
</script>
