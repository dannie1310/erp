<template>
    <div class="btn-group">
        <button @click="edit" type="button" class="btn btn-sm btn-outline-info" title="Editar Solicitud"> <i class="fa fa-pencil"></i> </button>
        <SolicitudShow v-if="value.show" @click="value.id" v-bind:id="value.id"/>
        <button type="button" class="btn btn-sm btn-outline-danger" title="Eliminar Solicitud"><i class="fa fa-trash"></i></button>
        <PDF  v-if="value.id" v-bind:id="value.id" @click="value.id"/>
    </div>
</template>
<script>
    import SolicitudShow from '../Show.vue';
    import PDF from '../FormatoSolicitudCompra.vue';
    export default {
        name: "solicitud-compra-buttons",
        components: {PDF, SolicitudShow},
        props: ['value'],
        methods: {
            edit() {
                this.$router.push({name:'solicitud-compra-edit', params: { id: this.value.id }});
           },
            delete(){
            },
        }
    }
</script>

<style scoped>

</style>
