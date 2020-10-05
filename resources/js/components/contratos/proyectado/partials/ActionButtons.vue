<template>
    <div class="btn-group">
        <Cambiar-area-subcontratante :id="value.id" :value="value" v-if="value.cambiaAreaSubcontratante" />
        <Show v-if="value.show" v-bind:id="value.id"  v-bind:contrato_proyectado="value.contrato_proyectado" />
        <Editar v-bind:id="value.id" v-if="value.edit" />
        <Delete v-bind:id="value.id" v-if="value.delete" />
        <PDF v-bind:id="value.id" @click="value.id" ></PDF>
    </div>
</template>

<script>
    import CambiarAreaSubcontratante from "../CambiarAreaSubcontratante";
    import Show from '../Show';
    import Editar from '../Edit';
    import Delete from "../Delete";
    import PDF from "../FormatoContratoProyectado"
    export default {
        name: "action-buttons",
        components: {CambiarAreaSubcontratante, Show, Editar, Delete, PDF},
        props: ['value'],
        methods: {
            cambiar_area() {
                this.$router.push({name: 'cambiar-area-subcontratante', params: {id: this.value.id}});
            }
        }
    }
</script>
