<template>
    <div class="btn-group">
        <Aprobar v-if="value.aprobar" v-bind:id="value.id"></Aprobar>
        <button v-if="value.edit" @click="edit" type="button" class="btn btn-sm btn-outline-info" title="Editar Solicitud"> <i class="fa fa-pencil"></i></button>
        <SolicitudShow v-if="value.show" @click="value.id" v-bind:id="value.id" v-bind:cotizacion="value.cotizacion"/>
       <!--  <PDF  v-if="value.id" v-bind:id="value.id" @click="value.id"/> -->
        <Delete v-if="value.delete" v-bind:id="value.id"/>
    </div>
</template>
<script>
    import SolicitudShow from '../Show.vue';
    import PDF from '../FormatoSolicitudCompra.vue';
    import Aprobar from '../Autorizar';
    import Delete from "../Delete";
    export default {
        name: "solicitud-compra-buttons",
        components: {PDF, SolicitudShow, Aprobar, Delete},
        props: ['value'],
        methods: {
            edit() {
                this.$router.push({name:'solicitud-compra-edit', params: { id: this.value.id }});
           },
        }
    }
</script>

<style scoped>

</style>
