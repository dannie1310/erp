<template>
    <div class="btn-group">
        <PDF v-bind:id="value.id" ></PDF>
    </div>
</template>

<script>

    import PDF from './FormatoOrdenCompra';
    export default {
        name: "action-buttons",
        components: {PDF},
        props: ['value'],

    }
</script>
