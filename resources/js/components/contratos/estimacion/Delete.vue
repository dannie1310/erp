<template>
    <span>
        <div class="row">
            <div class="col-md-12">
                <div class="invoice p-3 mb-3">
                    <div class="row">
                        <div class="col-12">
                            <h4> <i class="fa fa-list-alt"></i> Eliminar Estimación</h4>
                        </div>
                    </div>

                    <div v-if="estimacion" class="row">
                        <div class="table-responsive col-12">
                            <table class="table table-striped">
                                <tbody>
                                <tr>
                                    <td class="bg-gray-light">
                                        <b>Folio SAO:</b>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "estimacion-delete",
        props: ['id'],
        data() {
            return {
                cargando: false,
                motivo: '',
                estimacion: []
            }
        },
        methods: {
            find() {
                this.motivo = '';
                this.$store.commit('contratos/estimacion/SET_ESTIMACION', null);
                return this.$store.dispatch('contratos/estimacion/find', {
                    id: this.id,
                    params: {include: ['subcontratoEstimacion', 'subcontrato', 'empresa', 'moneda', 'partidas']}
                }).then(data => {
                    this.$store.commit('contratos/estimacion/SET_ESTIMACION', data);
                }).finally(() => {
                    this.cargando = false;
                })
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        // this.store()
                    }
                });
            },
        },
        computed: {
            estimacion() {
                return this.$store.getters['contratos/estimacion/currentEstimacion']
            },
        }
    }
</script>

<style scoped>

</style>