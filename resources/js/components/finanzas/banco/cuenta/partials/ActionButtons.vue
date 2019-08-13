<template>
    <div class="btn-group" v-if="value.id">
        <CuentaShow v-if="value.show" @click="value.id" v-bind:id="value.id" />
        <button type="button" class="btn btn-sm btn-outline-primary" title="Editar"><i class="fa fa-pencil"></i></button>
    </div>
</template>

<script>
    import CuentaShow from "../Show";
    export default {
        name: "action-buttons",
        components: { CuentaShow} ,
        props: ['value'],
    }
</script>

<style scoped>

</style>
