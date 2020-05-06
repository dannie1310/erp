<template>
    <div class="btn-group">
        <Show v-bind:id="value.id" v-bind:show="value.show"></Show>
        <DescargarLayout v-bind:id="value.id"></DescargarLayout>
    </div>
</template>
<script>
import Show from '../Show';
import DescargarLayout from '../DescargaLayout';
    export default {
        name: "solicitud-compra-buttons",
        components: {Show, DescargarLayout},
        props: ['value'],
    }
</script>

<style scoped>

</style>
