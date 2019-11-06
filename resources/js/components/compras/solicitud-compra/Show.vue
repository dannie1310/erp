<template>
    <span>
        <button @click="find()" type="button"class="btn btn-sm btn-outline-secondary" title="Ver Solicitud">
<i class="fa fa-eye"></i>
</button>
<div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-th"></i> INFORMACIÓN DE SOLICITUD DE COMPRA</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div v-if="solicitud">
                    <div class="row mb-3" v-if="solicitud.complemento.folio">
                        <div class="col-md-4">
                              <label>Folio: </label>  {{ solicitud.complemento.folio }}
                        </div>

                        <div class="col-md-4" v-if="solicitud.complemento.fecha_requisicion_origen">
                              <label>Fecha Req. Origen:</label> {{ solicitud.complemento.fecha_requisicion_origen }}
                        </div>

                         <div class="col-md-4" v-if="solicitud.complemento.requisicion_origen">
                            <label>Folio Req. Origen:</label>  {{ solicitud.complemento.requisicion_origen }}
                        </div>
                    </div>

                    <div class="row mb-5" v-if="solicitud">
                          <div class="col-md-4" v-if="solicitud.complemento.area_compradora.descripcion">
                            <label>Dpto. Responsable:  </label> {{ solicitud.complemento.area_compradora.descripcion }}
                          </div>

                          <div class="col-md-4" v-if="solicitud.complemento.tipo.descripcion">
                            <label>Tipo: </label> {{ solicitud.complemento.tipo.descripcion }}
                          </div>

                          <div class="col-md-4" v-if="solicitud.complemento.area_solicitante.descripcion">
                            <label>Área Solicitante: </label> {{ solicitud.complemento.area_solicitante.descripcion }}
                          </div>

                    </div>

                    <div class="row mb-5">
                        <div class="col-md-12 mb-3" v-if="solicitud.complemento.concepto">
                            <label>Concepto:</label>
                            <div>{{ solicitud.complemento.concepto }}</div>
                        </div>

                        <div class="col-md-12" v-if="solicitud.observaciones">
                            <label>Observaciones: </label>
                            <div>{{ solicitud.observaciones }}</div>
                        </div>
                    </div>

                    <!--Partidas-->
                     <div class="row mb-3">
                            <div class="col-12">
                                <h5>
                                    <i class="fa fa-list"></i> Partidas
                                </h5>
                            </div>
                        </div>

                    <div class="row">
                        <table class="table table-striped">
                                     <thead class="thead-dark">
                                                <tr>
                                                    <th>Descripción</th>
                                                    <th>No. de Parte</th>
                                                    <th>Marca</th>
                                                    <th>Modelo</th>
                                                    <th>Cantidad</th>
                                                    <th>Unidad</th>
                                                    <th>Fecha Requerida</th>
                                                    <th>Destino</th>
                                                    <th>Observaciones</th>
                                                </tr>
                                     </thead>

                            <tbody>
                                <tr v-for="(item,i) in solicitud.partidas.data">
                                    <td>{{ item.material.descripcion }}</td>
                                    <td>{{ item.material.numero_parte }}</td>
                                    <td>{{i}}</td>
                                    <td>{{i}}</td>
                                    <td>{{ item.entrega.cantidad }}</td>
                                    <td>{{ item.unidad }}</td>
                                    <td>{{ item.entrega.fecha }}</td>
                                    <td>{{i}}</td>
                                    <td>{{ item.complemento.observaciones }}</td>

                                </tr>

                            </tbody>

                        </table>


                    </div>
                </div>

            </div>
             <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                        </div>

        </div>
    </div>
</div>
</span>
</template>

<script>
    export default {
        name: "solicitud-show",
        props: ['id'],
        data(){
            return{
                bancos: null,
                cargando: false,
            }
        },
        methods: {
            find() {
                this.cargando = true;
                this.$store.commit('compras/solicitud-compra/SET_SOLICITUD', null);
                return this.$store.dispatch('compras/solicitud-compra/find', {
                    id: this.id,
                    params:{
                        include:['complemento', 'complemento.area_compradora', 'complemento.area_solicitante', 'complemento.tipo','partidas.material','partidas.entrega', 'partidas.complemento' ]
                    }
                }).then(data => {
                    this.$store.commit('compras/solicitud-compra/SET_SOLICITUD', data);
                    $(this.$refs.modal).modal('show')
                })
            }
        },
        computed: {
            solicitud() {
                return this.$store.getters['compras/solicitud-compra/currentSolicitud']
            }
        }
    }
</script>

<style scoped>

</style>
