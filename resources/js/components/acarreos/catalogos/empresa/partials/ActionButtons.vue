<template>
    <div class="btn-group">
        <Show v-bind:id="value.id" v-if="value.show"/>
        <Edit v-bind:id="value.id" v-if="value.edit" />
        <button @click="activar" v-if="value.activar" type="button" class="btn btn-sm btn-outline-success" title="Activar">
            <i class="fa fa-check-square"></i>
        </button>
        <Desactivar v-bind:id="value.id" v-if="value.desactivar" />
    </div>
</template>

<script>
    import Show from "../Show";
    import Edit from "../Edit";
    import Desactivar from "../Desactivar";
    export default {
        name: "ActionButtons",
        components: {Show, Edit, Desactivar},
        props: ['value'],
        methods: {
            activar() {
                return this.$store.dispatch('acarreos/empresa/activar', {
                    id: this.value.id,
                    params: {}})
                    .then((data) => {
                        this.$store.commit('acarreos/empresa/UPDATE_EMPRESA', data);
                    })
            },
        }
    }
</script>

<style scoped>

</style>
