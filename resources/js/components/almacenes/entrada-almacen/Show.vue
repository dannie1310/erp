<template>
    <span>
         <button @click="find" type="button" class="btn btn-sm btn-outline-secondary" title="Show">
              <i class="fa fa-eye"></i>
         </button>
        <div class="modal fade" ref="modal" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-trash"></i>ENTRADA A ALMACÉN</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row"  v-if="entrada">
                                <div class="col-12">
                                    <div class="invoice p-3 mb-3">
                                        <div class="row">
                                            <div class="col-12">
                                                <h5>Datos de la Entrada Almacén</h5>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="table-responsive col-md-12">
                                                <table class="table table-striped">
                                                    <tbody>
                                                        <tr>
                                                            <td class="bg-gray-light"><b>Folio:</b></td>
                                                            <td class="bg-gray-light">{{entrada.numero_folio_format}}</td>
                                                            <td class="bg-gray-light"><b>Empresa:</b></td>
                                                            <td class="bg-gray-light">{{entrada.empresa.razon_social}}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="bg-gray-light"><b>Orden de Compra:</b></td>
                                                            <td class="bg-gray-light">{{entrada.orden_compra.numero_folio_format}}</td>
                                                            <td class="bg-gray-light"><b>Referencia:</b></td>
                                                            <td class="bg-gray-light">{{entrada.referencia}}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="bg-gray-light"><b>Fecha:</b></td>
                                                            <td class="bg-gray-light">{{entrada.fecha_format}}</td>
                                                            <td class="bg-gray-light"><b>Estado:</b></td>
                                                            <td class="bg-gray-light">{{entrada.estado_format}}</td>
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
                                                            <th>Destino</th>
                                                            <th>Entrega a Contratista</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr v-for="(doc, i) in entrada.partidas.data">
                                                            <td>{{i+1}}</td>
                                                            <td>{{doc.material.numero_parte}}</td>
                                                            <td v-if="doc.material">{{doc.material.descripcion}}</td>
                                                            <td class="text-danger"  v-else>No se encuentra ningun material asignado</td>
                                                            <td>{{doc.unidad}}</td>
                                                            <td>{{doc.cantidad_format}}</td>
                                                            <td v-if="doc.almacen">{{doc.almacen.descripcion}}</td>
                                                            <td v-else-if="doc.concepto" :title="`${doc.concepto.path}`"><u>{{doc.concepto.descripcion}}</u></td>
                                                            <td class="text-danger"  v-else>No se encuentra ningun almacén asignado</td>
                                                            <td v-if="doc.contratista && doc.contratista.con_cargo == 1"><i class="fa fa-user" aria-hidden="true" ></i> {{doc.contratista.empresa.razon_social}} (con cargo)</td>
                                                            <td v-else-if="doc.contratista && doc.contratista.con_cargo == 0"><i class="fa fa-user-o" aria-hidden="true"></i> {{doc.contratista.empresa.razon_social}} (sin cargo)</td>
                                                            <td v-else>-</td>
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
                                               <h6>{{entrada.observaciones}}</h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "entrada-almacen-show",
        props: ['id' , 'pagina'],
        data() {
            return {
                partidas: ''
            }
        },
        methods: {
            find(){
                this.partidas = '';
                this.$store.commit('almacenes/entrada-almacen/SET_ENTRADA', null);
                return this.$store.dispatch('almacenes/entrada-almacen/find', {
                    id: this.id,
                    params: { include: ['orden_compra', 'empresa', 'partidas.contratista', 'partidas.almacen', 'partidas.material', 'partidas.inventario', 'partidas.concepto', 'partidas.movimiento'] }
                }).then(data => {
                    this.$store.commit('almacenes/entrada-almacen/SET_ENTRADA', data);
                    this.partidas = this.entrada.partidas.data;
                    $(this.$refs.modal).modal('show');
                })
            },
        },
        computed: {
            entrada() {
                return this.$store.getters['almacenes/entrada-almacen/currentEntrada'];
            }
        }
    }
</script>

<style scoped>

</style>