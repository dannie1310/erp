<template>
    <div class="btn-group">
        <cambiar-area-subcontratante :id="value.id" :value="value"></cambiar-area-subcontratante>
        <Editar v-bind:id="value.id"></Editar>

    </div>
</template>

<script>
    import CambiarAreaSubcontratante from "../CambiarAreaSubcontratante";
    import Editar from '../Edit';
    export default {
        name: "action-buttons",
        components: {CambiarAreaSubcontratante, Editar},
        props: ['value'],
        methods: {
            cambiar_area() {
                this.$router.push({name: 'cambiar-area-subcontratante', params: {id: this.value.id}});
            }
        }
    }
</script>