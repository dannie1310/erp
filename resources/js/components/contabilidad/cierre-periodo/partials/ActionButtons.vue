<template>
    <div class="btn-group">
        <abrir v-if="value.abrir" v-bind:id="value.id" :key="value.id"/>
        <cerrar v-if="value.cerrar" v-bind:id="value.id" :key="value.id"/>
    </div>
</template>

<script>
    import Abrir from "../Abrir";
    import Cerrar from "../Cerrar";
    export default {
        name: "action-buttons",
        components: {Cerrar, Abrir},
        props: ['value']
    }
</script>