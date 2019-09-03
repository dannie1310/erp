<template>
      <span>
        <button @click="find(id)" type="button" class="btn btn-sm btn-outline-secondary" title="Ver">
            <i class="fa fa-eye"></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-th"></i> CONSULTA DE CUENTA BANCARIA EMPRESA</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row" v-if="cuentaEmpresa">
                            <div class="col-12">
                                <div class="invoice p-3 mb-3">
                                    <div class="row">
                                        <div class="table-responsive col-12">
                                            <table class="table table-striped">
                                                <tbody>
                                                    <tr>
                                                        <th>Tipo de Beneficiario:</th>
                                                        <td>{{cuentaEmpresa.empresa.tipo}}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Beneficiario:</th>
                                                        <td>{{cuentaEmpresa.empresa.razon_social}}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Banco:</th>
                                                        <td>{{cuentaEmpresa.banco.razon_social}}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Cuenta / CLABE:</th>
                                                        <td>{{cuentaEmpresa.cuenta}}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Moneda:</th>
                                                        <td>{{cuentaEmpresa.moneda.nombre}}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Sucursal:</th>
                                                        <td>{{cuentaEmpresa.sucursal_format}}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Plaza:</th>
                                                        <td>{{cuentaEmpresa.plaza.clave_format}} ({{cuentaEmpresa.plaza.nombre}})</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Tipo:</th>
                                                        <td>{{cuentaEmpresa.tipo}}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Fecha Registro:</th>
                                                        <td>{{cuentaEmpresa.fecha_format}}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="table-responsive col-12" v-if="cuentaEmpresa.solicitud_alta">
                                        <table class="table table-striped">
                                            <tbody>
                                            <tr>
                                                <th colspan="2">Solicitud de Alta:</th>
                                                <td colspan="2">{{cuentaEmpresa.solicitud_alta.numero_folio_format_orden}}</td>
                                            </tr>
                                            <tr v-for="(mov,i) in cuentaEmpresa.solicitud_alta.movimientos.data"  v-if="mov && mov.id_tipo_movimiento == 2">
                                                <th>Fecha de Autorización:</th>
                                                <td>{{mov.fecha_format}}</td>
                                                <th>Autorizó:</th>
                                                <td>{{mov.usuario.nombre}}</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="table-responsive col-12" v-if="cuentaEmpresa.solicitud_baja">
                                        <table class="table table-striped">
                                            <tbody>
                                            <tr>
                                                <th colspan="2">Solicitud de Baja:</th>
                                                <td colspan="2">{{cuentaEmpresa.solicitud_baja.numero_folio_format_orden}}</td>
                                            </tr>
                                            <tr v-for="(mov_baja,i) in cuentaEmpresa.solicitud_baja.movimientos.data" v-if="mov_baja && mov_baja.id_tipo_movimiento == 2">
                                                <th>Fecha de Autorización:</th>
                                                <td>{{mov_baja.fecha_format}}</td>
                                                <th>Autorizó:</th>
                                                <td>{{mov_baja.usuario.nombre}}</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
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
        name: "cuenta-empresa-show",
        props: ['id'],
        methods: {
            find(id) {
                this.$store.commit('finanzas/cuenta-bancaria-empresa/SET_CUENTA', null);
                return this.$store.dispatch('finanzas/cuenta-bancaria-empresa/find', {
                    id: id,
                    params: { include: ['empresa', 'tipo', 'banco', 'plaza', 'moneda', 'solicitud_alta.movimientos.usuario', 'solicitud_baja.movimientos.usuario']}
                }).then(data => {
                    this.$store.commit('finanzas/cuenta-bancaria-empresa/SET_CUENTA', data);
                    $(this.$refs.modal).modal('show');
                })
            }
        },
        computed: {
            cuentaEmpresa() {
                return this.$store.getters['finanzas/cuenta-bancaria-empresa/currentCuenta'];
            }
        }
    }
</script>