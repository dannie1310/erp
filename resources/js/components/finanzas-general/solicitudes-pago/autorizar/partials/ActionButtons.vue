<template>
    <span>
        <div class="btn-group">
            <Show v-bind:id="value.id"/>
            <button @click="autorizar" v-if="value.autorizar" type="button" class="btn btn-sm btn-outline-success" title="Autorizar">
                <i class="fa fa-check"></i>
            </button>
            <button @click="rechazar" v-if="value.rechazar" type="button" class="btn btn-sm btn-outline-danger" title="Rechazar">
                <i class="fa fa-close"></i>
            </button>
        </div>
    </span>
</template>

<script>
    import Show from '../Show';
    export default {
        name: "ActionButtons",
        props: ['value'],
        components : {Show},
        methods: {
            autorizar() {
                return this.$store.dispatch('finanzas-general/solicitud-pago/autorizar', {
                    id: this.value.id,
                    params: {}})
                    .then((data) => {
                       this.paginate();
                    })
            },
            rechazar() {
                return this.$store.dispatch('finanzas-general/solicitud-pago/rechazar', {
                    id: this.value.id,
                    params: {}})
                    .then((data) => {
                        this.paginate();
                    })
            },
            paginate() {
                this.cargando = true;
                return this.$store.dispatch('finanzas-general/solicitud-pago/paginate', { params: {include: 'documento.remesaSinScope'
                ,scope:'autorizacionPendiente'}})
                    .then(data => {
                        this.$store.commit('finanzas-general/solicitud-pago/SET_SOLICITUDES', data.data);
                        this.$store.commit('finanzas-general/solicitud-pago/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },
        }
    }
</script>

<style scoped>

</style>
