<template>
    <div class="btn-group " v-if="value.id">
    <SucursalShow v-if="value.show" @click="value.id" v-bind:id="value.id" />
    </div>


</template>

<script>


    import SucursalCreate from "../Create";
    import SucursalShow from "../Show";
    import SucursalIndex from "../Index";
    export default {

        name: "action-buttons",
        components: { SucursalCreate, SucursalShow, SucursalIndex} ,
        props: ['value'],
        methods: {
            destroy() {

            },
            show() {
                this.$router.push({name: 'sucursal-show', params: {id: this.value.id}});
            },
        }
    }
</script>
