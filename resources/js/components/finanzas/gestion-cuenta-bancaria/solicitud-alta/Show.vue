<template>
    <span>
        <button @click="find(id)" type="button" class="btn btn-sm btn-outline-secondary" title="Ver">
            <i class="fa fa-eye"></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-th"></i> CONSULTA DE SOLICITUD DE ALTA DE CUENTA BANCARIA</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                        <div class="modal-body">
                            <div class="row" v-if="solicitudAlta">
                                <div class="col-12">
                                    <div class="invoice p-3 mb-3">
                                        <div class="row">
                                            <div class="table-responsive col-12">
                                                <table class="table table-striped">
                                                    <tbody>
                                                        <tr>
                                                            <th>Tipo de Beneficiario:</th>
                                                            <td>{{solicitudAlta.empresa.tipo_empresa}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Beneficiario:</th>
                                                            <td>{{solicitudAlta.empresa.razon_social}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Banco:</th>
                                                            <td>{{solicitudAlta.banco.razon_social}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Cuenta / CLABE:</th>
                                                            <td>{{solicitudAlta.cuenta}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Modena:</th>
                                                            <td>{{solicitudAlta.moneda.nombre}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Sucursal:</th>
                                                            <td>{{solicitudAlta.sucursal_format}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Plaza:</th>
                                                            <td>{{solicitudAlta.plaza.clave_format}} ({{solicitudAlta.plaza.nombre}})</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Tipo:</th>
                                                            <td>{{solicitudAlta.tipo_cuenta}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Observaciones:</th>
                                                            <td>{{solicitudAlta.observaciones}}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-file-pdf-o"></i>  Ver Archivo Soporte</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "solicitud-alta-show",
        props: ['id'],
        methods: {
            find(id) {
                this.$store.commit('finanzas/solicitud-alta-cuenta-bancaria/SET_CUENTA', null);
                return this.$store.dispatch('finanzas/solicitud-alta-cuenta-bancaria/find', {
                    id: id,
                    params: { include: ['moneda', 'subcontrato','empresa','banco','tipo','plaza'] }
                }).then(data => {
                    this.$store.commit('finanzas/solicitud-alta-cuenta-bancaria/SET_CUENTA', data);
                    $(this.$refs.modal).modal('show');
                })
            }
        },
        computed: {
            solicitudAlta() {
                return this.$store.getters['finanzas/solicitud-alta-cuenta-bancaria/currentCuenta'];
            }
        }
    }
</script>

<style scoped>

</style>