<template>
    <div class="btn-group">
        <router-link  :to="{ name: 'factura-seg-show', params: {id: value.id}}" type="button" class="btn btn-sm btn-outline-secondary" title="Consultar">
            <i class="fa fa-eye"></i>
        </router-link>
        <router-link  :to="{ name: 'factura-seg-delete', params: {id: value.id}}" v-if="value.delete" type="button" class="btn btn-sm btn-outline-danger" title="Eliminar">
            <i class="fa fa-trash"></i>
        </router-link>
    </div>
</template>

<script>
    export default {
        name: "factura-action-buttons",
        components: {},
        props: ['value'],
    }
</script>
