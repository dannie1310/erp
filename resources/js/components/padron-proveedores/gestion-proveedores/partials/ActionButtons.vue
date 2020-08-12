<template>
    <div class="btn-group">
        <button @click="edit" type="button" class="btn btn-sm btn-outline-info" title="Editar" v-if="value.edit">
            <i class="fa fa-pencil"></i>
        </button>
        <Show v-bind:id="value.id" v-if="value.show" v-bind:pagina="value.pagina"/>
        <PDF v-bind:id="value.id" @click="value.id"  v-if="$root.can('consultar_entrada_almacen')"></PDF>
    </div>
</template>

<script>
    import PDF from '../FormatoEntradaAlmacen';
    import Show from "../Show";
    export default {
        name: "ActionButtons",
        components: {PDF, Show},
        props: ['value'],
        methods: {
            edit() {
                this.$router.push({name: 'proveedores-edit', params: {id: this.value.id}});
            }
        }
    }
</script>

<style scoped>

</style>
