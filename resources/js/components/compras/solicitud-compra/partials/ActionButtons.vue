<template>
    <div class="btn-group">
        <Aprobar v-if="value.aprobar" v-bind:id="value.id"></Aprobar>
        <button v-if="value.edit" @click="edit" type="button" class="btn btn-sm btn-outline-info" title="Editar Solicitud"> <i class="fa fa-pencil"></i></button>
        <router-link  :to="{ name: 'solicitud-show', params: {id: value.id}}" v-if="$root.can('consultar_solicitud_compra')" type="button" class="btn btn-sm btn-outline-secondary" title="Ver">
            <i class="fa fa-eye"></i>
        </router-link>
        <PDF v-bind:id="value.id"/>
        <Delete v-if="value.delete" v-bind:id="value.id"/>
        <Relaciones v-bind:transaccion="value.transaccion"/>
        <router-link  :to="{ name: 'solicitud-compra-documentos', params: {id: value.id}}" v-if="$root.can('consultar_solicitud_compra')" type="button" class="btn btn-sm btn-outline-primary" title="Ver">
            <i class="fa fa-folder-open"></i>
        </router-link>
    </div>
</template>
<script>
    import Consulta from '../ShowModal';
    import PDF from '../FormatoSolicitudCompra.vue';
    import Aprobar from '../Autorizar';
    import Delete from "../Delete";
    import Relaciones from "../../../globals/ModalRelaciones";
    export default {
        name: "solicitud-compra-buttons",
        components: {PDF, Consulta, Aprobar, Delete,Relaciones},
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
