<template>
    <div class="btn-group">
        <button title="Aprobar" @click="aprobar" v-if="value.aprobar" type="button" class="btn btn-sm btn-outline-success"><i class="fa fa-thumbs-o-up"></i></button>
        <button title="Revertir Aprobación" @click="desaprobar"  v-if="value.desaprobar" type="button" class="btn btn-sm btn-outline-danger "><i class="fa fa-thumbs-down"></i></button>
    </div>
</template>

<script>
    export default {
        name: "action-buttons",
        props: ['value'],
        methods: {
            aprobar() {
                return this.$store.dispatch('contratos/estimacion/aprobar' ,{ id: this.value.id })
                    .then(() => {
                        this.$store.commit('contratos/estimacion/APROBAR_ESTIMACION', this.value.id);
                    })
            },
            desaprobar() {
                return this.$store.dispatch('contratos/estimacion/revertirAprobacion', { id: this.value.id })
                    .then(() => {
                        this.$store.commit('contratos/estimacion/REVERTIR_APROBACION', this.value.id);
                    })
            }
        }
    }
</script>