<template>
    <div class="btn-group">
        <Show v-bind:id="value.id"></Show>
    </div>
</template>
<script>
import Show from '../Show';
    export default {
        name: "presupuesto-buttons",
        components: {Show},
        props: ['value']
    }
</script>

<style scoped>

</style>