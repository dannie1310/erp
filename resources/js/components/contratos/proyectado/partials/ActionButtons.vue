<template>
    <div class="btn-group">
        <cambiar-area-subcontratante :id="value.id" :value="value"></cambiar-area-subcontratante>
        <Show v-if="$root.can('consultar_contrato_proyectado')" v-bind:id="value.id"></Show>
        <Editar v-bind:id="value.id" v-if="$root.can('editar_contrato_proyectado')"></Editar>
        <Delete v-bind:id="value.id" v-if="value.delete" />
    </div>
</template>

<script>
    import CambiarAreaSubcontratante from "../CambiarAreaSubcontratante";
    import Show from '../Show';
    import Editar from '../Edit';
    import Delete from "../Delete";
    export default {
        name: "action-buttons",
        components: {CambiarAreaSubcontratante, Show, Editar, Delete},
        props: ['value'],
        methods: {
            cambiar_area() {
                this.$router.push({name: 'cambiar-area-subcontratante', params: {id: this.value.id}});
            }
        }
    }
</script>
