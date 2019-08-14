<template>
    <span>
        <button @click="find(id)" type="button" class="btn btn-sm btn-outline-secondary" title="Ver">
            <i class="fa fa-eye"></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-th"></i> CONSULTA DE SOLICITUD DE BAJA DE CUENTA BANCARIA</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                        <div class="modal-body">
                            <div class="row" v-if="solicitudBaja">
                                <div class="col-12">
                                    <div class="invoice p-3 mb-3">
                                        <div class="row">
                                            <div class="table-responsive col-12">
                                                <table class="table table-striped">
                                                    <tbody>
                                                        <tr>
                                                            <th>Beneficiario:</th>
                                                            <td>{{solicitudBaja.empresa.razon_social}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Cuenta / CLABE:</th>
                                                            <td>{{solicitudBaja.cuenta}}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row"  v-if="solicitudBaja">
                                <div class="col-12">
                                    <div class="invoice p-3 mb-3">
                                        <div class="row">
                                            <div class="col-12">
                                                <h5>Datos de la Cuenta</h5>
                                            </div>
                                        </div>
                                        <form role="form">
                                            <div class="row">
                                                <div class="table-responsive col-md-12">
                                                    <table class="table table-striped">
                                                        <tbody>
                                                            <tr>
                                                                <td><b>Banco:</b></td>
                                                                <td>{{solicitudBaja.banco.razon_social}}</td>
                                                            </tr>
                                                            <tr>
                                                                <td><b>Modena:</b></td>
                                                                <td>{{solicitudBaja.moneda.nombre}}</td>
                                                            </tr>
                                                            <tr>
                                                                <td><b>Sucursal:</b></td>
                                                                <td>{{solicitudBaja.sucursal_format}}</td>
                                                            </tr>
                                                            <tr>
                                                                <td><b>Plaza:</b></td>
                                                                <td>{{solicitudBaja.plaza.clave_format}} ({{solicitudBaja.plaza.nombre}})</td>
                                                            </tr>
                                                            <tr>
                                                                <td><b>Tipo:</b></td>
                                                                <td>{{solicitudBaja.tipo_cuenta}}</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                             </div>
                             <div class="row" v-if="solicitudBaja">
                                <div class="col-12">
                                    <div class = "col-sm-6">
                                        <label>Observaciones:</label>
                                    </div>
                                    <div class = "col-sm-6">
                                          <h6>{{solicitudBaja.observaciones}}</h6>
                                    </div>
                                </div>
                            </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "solicitud-baja-show",
        props: ['id'],
        methods: {
            find(id) {
                this.$store.commit('finanzas/solicitud-baja-cuenta-bancaria/SET_CUENTA', null);
                return this.$store.dispatch('finanzas/solicitud-baja-cuenta-bancaria/find', {
                    id: id,
                    params: { include: ['moneda', 'subcontrato','empresa','banco','tipo','plaza'] }
                }).then(data => {
                    this.$store.commit('finanzas/solicitud-baja-cuenta-bancaria/SET_CUENTA', data);
                    $(this.$refs.modal).modal('show');
                })
            },
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