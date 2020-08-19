<template>
    <div class="btn-group">
        <button v-if="value.show" @click="entrar_a_expediente" type="button" class="btn btn-sm btn-outline-secondary" title="Entrar a expediente de proveedor">
            <i class="fa fa-folder-open"></i>
        </button>
        <PDF v-bind:id="value.id" @click="value.id"  v-if="$root.can('consultar_expediente_proveedor_')"></PDF>
    </div>
</template>

<script>
    import PDF from '../FormatoEntradaAlmacen';
    export default {
        name: "ActionButtons",
        components: {PDF},
        props: ['value'],
        methods: {
            entrar_a_expediente() {
                this.$router.push({name: 'entrar-a-expediente', params: {id: this.value.id}});
            },
        }
    }
</script>

<style scoped>

</style>
