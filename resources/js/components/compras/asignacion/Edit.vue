<template>
    <span>
        <div class="row">
            <div class="col-12">
                <div class="invoice p-3 mb-3">
                    <div class="modal-body">
                        <i class="fa fa-spin fa-spinner fa-2x" v-if="cargando"></i>
                        <div class="row" v-if="Object.keys(asignaciones).length > 0">
                            <div class="col-md-2" >
                                <div class="form-group">
                                    <label><b>Folio Asignación: </b></label>
                                    {{asignaciones.folio_asignacion_format}}
                                </div>
                            </div>
                            <div class="col-md-5" >
                                <div class="form-group">
                                    <label><b>Usuario Registro Asignación: </b></label>
                                    {{asignaciones.usuario.nombre}}
                                </div>
                            </div>

                            <div class="col-md-2" >
                                <div class="form-group">
                                    <label><b>Estado: </b></label>
                                    {{asignaciones.estado_format}}
                                </div>
                            </div>
                            <div class="col-md-3" >
                                <div class="form-group">
                                    <label><b>Fecha de Registro: </b></label>
                                    {{asignaciones.fecha_asignacion}}
                                </div>
                            </div>

                            <div class="col-md-2" >
                                <div class="form-group">
                                    <label><b>Folio Solicitud de Compra: </b></label>
                                    {{asignaciones.numero_folio_format}}
                                </div>
                            </div>
                            <div class="col-md-7" >
                                <div class="form-group">
                                    <label><b>Solicitud de Compra: </b></label>
                                    {{asignaciones.observaciones}}
                                </div>
                            </div>

                            <div class="col-md-3" >
                                <div class="form-group">
                                    <label><b>Fecha y Hora de Registro: </b></label>
                                    {{asignaciones.fecha_registro}}
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12" v-if="Object.keys(asignaciones).length > 0">

                <div class="invoice p-3 mb-3">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-primary pull-right" @click="generarOC"  v-if="asignaciones.asignaciones_pendientes_o_compra">Generar Ordenes de Compra</button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <br/>
                                <div class="col-12 table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th colspan="4" rowspan="4" class="text-left"><h5></h5></th>
                                            </tr>
                                            <tr class="bg-gray-light">
                                                <th colspan="6" >
                                                    <select
                                                        :disabled="Object.keys(asignaciones.data).length === 1"
                                                        type="text"
                                                        name="id_transaccion"
                                                        data-vv-as="Razón Social"
                                                        class="form-control"
                                                        id="id_transaccion"
                                                        v-model="id_transaccion">
                                                        <option v-for="asignacion in asignaciones.data" :value="asignacion.id_transaccion">{{ asignacion.razon_social }}</option>
                                                    </select>
                                                </th>
                                                <!-- <th colspan="2"  v-if="!asignaciones.data[id_transaccion].orden_compra">
                                                    <button type="button" class="btn btn-primary pull-right" @click="generarOrdenCompraIndividual">Generar Orden Compra</button>
                                                </th> -->
                                            </tr>
                                            <tr class="bg-gray-light">
                                                <th colspan="6" >{{asignaciones.data[id_transaccion].sucursal}}</th>
                                            </tr>
                                            <tr class="bg-gray-light">
                                                <th colspan="6"  >{{asignaciones.data[id_transaccion].direccion}}</th>
                                            </tr>
                                            <tr>
                                                <th>#</th>
                                                <th style="width: 32%;">Descripción</th>
                                                <th style="width: 8%;">Unidad</th>
                                                <th style="width: 8%;">Cantidad Solicitada</th>

                                                <th class="bg-gray-light ">Precio Unitario</th>
                                                <th class="bg-gray-light">% Descuento</th>
                                                <th class="bg-gray-light ">Precio Total</th>
                                                <th class="bg-gray-light">Moneda</th>
                                                <th class="bg-gray-light ">Precio Total Moneda Conversión</th>
                                                <th class="bg-gray-light th_money_input">Cantidad Asignada</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(item, i) in asignaciones.data[id_transaccion].partidas" v-if="asignaciones.data[id_transaccion].partidas">
                                                <td>{{ i+1}}</td>
                                                <td>{{ item.descripcion}}</td>
                                                <td>{{ item.unidad}}</td>
                                                <td>{{ item.cantidad_solicitada}}</td>
                                                <td class="money">{{ item.precio_unitario}}</td>
                                                <td>{{ item.descuento}}</td>
                                                <td class="money">{{ item.precio_total }}</td>
                                                <td>{{ item.moneda}}</td>
                                                <td class="money">{{ item.precio_moneda_conv }}</td>
                                                <td>{{ item.cantidad_asignada}}</td>
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
        <div class="row">
            <div class="col-12" v-if="Object.keys(asignaciones).length > 0 && asignaciones.asignaciones_con_o_compra > 0">
                <div class="invoice p-3 mb-3">
                    <div class="row">
                        <div class="col-12">
                            <h4>
                                <i class="fa fa-list"></i> Ordenes de Compra Generadas
                            </h4>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Folio O. Compra</th>
                                            <th>Razón Social</th>
                                            <th>Concepto</th>
                                            <th>Fecha</th>
                                            <th>Monto</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody v-for="(ordenes, i) in asignaciones.data" v-if="ordenes.orden_compra.length > 0">
                                        <tr v-for="(item, j) in ordenes.orden_compra">
                                            <td>{{ item.numero_folio_format}}</td>
                                            <td>{{ ordenes.razon_social}}</td>
                                            <td :title="item.observaciones">{{ item.observaciones_format}}</td>
                                            <td>{{ item.fecha_format}}</td>
                                            <td class="money">{{ item.monto_format}}</td>
                                            <td>
                                                <div class="custom-control custom-switch" v-if="$root.can('eliminar_orden_compra')">
                                                    <input type="checkbox" class="custom-control-input" v-if="!item.entradas_almacen" :id="`enable[${i}-${j}]`" v-model="item.eliminar" >&nbsp;
                                                    <label class="custom-control-label" :for="`enable[${i}-${j}]`" :title="item.entradas_almacen?'Con Entrada Almacen':'Eliminar'">
                                                        <i class="fa fa-trash" v-if="!item.entradas_almacen"></i>
                                                    </label>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-12">
                                <button type="button" class="btn btn-primary pull-right" @click="modalEliminacion" v-if="$root.can('eliminar_orden_compra')" :disabled="!eliminarOrdenes">Eliminar Orden(es) de Compra</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <nav>
            <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-sn" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-th"></i> ELIMINAR ORDEN(ES) DE COMPRA</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
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
                                                <div class="error-label" v-show="errors.has('motivo')">{{ errors.first('motivo') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="button" class="btn btn-danger" @click="eliminarOC"  :disabled="motivo == ''">Eliminar</button>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

    </span>
</template>

<script>
export default {
    name: "asignacion-proveedor-edit",
    props: ['id'],
    data() {
        return {
            cargando: false,
            asignaciones:[],
            cotizacion:[],
            id_transaccion:'',
            motivo:'',
        }
    },
    mounted() {
        this.getAsignacion();
    },
    computed: {
        eliminarOrdenes(){
            let cantidad = 0;
            if(this.asignaciones.data){
                Object.values(this.asignaciones.data).forEach(item => {
                    item.orden_compra.forEach(orden => {
                        if(orden.eliminar){
                        cantidad = cantidad +1;
                    }
                    });
                });
            }
            return cantidad > 0;
        }
    },
    methods: {
        asignacion(){
            this.cargando = true;
            this.asignacion = [];
            return this.$store.dispatch('compras/asignacion/find', {
                    id: this.id,
                    params:{
                        include: ['solicitud_compra','partidas.item_solicitud', 'partidas.cotizacion_partida','partidas.cotizacion.empresa','partidas.cotizacion.sucursal']
                    }
                }).then(data => {
                    this.asignacion = data;
                    this.cotizacion = data.partidas.data[0].cotizacion;
                }).finally(()=>{
                    this.cargando = false;
                })
        },
        eliminarOC(){
            this.cargando = true;
            let ordenes_c = [];
            Object.values(this.asignaciones.data).forEach(item => {
                item.orden_compra.forEach(orden => {
                    if(orden.eliminar){
                        ordenes_c.push(orden.id);
                    }
                });

            });
            if(ordenes_c.length > 0){
                return this.$store.dispatch('compras/orden-compra/eliminarOrdenes', { data:{data:ordenes_c, motivo:this.motivo}}
                  ).then(data => {
                    this.getAsignacion();
                }).finally(()=>{
                    this.cargando = false;
                    $(this.$refs.modal).modal('hide');
                })
            }else{
                swal('Atención', 'Seleccione al menos una orden de compra a eliminar', 'warning');
            }
        },
        modalEliminacion(){
            $(this.$refs.modal).appendTo('body')
            $(this.$refs.modal).modal('show');
        },
        getAsignacion(){
            this.cargando = true;
            this.asignacion = [];
            return this.$store.dispatch('compras/asignacion/getAsignacion', {
                    id: this.id,
                    params:{}
                }).then(data => {
                    this.asignaciones = data;
                    this.id_transaccion = Object.keys(data.data)[0];
                }).finally(()=>{
                    this.cargando = false;
                })
        },
        generarOC(){
            return this.$store.dispatch('compras/asignacion/generarOC', {
                    data: {
                        id: this.id
                    }
                }).then(data => {
                    this.$router.push({name: 'asignacion-proveedor'});
                }) .finally(() => {
                    this.descargando = false;
                })
        },
        generarOrdenCompraIndividual(){
            return this.$store.dispatch('compras/asignacion/generarOrdenIndividual', {
                    data: {
                        id_transaccion: this.id_transaccion,
                        id: this.id,
                    }
                }).then(data => {
                    this.getAsignacion();
                }) .finally(() => {
                    this.descargando = false;
                })
        },
        precioTotal(index){
            let descuento = isNaN(this.asignacion.partidas.data[index].cotizacion_partida.descuento)?0:this.asignacion.partidas.data[index].cotizacion_partida.descuento;
            if(descuento === 0){
                return this.asignacion.partidas.data[index].cantidad * this.asignacion.partidas.data[index].cotizacion_partida.precio_unitario;
            }
            let p_u_descuento = ((descuento/100) * this.asignacion.partidas.data[index].cotizacion_partida.precio_unitario) - this.asignacion.partidas.data[index].cotizacion_partida.precio_unitario;
            return this.asignacion.partidas.data[index].cantidad * p_u_descuento
        },
        precioTotalMC(index){
            return this.precioTotal(index) * this.asignacion.partidas.data[index].cotizacion_partida.moneda.tipo_cambio;
        },

    },

}
</script>

<style>

</style>
