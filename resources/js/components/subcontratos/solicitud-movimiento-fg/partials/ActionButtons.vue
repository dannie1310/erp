<template>
    <div class="btn-group">
        <movimiento-bancario-show :id="value.id" v-if="value.show"></movimiento-bancario-show>
        <movimiento-bancario-edit :id="value.id" v-if="value.edit"></movimiento-bancario-edit>
        <button @click="destroy"  v-if="value.delete" type="button" class="btn btn-sm btn-outline-danger" title="Eliminar"><i class="fa fa-trash"></i></button>
    </div>
</template>

<script>
    import MovimientoBancarioShow from "../Show";
    import MovimientoBancarioEdit from "../Edit";
    export default {
        name: "action-buttons",
        components: {MovimientoBancarioEdit, MovimientoBancarioShow},
        props: ['value'],
        methods: {
            destroy() {
                return this.$store.dispatch('tesoreria/movimiento-bancario/delete', this.value.id);
            },
            show() {
                this.$router.push({name: 'movimiento-bancario-show', params: {id: this.value.id}});
            }
        }
    }
</script>