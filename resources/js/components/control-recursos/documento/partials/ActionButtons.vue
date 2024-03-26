<template>
    <div class="btn-group">
        <router-link v-if="value.con_cfdi"  :to="{ name: 'factura-recurso-show', params: {id: value.id}}" type="button" class="btn btn-sm btn-outline-secondary" title="Consultar">
            <i class="fa fa-eye"></i>
        </router-link>
        <router-link v-else  :to="{ name: 'documento-recurso-show', params: {id: value.id}}" type="button" class="btn btn-sm btn-outline-secondary" title="Consultar">
            <i class="fa fa-eye"></i>
        </router-link>

        <router-link v-if="value.con_cfdi && value.edit" :to="{ name: 'factura-recurso-edit', params: {id: value.id}}" type="button" class="btn btn-sm btn-outline-info" title="Editar">
            <i class="fa fa-pencil"></i>
        </router-link>
        <router-link v-if="!value.con_cfdi && value.edit" :to="{ name: 'documento-recurso-edit', params: {id: value.id}}" type="button" class="btn btn-sm btn-outline-info" title="Editar">
            <i class="fa fa-pencil"></i>
        </router-link>

        <router-link v-if="value.con_cfdi && value.delete" :to="{ name: 'factura-recurso-delete', params: {id: value.id}}" type="button" class="btn btn-sm btn-outline-danger" title="Eliminar">
            <i class="fa fa-trash"></i>
        </router-link>
        <router-link v-if="!value.con_cfdi && value.delete" :to="{ name: 'documento-recurso-delete', params: {id: value.id}}" type="button" class="btn btn-sm btn-outline-danger" title="Eliminar">
            <i class="fa fa-trash"></i>
        </router-link>
    </div>
</template>

<script>
    export default {
        name: "documento-action-buttons",
        components: {},
        props: ['value'],
        methods: {

        }
    }
</script>
