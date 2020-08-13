<template>
    <div class="btn-group">
        <button v-if="value.edit" @click="edit" type="button" class="btn btn-sm btn-outline-secondary" title="Show">
            <i class="fa fa-folder-open"></i>
        </button>
        <button v-else @click="show" type="button" class="btn btn-sm btn-outline-secondary" title="Show">
            <i class="fa fa-folder-open"></i>
        </button>
        <PDF v-bind:id="value.id" @click="value.id"  v-if="$root.can('consultar_entrada_almacen')"></PDF>
    </div>
</template>

<script>
    import PDF from '../FormatoEntradaAlmacen';
    export default {
        name: "ActionButtons",
        components: {PDF},
        props: ['value'],
        methods: {
            edit() {
                this.$router.push({name: 'proveedores-edit', params: {id: this.value.id}});
            },
            show() {
                //this.$router.push({name: 'proveedores-show', params: {id: this.value.id}});
            }
        }
    }
</script>

<style scoped>

</style>
