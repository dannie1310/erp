<template>
    <div class="btn-group">
        <Show v-bind:id="value.id"></Show>
        <PDF v-bind:id="value.id" />
    </div>
</template>

<script>
   import Show from '../Show';
   import PDF from '../FormatoTablaComparativa'
    export default {
        name: "action-buttons",
        components: {Show, PDF},
        props: ['value'],
        methods: {
        }
    }
</script>
