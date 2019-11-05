<template>
    <div class="btn-group">
        <button @click="edit" type="button" class="btn btn-sm btn-outline-secondary"> <i class="fa fa-pencil"></i> </button>
        <PDF  v-if="value.id" v-bind:id="value.id" @click="value.id"></PDF>
    </div>
</template>
<script>
    import PDF from '../FormatoSolicitudCompra.vue';
    export default {
        name: "solicitud-compra-buttons",
        components: {PDF},
        props: ['value'],
        methods: {
            edit() {
                this.$router.push({name:'solicitud-compra-edit', params: { id: this.value.id }});
            }
        }
    }
</script>

<style scoped>

</style>
