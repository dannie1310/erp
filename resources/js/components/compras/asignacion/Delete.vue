<template>
    <span>
        <button @click="find()" type="button" class="btn btn-sm btn-outline-danger" title="Eliminar Asignación">
            <i class="fa fa-trash" v-if="!cargando"></i>
            <i class="fa fa-spinner fa-spin" v-else></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-trash"></i> ELIMINAR ASIGNACION DE PROVEEDOR</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="invoice p-3 mb-3">
                                        <div class="row col-md-12">
                                        <div class="col-md-6">
                                            <h5>Folio: &nbsp; <b>{{asignacion.folio_asignacion_format}}</b></h5>
                                        </div>
                                    </div>
                                        <table class="table" v-if="asignacion">
                                            <tbody>
                                                <tr>
                                                    <td class="bg-gray-light" align="center" colspan="4"><h6><b>{{razon_social}}</b></h6></td>                                                    
                                                </tr>
                                                <tr>
                                                    <td class="bg-gray-light"><b>Sucursal:</b></td>
                                                    <td class="bg-gray-light">{{(sucursal.descripcion) ? sucursal.descripcion : '---------'}}</td>
                                                    <td class="bg-gray-light"><b>Usuario Registro</b></td>
                                                    <td class="bg-gray-light">{{(asignacion.usuario) ? asignacion.usuario.nombre : '------'}}</td>
                                                </tr>
                                                <tr>
                                                    <td class="bg-gray-light"><b>Direccion:</b></td>
                                                    <td class="bg-gray-light">{{(sucursal.direccion) ? sucursal.direccion : '---------'}}</td>
                                                    <td class="bg-gray-light"><b>Folio Solicitud:</b></td>
                                                    <td class="bg-gray-light">{{asignacion.folio_solicitud_format}}</td>
                                                </tr>
                                                <tr>
                                                    <td class="bg-gray-light"><b>Concepto</b></td>
                                                    <td class="bg-gray-light">{{asignacion.observaciones}}</td>
                                                    <td class="bg-gray-light"><b>Fecha / Hora Registro</b></td>
                                                    <td class="bg-gray-light">{{asignacion.fecha_asignacion}}</td>                                                    
                                                </tr>
                                            </tbody>
                                        </table>
                                        <div class="row">
                                            <div class="col-12">
                                                <h6><b>Detalle de las partidas</b></h6>
                                            </div>
                                        </div>
                                <div class="row">
                                    <div class="table-responsive col-md-12">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th class="index_corto">#</th>
                                                        <th>Descripción</th>
                                                        <th class="unidad">Unidad</th>
                                                        <th class="no_parte">Cantidad Asignada</th>
                                                        <th class="money">Precio Unitario</th>
                                                        <th class="no_parte">% Descuento</th>
                                                        <th style="width:10%;">Precio Total</th>
                                                        <th class="no_parte">Moneda</th>
                                                        <th style="width:12%;">Precio Total Moneda Conversión</th>                                                        
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(partida, i) in partidas" v-if="partidas.length > 0 && asignacion.partidas.data[0].cotizacion_partida">
                                                        <td>{{i+1}}</td>
                                                        <td >{{(partida.material) ? partida.material.descripcion : '---------'}}</td>
                                                        <td style="text-align: center">{{(partida.material) ? partida.material.unidad : '--------'}}</td>
                                                        <td style="text-align: center">{{partida.cantidad_asignada_format}}</td>
                                                        <td style="text-align: center">{{(partida.cotizacion_partida) ? partida.cotizacion_partida.precio_unitario_format : '-------'}}</td>
                                                        <td style="text-align: center">{{(partida.cotizacion_partida) ? partida.cotizacion_partida.descuento : '--------------'}}</td>
                                                        <td style="text-align: center">{{'$ ' + parseFloat(((partida.cotizacion_partida) ? partida.cotizacion_partida.precio_unitario : 0) * partida.cantidad_asignada).formatMoney(2,'.',',')}}</td>
                                                        <td style="text-align: center">{{(partida.cotizacion_partida) ? partida.cotizacion_partida.moneda.nombre : '-------'}}</td>
                                                        <td style="text-align: center">{{'$ ' + parseFloat(partida.cantidad_asignada * partida.cotizacion_partida.precio_unitario * ((partida.cotizacion.complemento) ? ((partida.cotizacion_partida.id_moneda > 1) ? ((partida.cotizacion_partida.id_moneda == 2) ? partida.cotizacion.complemento.tc_usd : partida.cotizacion.complemento.tc_eur) : 1) : partida.cotizacion_partida.moneda.tipo_cambio_igh)).formatMoney(2, '.', ',')}}</td>                                                        
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                  </div>
                                  <div class="row">
                                    <div class="col-md-12">
                                        <label for="motivo" class="col-form-label">Motivo de eliminación </label>
                                    </div>
                                </div>
                                  <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group row error-content">
                                            <textarea
                                                name="motivo"
                                                id="motivo"
                                                v-model="motivo"
                                                class="form-control"
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
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-danger" :disabled="errors.count() > 0">Eliminar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
export default {
    name: "asignacion-proveedores-delete",
    props: ['id'],
    data() {
        return {
            cargando: false,
            asignacion: false,
            partidas: [],
            motivo: ''
        }
    },
    methods: {
        destroy() {            
            return this.$store.dispatch('compras/asignacion/delete', {
                id: this.id,
                params: {data: this.motivo}
            })
            .then(() => {
                this.$store.dispatch('compras/asignacion/paginate', {params: {sort: 'id', order: 'desc'}})
                .then(data => {
                    this.$store.commit('compras/asignacion/SET_ASIGNACIONES', data.data);
                    this.$store.commit('compras/asignacion/SET_META', data.meta);
                })
            }).finally( ()=>{
                $(this.$refs.modal).modal('hide');
            });
        },
        find() {

            this.cargando = true;
            this.motivo = '';

                this.$store.commit('compras/asignacion/SET_ASIGNACION', null);
                return this.$store.dispatch('compras/asignacion/find', {
                    id: this.id,
                    params:{include: [
                        'partidas.cotizacion_partida.moneda',
                        'partidas.cotizacion.complemento',                        
                        'partidas.cotizacion.empresa',
                        'partidas.cotizacion.sucursal',                        
                        'partidas.cotizacion_partida',
                        'partidas.material',                        
                        'solicitud',
                        'usuario'
                        ]}
                }).then(data => {

                    this.$store.commit('compras/asignacion/SET_ASIGNACION', data);
                    this.asignacion = data;
                    this.partidas = this.asignacion.partidas.data;
                    
                    $(this.$refs.modal).appendTo('body')
                    $(this.$refs.modal).modal('show')
                    
                }).finally(() => {
                    this.cargando = false;
                })
        },
        validate() {

                this.$validator.validate().then(result => {
                    if (result) {
                        this.destroy()
                    }
                });
            },
    },
    computed: {
        razon_social()
        {
            return (this.asignacion.partidas) ? this.asignacion.partidas.data[0].cotizacion.empresa.razon_social : '--------';
        },
        sucursal()
        {
            return (this.asignacion.partidas) ? this.asignacion.partidas.data[0].cotizacion.sucursal : '--------';
        },
    }
}
</script>
<style>
    .icons
    {
        text-align: center;
    }
</style>
