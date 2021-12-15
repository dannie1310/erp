<template>
    <div class="btn-group">
        <EditEmpresaBoletinada v-bind:id="value.id" v-if="value.edit"></EditEmpresaBoletinada>
        <DeleteEmpresaBoletinada v-bind:id="value.id" v-if="value.delete"></DeleteEmpresaBoletinada>
    </div>
</template>

<script>
    import DeleteEmpresaBoletinada from "../Delete";
    import EditEmpresaBoletinada from "../Edit";
    export default {
        name: "ActionButtons",
        components: {EditEmpresaBoletinada, DeleteEmpresaBoletinada},
        props: ['value'],
        methods: {

        }
    }
</script>

<style scoped>

</style>
