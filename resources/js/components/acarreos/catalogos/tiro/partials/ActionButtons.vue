<template>
    <div class="btn-group">
        <Show v-bind:id="value.id" v-if="value.show"/>
        <asignar-concepto v-bind:id="value.id" v-if="value.concepto" />
        <button @click="activar" v-if="value.activar" type="button" class="btn btn-sm btn-outline-success" title="Activar">
            <i class="fa fa-check"></i>
        </button>
        <Desactivar v-bind:id="value.id" v-if="value.desactivar"></Desactivar>
    </div>
</template>

<script>
    import AsignarConcepto from "../AsignarConcepto";
    import Desactivar from "../Desactivar";
    import Show from "../Show";
    export default {
        name: "ActionButtons",
        components: {AsignarConcepto, Desactivar, Show},
        props: ['value'],
        methods: {
            activar() {
                return this.$store.dispatch('acarreos/tiro/activar', {
                    id: this.value.id,
                    params: {}})
                    .then((data) => {
                        this.$store.commit('acarreos/tiro/UPDATE_TIRO', data);
                    })
            },

        }
    }
</script>

<style scoped>

</style>
