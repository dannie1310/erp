<template>
    <div class="btn-group">
        <Cambiar-area-subcontratante :id="value.id" :value="value" />
        <router-link  :to="{ name: 'proyectado-show', params: {id: value.id}}" v-if="value.show" type="button" class="btn btn-sm btn-outline-secondary" title="Ver">
            <i class="fa fa-eye"></i>
        </router-link>
        <router-link  :to="{ name: 'proyectado-edit', params: {id: value.id}}" v-if="value.edit" type="button" class="btn btn-sm btn-outline-info" title="Editar">
            <i class="fa fa-pencil"></i>
        </router-link>
        <PDF v-bind:id="value.id" @click="value.id" v-if="value.show" />
        <Delete v-bind:id="value.id" v-if="value.delete" />
        <Relaciones v-bind:transaccion="value.transaccion"/>
        <router-link  :to="{ name: 'proyectado-documentos', params: {id: value.id}}" v-if="value.show && $root.can('consultar_archivos_transaccion')" type="button" class="btn btn-sm btn-outline-primary" title="Ver">
            <i class="fa fa-folder-open"></i>
        </router-link>
    </div>
</template>

<script>
    import CambiarAreaSubcontratante from "../CambiarAreaSubcontratante";
    import Delete from "../Delete";
    import PDF from "../FormatoContratoProyectado";
    import Relaciones from "../../../globals/ModalRelaciones";
    export default {
        name: "buttons-contrato-proyectado",
        components: {CambiarAreaSubcontratante, Delete, PDF, Relaciones},
        props: ['value'],
        methods: {
            cambiar_area() {
                this.$router.push({name: 'cambiar-area-subcontratante', params: {id: this.value.id}});
            }
        }
    }
</script>
