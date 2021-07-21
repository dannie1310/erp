<template>
    <div class="btn-group">
        <router-link :to="{ name: 'cotizacion-proveedor-show', params: {id: this.value.id}}" v-if="value.show" type="button" class="btn btn-sm btn-outline-secondary" title="Consultar">
            <i class="fa fa-eye"></i>
        </router-link>
    </div>
</template>
<script>
    export default {
        name: "cotizacion-buttons",
        components: {},
        props: ['value'],
        data()
        {
            return {
            }
        },

        methods: {

        }
    }
</script>

<style scoped>

</style>
