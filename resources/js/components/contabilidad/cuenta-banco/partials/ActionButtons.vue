<template>
    <div class="btn-group">
        <CuentaBancoShow  v-bind:id="value.id" v-if="value.show" />
        <CuentaBancoEdit  v-bind:id="value.id" v-if="value.edit"  />
<!--
        <button @click="destroy" v-if="value.delete" type="button" class="btn btn-sm btn-outline-danger"><i class="fa fa-trash"></i></button>
-->
    </div>
</template>

<script>
    import CuentaBancoEdit from "../Edit";
    import CuentaBancoShow from "../Show";
    export default {
        name: "action-buttons",
        components: {CuentaBancoShow, CuentaBancoEdit},
        props: ['value'],
        /*methods: {
            destroy() {

            },
        },
        mounted() {
        }*/
    }
</script>