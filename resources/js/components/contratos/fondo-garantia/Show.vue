<template>
    <span>
        <button @click="init" v-if="$root.can('consultar_fondo_garantia')" type="button" class="btn btn-sm btn-outline-secondary" :disabled="cargando" title="Ver">
            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
            <i class="fa fa-eye" v-else></i>
        </button>

        <div ref="modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"><i class="fa fa-search" style="padding-right:3px"></i>Detalle del Fondo de Garantía</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div >
                            <div v-if="fondo_garantia">
                                <div class="row" v-if="!(fondo_garantia.porcentaje_abonos>0 && fondo_garantia.porcentaje_cargos>0)">
                                    <div class=" offset-md-9 col-md-3">
                                        <div class="info-box">
                                          <span class="info-box-icon bg-success"><i class="fa fa-dollar"></i></span>
                                          <div class="info-box-content">
                                            <span class="info-box-text">Saldo</span>
                                            <span class="info-box-number">{{fondo_garantia.saldo_format}}</span>
                                          </div>
                                            <!-- /.info-box-content -->
                                        </div>
                                    </div>
                                </div>

                                <div class="row" v-else>
                                    <div class=" offset-md-6 col-md-3">
                                        <div class="info-box">
                                          <span class="info-box-icon bg-success"><i class="fa fa-dollar"></i></span>
                                          <div class="info-box-content">
                                            <span class="info-box-text">Saldo</span>
                                            <span class="info-box-number">{{fondo_garantia.saldo_format}}</span>
                                          </div>
                                            <!-- /.info-box-content -->
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div style="text-align: right">
                                            <label>Suma Cargos:</label> {{fondo_garantia.suma_cargos_format}}
                                        </div>
                                        <div  style="text-align: right">
                                            <label>Suma Abonos:</label> {{fondo_garantia.suma_abonos_format}}
                                        </div>
                                        <div class="progress  progress-sm">
                                            <div class="progress-bar bg-success progress-bar-striped" role="progressbar" :aria-valuenow="fondo_garantia.porcentaje_cargos" aria-valuemin="0" aria-valuemax="100" :style="fondo_garantia.estilo_porcentaje_cargos">
                                                <span class="sr-only">{{fondo_garantia.porcentaje_cargos}}%</span>
                                            </div>
                                        </div>
                                        <div class="progress  progress-sm">
                                            <div class="progress-bar bg-danger progress-bar-striped" role="progressbar" :aria-valuenow="fondo_garantia.porcentaje_abonos" aria-valuemin="0" aria-valuemax="100" :style="fondo_garantia.estilo_porcentaje_abonos">
                                                <span class="sr-only">{{fondo_garantia.porcentaje_abonos}}%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-if="fondo_garantia">
                                <div class="row" v-if="fondo_garantia.subcontrato.id">
                                    <div class="col-md-12">
                                        <detalle-subcontrato :subcontrato="fondo_garantia.subcontrato" ></detalle-subcontrato>
                                    </div>
                                </div>
                            </div>

                            <div  v-if="fondo_garantia" class="card">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label ><i class="fa fa-archive" style="padding-right:3px"></i>Histórico de Movimientos:</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">

                                    <div v-if="fondo_garantia.movimientos" class="row">
                                        <div class="col-12 table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th class="bg-gray-light">#</th>
                                                        <th class="bg-gray-light">Fecha</th>
                                                        <th class="bg-gray-light">Movimiento</th>
                                                        <th class="bg-gray-light">Cargo</th>
                                                        <th class="bg-gray-light">Abono</th>
                                                        <th class="bg-gray-light">Saldo</th>
                                                        <th class="bg-gray-light">Usuario</th>
                                                        <th class="bg-gray-light">Observaciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(movimiento, i) in fondo_garantia.movimientos.data">
                                                        <td>{{ i + 1 }}</td>
                                                        <td>{{movimiento.created_at_format}}</td>
                                                        <td>{{movimiento.tipo_movimiento.descripcion}}</td>
                                                        <td class="money">{{(movimiento.tipo_movimiento.naturaleza == 1)?movimiento.importe_abs_format:'-'}}</td>
                                                        <td class="money">{{(movimiento.tipo_movimiento.naturaleza == 2)?movimiento.importe_abs_format:'-'}}</td>
                                                        <td class="money">{{movimiento.saldo_format}}</td>
                                                        <td>{{movimiento.usuario_registra_desc}}</td>
                                                        <td>{{movimiento.observaciones}}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    import DetalleSubcontrato from "../subcontrato/partials/DetalleSubcontrato";
    export default {
        name: "fondo-garantia-show",
        components: {DetalleSubcontrato},
        props: ['id'],
        data() {
            return {
                fondo_garantia : null,
                cargando: false,
            }
        },
        methods: {
            init(){
                this.find({id: this.id}).then(data=>{

                    this.fondo_garantia = data
                    $(this.$refs.modal).appendTo('body')
                    $(this.$refs.modal).modal('show')
                })
                    .finally(() => {
                        this.cargando = false;
                    });
            },
            find(payload) {
                this.cargando = true;
                return this.$store.dispatch('contratos/fondo-garantia/find', {
                    id: payload.id,
                    params: { include: 'movimientos,subcontrato.empresa,subcontrato.moneda' }
                })

            },
        },
        computed: {
            /*fondo_garantia() {
                return this.$store.getters['contratos/fondo-garantia/currentFondoGarantia']
            }*/
        }
    }
</script>
<style scoped>
    .modal-body {
        background-color: #dedede;
    }
    .money
    {
        text-align: right;
        width: 150px;
        max-width: 150px;
        min-width: 100px;
    }
</style>
