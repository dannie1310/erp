<template>
    <div class="btn-group">
        <router-link :to="{ name: 'cotizacion-proveedor-show', params: {id: this.value.id}}" v-if="value.show" type="button" class="btn btn-sm btn-outline-secondary" title="Consultar">
            <i class="fa fa-eye"></i>
        </router-link>
        <DescargaLayoutProveedor v-if="value.descarga_layout" v-bind:id="value.id" v-bind:id_cotizacion="value.id_cotizacion" />
        <router-link :to="{ name: 'cotizacion-proveedor-edit', params: {id: this.value.id}}" v-if="value.edit" type="button" class="btn btn-sm btn-outline-info" title="Editar">
            <i class="fa fa-pencil"></i>
        </router-link>
    </div>
</template>
<script>
    import DescargaLayoutProveedor from "../DescargaLayoutProveedor";
    export default {
        name: "cotizacion-buttons",
        components: { DescargaLayoutProveedor },
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
