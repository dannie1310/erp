<template>
      <span>
         <button @click="find" type="button" class="btn btn-sm btn-outline-secondary" title="Show">
             <i class="fa fa-eye"></i>
         </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-th"></i> CONSULTA DE AJUSTE DE INVENTARIO</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row"  v-if="ajustes">
                            <div class="col-12">
                                <div class="invoice p-3 mb-3">
                                    <div class="row">
                                        <div class="col-12">
                                            <h5>Datos del Ajuste de Inventario</h5>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="table-responsive col-md-12">
                                            <table class="table table-striped">
                                                <tbody>
                                                    <tr>
                                                        <td class="bg-gray-light"><b>Folio:</b></td>
                                                        <td class="bg-gray-light">{{ajustes.numero_folio_format}}</td>
                                                        <td class="bg-gray-light"><b>Tipo:</b></td>
                                                        <td class="bg-gray-light">{{ajustes.tipo}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="bg-gray-light"><b>Almacén:</b></td>
                                                        <td class="bg-gray-light">{{ajustes.almacen.descripcion}}</td>
                                                        <td class="bg-gray-light"><b>Tipo de Almacén:</b></td>
                                                        <td class="bg-gray-light">{{ajustes.almacen.tipo}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="bg-gray-light"><b>Referencia:</b></td>
                                                        <td class="bg-gray-light">{{ajustes.referencia}}</td>
                                                        <td class="bg-gray-light"><b>Fecha:</b></td>
                                                        <td class="bg-gray-light">{{ajustes.fecha_format}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="bg-gray-light"><b>Estado:</b></td>
                                                        <td class="bg-gray-light">{{ajustes.estado_format}}</td>
                                                        <td class="bg-gray-light"><b>Registró:</b></td>
                                                        <td class="bg-gray-light" v-if="ajustes.usuario">{{ajustes.usuario.nombre}}</td>
                                                        <td class="bg-gray-light" v-else></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
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
                                                        <th>#</th>
                                                        <th>No. de Parte</th>
                                                        <th>Material</th>
                                                        <th>Unidad</th>
                                                        <th>Cantidad</th>
                                                        <th>Importe</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(doc, i) in ajustes.partidas.data">
                                                        <td>{{i+1}}</td>
                                                        <td>{{doc.material.numero_parte}}</td>
                                                        <td v-if="doc.material">{{doc.material.descripcion}}</td>
                                                        <td>{{doc.material.unidad}}</td>
                                                        <td class="td_money">{{doc.cantidad}}</td>
                                                        <td class="td_money">{{parseFloat(doc.importe).formatMoney(2,'.',',')}}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <h6><b>Observaciones:</b></h6>
                                        </div>
                                        <div class="col-sm-10">
                                           <h6>{{ajustes.observaciones}}</h6>
                                        </div>
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
        name: "ajuste-inventario-show",
        props: ['id' , 'pagina', 'tipo'],
        data() {
            return {
                motivo: '',
                ajustes: ''
            }
        },
        methods: {
            find() {
                if(this.tipo == 0) {
                   this.getPositivo();
                }
                if(this.tipo == 1){
                    this.getNegativo();
                }
                if(this.tipo == 2){
                    this.getNuevoLote();
                }
            },
            getPositivo(){
                this.$store.commit('almacenes/ajuste-positivo/SET_AJUSTE', null);
                return this.$store.dispatch('almacenes/ajuste-positivo/find', {
                    id: this.id,
                    params: {include: ['almacen', 'partidas.material', 'usuario']}
                }).then(data => {
                    this.$store.commit('almacenes/ajuste-positivo/SET_AJUSTE', data);
                    this.ajustes = data;
                    $(this.$refs.modal).modal('show');
                })
            },
            getNegativo(){
                this.$store.commit('almacenes/ajuste-negativo/SET_AJUSTE', null);
                return this.$store.dispatch('almacenes/ajuste-negativo/find', {
                    id: this.id,
                    params: {include: ['almacen', 'partidas.material', 'usuario']}
                }).then(data => {
                    this.$store.commit('almacenes/ajuste-negativo/SET_AJUSTE', data);
                    this.ajustes = data;
                    $(this.$refs.modal).modal('show');
                })
            },
            getNuevoLote(){
                this.$store.commit('almacenes/nuevo-lote/SET_AJUSTE', null);
                return this.$store.dispatch('almacenes/nuevo-lote/find', {
                    id: this.id,
                    params: {include: ['almacen', 'partidas.material', 'usuario']}
                }).then(data => {
                    this.$store.commit('almacenes/nuevo-lote/SET_AJUSTE', data);
                    this.ajustes = data;
                    $(this.$refs.modal).modal('show');
                })
            }
        }
    }
</script>

<style scoped>

</style>