<template>
    <span>
        <div class="btn-group">
            <router-link  :to="{ name: 'estimacion-proveedor-show', params: {id: value.id, base: value.base}}" v-if="value.show" type="button" class="btn btn-sm btn-outline-secondary" title="Ver">
                <i class="fa fa-eye"></i>
            </router-link>
             <router-link  :to="{ name: 'estimacion-proveedor-edit', params: {id: value.id, base: value.base}}" v-if="value.edit" type="button" class="btn btn-sm btn-outline-info" title="Editar">
                <i class="fa fa-pencil"></i>
            </router-link>
        </div>
    </span>
</template>

<script>
    export default {
        name: "action-buttons",
        props: ['value'],
        data() {
            return {

            }
        },
        methods: {

        }
    }
</script>
