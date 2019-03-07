<template>
    <div class="btn-group">
        <CuentaBancoShow  v-bind:id="value.id" v-if="value.show"/>
        <CuentaBancoEdit  v-bind:id="value.id" v-if="value.edit"/>
    </div>
</template>

<script>
    import CuentaBancoEdit from "../Edit";
    import CuentaBancoShow from "../Show";

    export default {
        name: "action-buttons",
        components: {CuentaBancoShow, CuentaBancoEdit},
        props: ['value']
    }
</script>