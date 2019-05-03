<template>
    <div class="btn-group">
        <traspaso-entre-cuentas-show :id="value.id" v-if="value.show"></traspaso-entre-cuentas-show>
        <traspaso-entre-cuentas-edit :id="value.id" v-if="value.edit"></traspaso-entre-cuentas-edit>
        <button @click="destroy" v-if="value.delete" type="button" class="btn btn-sm btn-outline-danger" title="Eliminar"><i class="fa fa-trash"></i></button>
    </div>
</template>

<script>

    import TraspasoEntreCuentasShow from "../Show";
    import TraspasoEntreCuentasEdit from "../Edit";
    export default {
        name: "action-buttons",
        components: {TraspasoEntreCuentasEdit, TraspasoEntreCuentasShow},
        props: ['value'],
        methods: {
            destroy() {
                return this.$store.dispatch('tesoreria/traspaso-entre-cuentas/delete', this.value.id)
                    .then(() => {
                        this.$store.commit('tesoreria/traspaso-entre-cuentas/DELETE_TRASPASO', this.value.id);
                    })
            }
        }
    }
</script>