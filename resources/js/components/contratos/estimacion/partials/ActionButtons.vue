<template>
    <div class="btn-group">
        <button title="Aprobar" @click="aprobar" v-if="value.aprobar" type="button" class="btn btn-sm btn-outline-success" :disabled="aprobando">
            <i v-if="aprobando" class="fa fa-spin fa-spinner"></i>
            <i v-else class="fa fa-thumbs-o-up"></i>
        </button>
        <button title="Revertir Aprobación" @click="desaprobar"  v-if="value.desaprobar" type="button" class="btn btn-sm btn-outline-danger" :disabled="revirtiendo">
            <i v-if="revirtiendo" class="fa fa-spin fa-spinner"></i>
            <i v-else class="fa fa-thumbs-down"></i>
        </button>
    </div>
</template>

<script>
    export default {
        name: "action-buttons",
        props: ['value'],
        data() {
            return {
                aprobando: false,
                revirtiendo: false
            }
        },
        methods: {
            aprobar() {
                this.aprobando = true;
                return this.$store.dispatch('contratos/estimacion/aprobar' ,{ id: this.value.id })
                    .then(() => {
                        this.$store.commit('contratos/estimacion/APROBAR_ESTIMACION', this.value.id);
                    })
                    .finally(() => {
                        this.aprobando = false;
                    })
            },

            desaprobar() {
                this.revirtiendo = true;
                return this.$store.dispatch('contratos/estimacion/revertirAprobacion', { id: this.value.id })
                    .then(() => {
                        this.$store.commit('contratos/estimacion/REVERTIR_APROBACION', this.value.id);
                    })
                    .finally(() => {
                        this.revirtiendo = false;
                    })
            }
        }
    }
</script>