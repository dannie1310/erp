<template>
    <div class="btn-group">
        <router-link  :to="{ name: 'poliza-show', params: {id: value.id}}" v-if="$root.can('consultar_prepolizas_generadas')" type="button" class="btn btn-sm btn-outline-secondary" title="Ver">
            <i class="fa fa-eye"></i>
        </router-link>
        <router-link  :to="{ name: 'poliza-edit', params: {id: value.id}}" v-if="$root.can('editar_prepolizas_generadas')&&value.edit && value.estatus" type="button" class="btn btn-sm btn-outline-warning" title="Ver">
            <i class="fa fa-pencil"></i>
        </router-link>
        <router-link  :to="{ name: 'poliza-historico', params: {id: value.id}}" v-if="$root.can('consultar_prepolizas_generadas')" type="button" class="btn btn-sm btn-outline-secondary" title="Ver">
            <i class="fa fa-clock-o"></i>
        </router-link>
    </div>
</template>

<script>
    export default {
        name: "action-buttons",
        props: ['value'],
    }
</script>
