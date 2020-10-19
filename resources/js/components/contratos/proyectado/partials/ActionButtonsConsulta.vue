<template>
    <div class="btn-group">
        <Show v-bind:value="value" />
        <PDF v-bind:id="value.id" ></PDF>
        <ModalArchivos v-bind:id="value.id" v-bind:url="'/sao/modal/lista_archivos/{id}'"></ModalArchivos>
    </div>
</template>

<script>
    import Show from '../ShowModal';
    import PDF from "../FormatoContratoProyectado";
    import ModalArchivos from "../../../globals/archivos/Modal";
    export default {
        name: "action-buttons",
        components: {Show, PDF, ModalArchivos },
        props: ['value'],
        methods: {
            cambiar_area() {
                this.$router.push({name: 'cambiar-area-subcontratante', params: {id: this.value.id}});
            }
        }
    }
</script>
