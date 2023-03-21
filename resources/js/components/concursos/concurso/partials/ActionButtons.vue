<template>
    <div class="btn-group">
        <router-link :to="{ name: 'concurso-edit', params: {id: this.value.id}}" v-if="value.edit" type="button" class="btn btn-sm btn-outline-primary" title="Editar">
            <i class="fa fa-pencil"></i>
        </router-link>
        <cierre-concurso v-if="value.close" v-bind:id="value.id"></cierre-concurso>
        <formato-apertura-concurso v-bind:id="value.id" ></formato-apertura-concurso>
    </div>
</template>
<script>
    import FormatoAperturaConcurso from "../FormatoAperturaConcurso.vue";
    import CierreConcurso from "../CierreConcurso.vue";

    export default {
        name: "concurso-buttons",
        components: {CierreConcurso, FormatoAperturaConcurso},
        props: ['value'],
        data()
        {
            return {
            }
        },

        methods: {

        }
    }
</script>

<style scoped>

</style>
