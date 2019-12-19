<template>
    <span>
        <button @click="find(id)" type="button" class="btn btn-sm btn-outline-danger" title="Eliminar Venta" v-show="borrar">
            <i class="fa fa-trash"></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-th"></i> ELIMINAR VENTA</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row" v-if="venta">
                            <div class="col-12">
                            <div class="invoice p-3 mb-3">
                                    <div class="row">
                                        <div class="col-12">
                                            <b>Datos de la Venta</b>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="table-responsive col-md-12">
                                            <table class="table table-striped">
                                                <tbody>
                                                    <tr>
                                                        <td class="bg-gray-light"><b>Folio:</b></td>
                                                        <td class="bg-gray-light">{{venta.folio_format}}</td>
                                                        <td class="bg-gray-light"><b>Fecha:</b></td>
                                                        <td class="bg-gray-light">{{venta.fecha_format}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="bg-gray-light"><b>Empresa:</b></td>
                                                        <td class="bg-gray-light">{{venta.empresa.razon_social}}</td>
                                                        <td class="bg-gray-light"><b>Monto:</b></td>
                                                        <td class="bg-gray-light">{{venta.monto}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="bg-gray-light"><b>RFC:</b></td>
                                                        <td class="bg-gray-light">{{venta.empresa.rfc}}</td>
                                                        <td class="bg-gray-light"><b>Estado:</b></td>
                                                        <td class="bg-gray-light">
                                                            <small class="badge" :class="{'badge-danger': venta.estado.id == '-1',
                                                                                         'badge-primary': venta.estado.id == '0',
                                                                                         'badge-success': venta.estado.id == '1'}">
                                                                 {{ venta.estado.descripcion }} </small></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="bg-gray-light"><b>Observaciones:</b></td>
                                                        <td class="bg-gray-light">{{venta.observaciones_format}}</td>
                                                        <td class="bg-gray-light" v-if="venta.usuario"><b>Usuario Registró</b></td>
                                                        <td class="bg-gray-light" v-else="venta.usuario"></td>
                                                        <td class="bg-gray-light" v-if="venta.usuario">{{venta.usuario.nombre}}</td>
                                                        <td class="bg-gray-light" v-else="venta.usuario"></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                     <div class="row">
                                        <div class="col-12">
                                           <b>Detalle de las partidas</b>
                                        </div>
                                     </div>
                                    <div class="row">
                                        <div class="table-responsive col-md-12">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>No. de Parte</th>
                                                        <th>Descripción</th>
                                                        <th>Unidad</th>
                                                        <th>Cantidad</th>
                                                        <th>Precio/U</th>
                                                        <th>Importe</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(partida, i) in venta.partidas.data">
                                                        <td>{{i+1}}</td>
                                                        <td >{{partida.material.numero_parte}}</td>
                                                        <td >{{partida.material.descripcion}}</td>
                                                        <td>{{partida.unidad}}</td>
                                                        <td style="text-align: right">{{partida.cantidad_decimal}}</td>
                                                        <td style="text-align: right">{{partida.precio_unitario}}</td>
                                                        <td style="text-align: right">{{partida.importe}}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <div class="row">
                                                <div class=" col-md-12" align="right">
                                                        <label class="col-sm-2 col-form-label"></label>
                                                        <label class="col-sm-2 col-form-label">Subtotal:</label>
                                                        <label class="col-sm-2 col-form-label" style="text-align: right">{{venta.subtotal}}</label>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class=" col-md-12" align="right">
                                                        <label class="col-sm-2 col-form-label"></label>
                                                        <label class="col-sm-2 col-form-label">IVA(16%)</label>
                                                        <label class="col-sm-2 col-form-label" style="text-align: right">{{venta.impuesto}}</label>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class=" col-md-12" align="right">
                                                        <label class="col-sm-2 col-form-label"></label>
                                                        <label class="col-sm-2 col-form-label">Total:</label>
                                                        <label class="col-sm-2 col-form-label" style="text-align: right">{{venta.monto}}</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group row error-content">
                                            <label for="motivo" class="col-sm-2 col-form-label">Motivo:</label>
                                        <div class="col-sm-10">
                                            <textarea
                                                name="motivo"
                                                id="motivo"
                                                class="form-control"
                                                v-model="motivo"
                                                v-validate="{required: true}"
                                                data-vv-as="Motivo"
                                                :class="{'is-invalid': errors.has('motivo')}"
                                            ></textarea>
                                            <div class="invalid-feedback" v-show="errors.has('motivo')">{{ errors.first('motivo') }}</div>
                                        </div>
                                    </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-danger" :disabled="errors.count() > 0 || motivo === ''" @click="validate">Eliminar</button>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>
<script>
export default {
    name: "cancelar-venta",
    props: ['id','pagina','borrar'],
    data() {
        return {
            motivo:'',
            cargando: false,
        }
    },
    methods: {
        destroy() {
            return this.$store.dispatch('ventas/venta/delete', {
                id: this.id,
                params: {data: [this.$data.motivo]}
            })
            .then(() => {
                this.$store.dispatch('ventas/venta/paginate', {})
                .then(data => {
                    this.$store.commit('ventas/venta/SET_VENTAS', data.data);
                    this.$store.commit('ventas/venta/SET_META', data.meta);
                })
            }).finally( ()=>{
                $(this.$refs.modal).modal('hide');
            });
        },
        find(id) {
            this.$store.commit('ventas/venta/SET_VENTA', null);
            return this.$store.dispatch('ventas/venta/find', {
                id: id,
                params: {include: ['empresa', 'partidas.material', 'usuario', 'estado']}
            }).then(data => {
                this.$store.commit('ventas/venta/SET_VENTA', data);
                $(this.$refs.modal).modal('show')
            })
        },
        validate() {
            this.$validator.validate().then(result => {
                if (result) {
                    if(this.motivo === '') {
                        swal('¡Error!', 'Debe colocar un motivo para realizar la operación.', 'error')
                    }
                    else {
                        this.destroy()
                    }
                }
            });
        },
    },
    computed:{
        venta() {
            return this.$store.getters['ventas/venta/currentVenta'];
        }
    }
}
</script>
<style>
    .icons
    {
        text-align: center;
    }
</style>