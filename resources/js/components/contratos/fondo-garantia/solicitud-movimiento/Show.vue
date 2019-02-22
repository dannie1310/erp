<template>
    <span>
        <button @click="find(id)" v-if="$root.can('consultar_cuenta_almacen')" type="button" class="btn btn-sm btn-outline-secondary" title="Ver">
            <i class="fa fa-eye"></i>
        </button>

        <div ref="modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"><i class="fa fa-search" style="padding-right:3px"></i>Detalle de Solicitud de Movimiento a Fondo de Garantía</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div v-if="solicitud" >
                        <div class="row">
                                 <!-- Fecha -->
                                <div class="offset-md-8 col-md-1">
                                     <label>Fecha:</label>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group error-content">
                                        {{solicitud.fecha_format}}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <!-- Referencia -->
                                <div class="col-md-2">
                                    <label>Referencia:</label>
                                </div>
                                <div class="col-md-6">
                                   {{solicitud.referencia}}
                                </div>
                                <div class="col-md-1">
                                    <label >Tipo:</label>
                                </div>
                                <div class="col-md-3">
                                    {{ solicitud.tipo_solicitud.descripcion }}
                                </div>
                            </div>
                            <div class="row">
                                <!-- Subcontrato -->
                                <div class="col-md-2">
                                    <label>Subcontrato:</label>
                                </div>
                                <div class="col-md-8">
                                   {{ solicitud.fondo_garantia.subcontrato.numero_folio_format }}  [{{ solicitud.fondo_garantia.subcontrato.referencia }}]
                                </div>

                            </div>
                            <div class="row">
                                <!-- Importe -->
                                <div class="offset-md-8 col-md-1">
                                     <label >Importe:</label>
                                </div>
                                <div class="col-md-3">
                                    {{ solicitud.importe }}
                                </div>
                            </div>
                            <div class="row">
                            <!-- Observaciones -->
                                <div class="col-md-2">
                                    <label >Observaciones:</label>
                                </div>
                                <div class="col-md-10">
                                    {{ solicitud.observaciones }}
                                </div>
                            </div>
                            </div>
                        <hr />
                         <div class="row">
                                <div class="col-md-12">
                                     <label ><i class="fa fa-archive" style="padding-right:3px"></i>Histórico de Movimientos:</label>
                                </div>
                         </div>
                        <div v-if="solicitud" class="row">
                            <div class="col-12 table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th class="bg-gray-light">#</th>
                                        <th class="bg-gray-light">Fecha</th>
                                        <th class="bg-gray-light">Movimiento</th>
                                        <th class="bg-gray-light">Usuario</th>
                                        <th class="bg-gray-light">Observaciones</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(movimiento, i) in solicitud.movimientos.data">
                                        <td>{{ i + 1 }}</td>
                                        <td>{{movimiento.created_at_format}}</td>
                                        <td>{{movimiento.tipo_movimiento.estado_resultante_desc}}</td>
                                        <td>{{movimiento.usuario_completo_registra_desc}}</td>
                                        <td>{{movimiento.observaciones}}</td>
                                    </tr>
                                    </tbody>

                                </table>

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
        name: "solicitud-movimiento-fondo-garantia-show",
        props: ['id'],
        methods: {
            find(id) {
                return this.$store.dispatch('contratos/solicitud-movimiento-fg/find', {
                    id: id,
                    params: { include: 'movimientos' }
                }).then(() => {
                    $(this.$refs.modal).modal('show')
                })
            },
        },

        computed: {
            solicitud() {
                return this.$store.getters['contratos/solicitud-movimiento-fg/currentSolicitud']
            }
        }
    }
</script>
<style>
    th
    {
        text-align: center;
    }
</style>