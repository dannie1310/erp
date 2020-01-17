<template>
    <span>
        <div class="btn-group">
            <FacturaShow v-if="value.show" v-bind:id="value.id" />
            <PDF v-bind:id="value.id" @click="value.id" ></PDF>
        </div>
    </span>
</template>

<script>
    import PDF from '../FormatoCR';
    import FacturaShow from "../Show";
    export default {
        name: "action-buttons",
        components: {PDF, FacturaShow},
        props: ['value'],
    }
</script>

<style scoped>

</style>