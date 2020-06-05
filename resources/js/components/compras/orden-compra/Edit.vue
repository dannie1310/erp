<template>  
    <span>
        <div class="row">
            <div class="col-12">    
                <div class="invoice p-3 mb-3">
                    <div class="modal-body">
                        <i class="fa fa-spin fa-spinner fa-2x" v-if="cargando"></i>
                        <div class="row" v-if="Object.keys(orden_compra).length > 0">
                            
                            <div class="col-md-4" >
                                <div class="form-group">
                                    <label><b>Folio Solicitud de Compra: </b></label>
                                    {{orden_compra.solicitud.numero_folio_format}}
                                </div>
                            </div>
                            <div class="col-md-8" >
                                <div class="form-group">
                                    <label><b>Solicitud de Compra: </b></label>
                                    {{orden_compra.solicitud.observaciones}}
                                </div>
                            </div>
                            
                            <div class="col-md-4" >
                                <div class="form-group">
                                    <label><b>Folio Orden de Compra: </b></label>
                                    {{orden_compra.numero_folio_format}}
                                </div>
                            </div>
                            
                            <div class="col-md-4" >
                                <div class="form-group">
                                    <label><b>Usuario Registro: </b></label>
                                    {{orden_compra.usuario.nombre}}
                                </div>
                            </div>
                            
                            <div class="col-md-4" >
                                <div class="form-group">
                                    <label><b>Fecha Registro: </b></label>
                                    {{orden_compra.fecha_format}}
                                </div>
                            </div>
                            
                            <div class="col-md-4" >
                                <div class="form-group">
                                    <label><b>Razón Social: </b></label>
                                    {{orden_compra.empresa.razon_social}}
                                </div>
                            </div>
                            
                            <div class="col-md-4" >
                                <div class="form-group">
                                    <label><b>Sucursal: </b></label>
                                    {{orden_compra.sucursal.descripcion}}
                                </div>
                            </div>
                            
                            <div class="col-md-4" >
                                <div class="form-group">
                                    <label><b>Dirección: </b></label>
                                    {{orden_compra.sucursal.direccion}}
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
             <div class="col-12" v-if="Object.keys(orden_compra).length > 0">
                
                <div class="invoice p-3 mb-3">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <br/>
                                <div class="col-12 table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Numero de Parte</th>
                                                <th>Descripción</th>
                                                <th>Unidad</th>
                                                <th>Cantidad</th>
                                                <th>Precio</th>
                                                <th>Descuento</th>
                                                <th>Importe</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(partida, i) in orden_compra.partidas.data">
                                                <td>{{i+1}}</td>
                                                <td>{{partida.material.numero_parte}}</td>
                                                <td>{{partida.material.descripcion}}</td>
                                                <td>{{partida.material.unidad}}</td>
                                                <td>{{partida.cantidad}}</td>
                                                <td>{{partida.precio_unitario}}</td>
                                                <td>Descuento</td>
                                                <td>{{partida.importe}}</td>
                                            </tr>
                                            <tr>
                                                <td  colspan="6"></td>
                                                <td>Subtotal</td>
                                                <td>1000</td>
                                            </tr>
                                            <tr>
                                                <td  colspan="6"></td>
                                                <td>Impuesto</td>
                                                <td>1000</td>
                                            </tr>
                                            <tr>
                                                <td  colspan="6"></td>
                                                <td>Total</td>
                                                <td>1000</td>
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
    </span>
</template>

<script>
export default {
    name: "orden-compra-edit",
    props: ['id'],
    data() {
        return {
            cargando: false,
            orden_compra:[],
            tipo_gasto:[],
            formas_pago_credito:[],
        }
    },
    mounted() {
        this.cargando = true;
        this.getTipoGasto();
        this.getOrdenCompra();
        this.getFormaPagoCredito();
    },
    methods: {
        getOrdenCompra(){
            this.orden_compra = [];
            return this.$store.dispatch('compras/orden-compra/find', {
                    id: this.id,
                    params:{
                        include: ['empresa', 'sucursal', 'usuario', 'partidas.material', 'solicitud']
                    }
                }).then(data => {
                    this.orden_compra = data;
                }).finally(()=>{
                    this.cargando = false;
                })
        },
        getTipoGasto(){
            this.tipo_gasto = [];
            return this.$store.dispatch('cadeco/costo/index', {
                    id: this.id,
                    params:{
                        scope: ['datosContablesConfiguracion'], sort: 'descripcion', order: 'ASC'
                    }
                }).then(data => {
                    this.tipo_gasto = data;
                })
        },
        getFormaPagoCredito(){
            this.tipo_gasto = [];
            return this.$store.dispatch('compras/forma-pago-credito/index', {
                    id: this.id,
                    params:{
                        scope: [], sort: 'id', order: 'ASC'
                    }
                }).then(data => {
                    this.formas_pago_credito = data;
                })
        },
    }

}
</script>

<style>

</style>