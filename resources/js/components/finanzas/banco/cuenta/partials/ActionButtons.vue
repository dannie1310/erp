<template>
    <div class="btn-group" v-if="value.id">
        <CuentaShow v-if="value.show" @click="value.id" v-bind:id="value.id" />
        <CuentaEdit v-if="value.edit" @click="value.id" v-bind:id="value.id" />
    </div>
</template>

<script>
    import CuentaEdit from "../Edit";
    import CuentaShow from "../Show";
    export default {
        name: "action-buttons",
        components: { CuentaEdit, CuentaShow} ,
        props: ['value'],
    }
</script>

<style scoped>

</style>
