<template>
    <span>
        <button @click="find(id)" type="button" class="btn btn-sm btn-outline-danger" title="Rechazar">
            <i class="fa fa-trash"></i>
        </button>
    </span>
</template>

<script>
    export default {
        name: "salida-almacen-delete",
        props: ['id'],
        data() {
            return {
                observaciones: ''
            }
        },
        methods: {
            find(id) {
                this.$store.commit('finanzas/solicitud-alta-cuenta-bancaria/SET_CUENTA', null);
                return this.$store.dispatch('finanzas/solicitud-baja-cuenta-bancaria/find', {
                    id: id,
                    params: { include: ['moneda', 'subcontrato','empresa','banco','tipo','plaza','movimientos','movimientos.usuario','movimiento_solicitud'] }
                }).then(data => {
                    this.$store.commit('finanzas/solicitud-baja-cuenta-bancaria/SET_CUENTA', data);
                    $(this.$refs.modal).modal('show');
                })
            },
            init() {
                this.pdf()
            },
            pdf(){
                var url = '/api/finanzas/gestion-cuenta-bancaria/solicitud-baja/pdf/' + this.id +'?db=' + this.$session.get('db') + '&idobra=' + this.$session.get('id_obra')+'&access_token='+this.$session.get('jwt');
                $(this.$refs.body).html('<iframe src="'+url+'"  frameborder="0" height="100%" width="100%">CONSULTA DE ARCHIVO DE SOPORTE SOLICITUD DE ALTA DE CUENTA BANCARIA</iframe>');
                $(this.$refs.modalPDF).modal('show');
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.rechazar()
                    }
                });
            },
            rechazar() {
                return this.$store.dispatch('finanzas/solicitud-baja-cuenta-bancaria/rechazar', {
                    id: this.id,
                    params: { include: ['moneda', 'subcontrato','empresa','banco','tipo','plaza','mov_estado','movimientos.usuario','movimiento_solicitud'], data:[this.$data.observaciones]}
                }).then(data => {
                    this.$store.commit('finanzas/solicitud-baja-cuenta-bancaria/UPDATE_CUENTA', data)
                    $(this.$refs.modal).modal('hide');
                })
                    .finally( ()=>{
                        this.cargando = false;
                    });
            }
        },
        computed: {
            solicitudBaja() {
                return this.$store.getters['finanzas/solicitud-baja-cuenta-bancaria/currentCuenta'];
            }
        }
    }
</script>

<style scoped>

</style>