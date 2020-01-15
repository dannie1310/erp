<template>
    <span>
        <div class="btn-group">
            <PDF v-bind:id="value.id" @click="value.id" ></PDF>
        </div>
    </span>
</template>

<script>
    import PDF from '../FormatoCR';
    export default {
        name: "action-buttons",
        components: {PDF},
        props: ['value'],

    }
</script>

<style scoped>

</style>