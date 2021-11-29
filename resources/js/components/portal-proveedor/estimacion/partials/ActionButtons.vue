<template>
    <span>
        <div class="btn-group">
            <router-link  :to="{ name: 'estimacion-proveedor-show', params: {id: value.id, base: value.base}}" v-if="value.show" type="button" class="btn btn-sm btn-outline-secondary" title="Ver">
                <i class="fa fa-eye"></i>
            </router-link>
            <!--<button @click="edit" type="button" class="btn btn-sm btn-outline-info" title="Editar" v-if="value.edit && (value.estado == 0)">
                <i class="fa fa-pencil"></i>
            </button>-->
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
