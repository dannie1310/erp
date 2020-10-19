<template>
    <div class="btn-group">
        <Cambiar-area-subcontratante :id="value.id" :value="value" />
        <Show v-if="value.show" v-bind:id="value.id"  v-if="$root.can('consultar_contrato_proyectado')"/>
        <Editar v-bind:id="value.id" v-if="value.edit" />
        <Delete v-bind:id="value.id" v-if="value.delete" />
        <PDF v-bind:id="value.id" @click="value.id" v-if="$root.can('consultar_contrato_proyectado')"></PDF>
        <Relaciones v-bind:transaccion="value.transaccion"/>
        <router-link  :to="{ name: 'proyectado-documentos', params: {id: value.id}}" v-if="$root.can('consultar_contrato_proyectado') && $root.can('consultar_archivos_transaccion')" type="button" class="btn btn-sm btn-outline-primary" title="Ver">
            <i class="fa fa-folder-open"></i>
        </router-link>
    </div>
</template>

<script>
    import CambiarAreaSubcontratante from "../CambiarAreaSubcontratante";
    import Show from '../Show';
    import Editar from '../Edit';
    import Delete from "../Delete";
    import PDF from "../FormatoContratoProyectado";
    import Relaciones from "../../../globals/ModalRelaciones";
    export default {
        name: "action-buttons",
        components: {CambiarAreaSubcontratante, Show, Editar, Delete, PDF, Relaciones},
        props: ['value'],
        methods: {
            cambiar_area() {
                this.$router.push({name: 'cambiar-area-subcontratante', params: {id: this.value.id}});
            }
        }
    }
</script>
