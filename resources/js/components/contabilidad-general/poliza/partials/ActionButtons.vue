<template>
    <div class="btn-group">
        <button @click="find(value.id)" type="button" class="btn btn-sm btn-outline-secondary  " title="Ver" >
            <i class="fa fa-eye" aria-hidden="true"></i>
        </button>  

    </div>
</template>

<script>
    export default {
        name: "action-buttons",
        props: ['value'],
        methods: {
            find(id) {
                this.$store.commit('contabilidadGeneral/poliza/SET_POLIZA', null);
                return this.$store.dispatch('contabilidadGeneral/poliza/find', {
                    id: id,
                    params: {include: ['movimientos_poliza'], id_empresa : this.value.id_empresa}
                }).then(data => {
                    this.$store.commit('contabilidadGeneral/poliza/SET_POLIZA', data);
                    $(this.$refs.modalEditPoliza).modal('show');
                })
            },
        },
    }
</script>

