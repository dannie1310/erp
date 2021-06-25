<template>
    <div class="btn-group">
        <Edit v-bind:anio="value.anio" v-bind:semana="value.semana" v-bind:id_proyecto="value.id_proyecto" v-if="value.edit" />
    </div>
</template>

<script>
    import Edit from "../Edit";
    export default {
        name: "ActionButtons",
        props: ['value'],
        components: {Edit},
        methods: {

        }
    }
</script>

<style scoped>

</style>
