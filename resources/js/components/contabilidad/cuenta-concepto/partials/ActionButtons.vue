<template>
    <div class="btn-group">
        <CuentaConceptoEdit v-if="value.edit" v-bind:id="value.id" :key="value.id"/>
    </div>
</template>

<script>
    import CuentaConceptoEdit from "../Edit";
    export default {
        name: "cuenta-concepto-action-buttons",
        components: {CuentaConceptoEdit},
        props: ['value']
    }
</script>