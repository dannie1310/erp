<template>
    <div class="btn-group">
        <Show v-bind:id="value.id" v-if="value.show"/>
        <Edit v-bind:id="value.id" v-if="value.edit" />
        <!--<button @click="activar" v-if="value.activar" type="button" class="btn btn-sm btn-outline-success" title="Activar">
            <i class="fa fa-check-square"></i>
        </button>
        <Desactivar v-bind:id="value.id" v-if="value.desactivar"></Desactivar>-->
    </div>
</template>

<script>
   // import Desactivar from "../Desactivar";
    import Show from "../Show";
    import Edit from "../Edit";
    export default {
        name: "ActionButtons",
        components: {Show, Edit},
        props: ['value'],
        methods: {
           /* activar() {
                return this.$store.dispatch('acarreos/tiro/activar', {
                    id: this.value.id,
                    params: {}})
                    .then((data) => {
                        this.$store.commit('acarreos/tiro/UPDATE_TIRO', data);
                    })
            },*/
        }
    }
</script>

<style scoped>

</style>
