<template>
    <button @click="abrir" type="button" class="btn btn-sm btn-outline-danger" title="Cerrar Periodo">
        <i class="fa fa-calendar-times-o"></i>
    </button>
</template>

<script>
    export default {
        name: "cierre-periodo-cerrar",
        props: ['id'],

        methods: {
            abrir() {
                return this.$store.dispatch('contabilidad/cierre-periodo/cerrar', {
                    id: this.id
                })
                    .then(data => {
                        $(this.$refs.modal).modal('hide');
                        this.$emit('cerrado');
                        this.$store.commit('contabilidad/cierre-periodo/UPDATE_CIERRE', data);
                    })
            }
        }
    }
</script>