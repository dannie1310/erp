<template>
    <div class="btn-group">
        <CuentaBancoShow v-if="value.show" v-bind:id="value.id" />
        <CuentaBancoEdit v-if="value.edit" v-bind:id="value.id" />
        <button @click="destroy" v-if="value.delete" type="button" class="btn btn-sm btn-outline-danger"><i class="fa fa-trash"></i></button>
    </div>
</template>

<script>
    import CuentaBancoEdit from "../Edit";
    import CuentaBancoShow from "../Show";
    export default {
        name: "action-buttons",
        components: {CuentaBancoShow, CuentaBancoEdit},
        props: ['value'],
        methods: {
            destroy() {

            },
        },
        mounted() {
        }
    }
</script>