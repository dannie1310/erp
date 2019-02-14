<template>
    <div class="btn-group">
        <CuentaEmpresaShow v-if="value.show" v-bind:id="value.id" />
        <CuentaEmpresaEdit v-if="value.edit" v-bind:id="value.id" />
        <button @click="destroy" v-if="value.delete" type="button" class="btn btn-sm btn-outline-danger"><i class="fa fa-trash"></i></button>
    </div>
</template>

<script>
    import CuentaEmpresaEdit from "../Edit";
    import CuentaEmpresaShow from "../Show";
    export default {
        name: "action-buttons",
        components: {CuentaEmpresaShow, CuentaEmpresaEdit},
        props: ['value'],
        methods: {
            destroy() {

            },
        },
        mounted() {
        }
    }
</script>