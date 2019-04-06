<template>
    <div class="btn-group">
        <TipoCuentaContableShow v-if="value.show" v-bind:id="value.id" />
        <TipoCuentaContableEdit v-if="value.edit" v-bind:id="value.id" />
        <button @click="destroy" v-if="value.delete" type="button" class="btn btn-sm btn-outline-danger"><i class="fa fa-trash"></i></button>
    </div>
</template>

<script>
    import TipoCuentaContableShow from "../Show";
    import TipoCuentaContableEdit from "../Edit";
    export default {
        name: "action-buttons",
        components: {TipoCuentaContableShow, TipoCuentaContableEdit},
        props: ['value'],
        methods: {
            destroy() {

            },
        },
        mounted() {
        }
    }
</script>