<template>
    <button @click="abrir" type="button" class="btn btn-sm btn-outline-success" title="Abrir Periodo">
        <i class="fa fa-calendar-check-o"></i>
    </button>
</template>

<script>
    export default {
        name: "cierre-periodo-abrir",
        props: ['id'],

        methods: {
            abrir() {
                return this.$store.dispatch('contabilidad/cierre-periodo/abrir', {
                    id: this.id
                })
                    .then(data => {
                        $(this.$refs.modal).modal('hide');
                        this.$emit('abierto');
                        this.$store.commit('contabilidad/cierre-periodo/UPDATE_CIERRE', data);
                    })
            }
        }
    }
</script>