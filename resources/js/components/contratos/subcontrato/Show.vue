<template>
      <span>
         <button @click="find" type="button" class="btn btn-sm btn-outline-secondary" title="Show">
             <i class="fa fa-eye"></i>
         </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-eye"></i> CONSULTA DE SUBCONTRATO</h5>
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
                                            <h5>Subcontrato: <b>#00147</b></h5>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="table-responsive col-md-12">
                                            <table class="table">
                                                <tbody>
                                                    <tr>
                                                        <td class="bg-dark" align="left"><b>Referencia:</b></td>
                                                        <td align="left">005-OS-CH2-PCO-2020-Provisiones</td>
                                                        <td colspan="3"></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="3"></td>
                                                        <td class="bg-dark" align="left"><b>Folio:</b></td>
                                                        <td class="bg-dark" align="right">#00147</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="3"></td>
                                                        <td class="bg-dark" align="left"><b>Fecha:</b></td>
                                                        <td class="bg-dark" align="right">03/02/2020</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="3"></td>
                                                        <td class="bg-dark" align="left" style="width:10%;"><b>Contrato:</b></td>
                                                        <td class="bg-dark" align="right" style="width:10%;">#00146</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="bg-dark" align="center" colspan="5"><h4><b>VERA GUEVARA DAVID</b></h4></td>
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
                                                        <th class="bg-dark">#</th>
                                                        <th class="bg-dark">No. de Parte</th>
                                                        <th class="bg-dark">Material</th>
                                                        <th class="bg-dark">Unidad</th>
                                                        <th class="bg-dark">Cantidad</th>
                                                        <th class="bg-dark">Importe</th>
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
        name: "subcontrato-show",
        props: ['id' , 'show'],
        data() {
            return {
                ajustes: ''
            }
        },
        methods: {
            find() {
                console.log('Show Subcontratos', this.id, this.show);
                this.getNegativo();
                
                // if(this.tipo == 0) {
                //    this.getPositivo();
                // }
                // if(this.tipo == 1){
                //     this.getNegativo();
                // }
                // if(this.tipo == 2){
                //     this.getNuevoLote();
                // }
            },
            getPositivo(){
                this.$store.commit('almacenes/ajuste-positivo/SET_AJUSTE', null);
                return this.$store.dispatch('almacenes/ajuste-positivo/find', {
                    id: this.id,
                    params: {include: ['almacen', 'partidas.material', 'usuario']}
                }).then(data => {
                    this.$store.commit('almacenes/ajuste-positivo/SET_AJUSTE', data);
                    this.ajustes = data;
                    $(this.$refs.modal).appendTo('body')
                    $(this.$refs.modal).modal('show');
                })
            },
            getNegativo(){
                this.$store.commit('almacenes/ajuste-negativo/SET_AJUSTE', null);
                return this.$store.dispatch('almacenes/ajuste-negativo/find', {
                    id: 117401,
                    params: {include: ['almacen', 'partidas.material', 'usuario']}
                }).then(data => {
                    this.$store.commit('almacenes/ajuste-negativo/SET_AJUSTE', data);
                    this.ajustes = data;
                    $(this.$refs.modal).appendTo('body')
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
                    $(this.$refs.modal).appendTo('body')
                    $(this.$refs.modal).modal('show');
                })
            }
        }
    }
</script>

<style scoped>

</style>
