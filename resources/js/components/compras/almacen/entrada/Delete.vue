<template>
    <span>
         <button @click="find" type="button" class="btn btn-sm btn-outline-danger " title="Eliminar">
             <i class="fa fa-trash"></i>
         </button>
        <div class="modal fade" ref="modal" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-trash"></i> ELIMINAR ENTRADA A ALMACÉN</h5>
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
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr v-for="(doc, i) in entrada.partidas.data">
                                                            <td>{{i+1}}</td>
                                                            <td>{{doc.material.numero_parte}}</td>
                                                            <td v-if="doc.material">{{doc.material.descripcion}}</td>
                                                            <td class="text-danger"  v-else>No se encuentra ningun material asignado</td>
                                                            <td>{{doc.unidad}}</td>
                                                            <td>{{doc.cantidad}}</td>
                                                            <td v-if="doc.almacen">{{doc.almacen.descripcion}}</td>
                                                            <td v-else-if="doc.concepto" :title="`${doc.concepto.path}`"><u>{{doc.concepto.descripcion}}</u></td>
                                                            <td class="text-danger"  v-else>No se encuentra ningun almacén asignado</td>
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
                                                        <div class="invalid-feedback" v-show="errors.has('motivo')">{{ errors.first('motivo') }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-danger" :disabled="errors.count() > 0 || motivo == ''">Eliminar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "entrada-almacen-delete",
        props: ['id' , 'pagina'],
        data() {
            return {
                motivo: '',
                partidas: ''
            }
        },
        methods: {
            find(){
                this.motivo = '';
                this.partidas = '';
                this.$store.commit('compras/entrada-almacen/SET_ENTRADA', null);
                return this.$store.dispatch('compras/entrada-almacen/find', {
                    id: this.id,
                    params: { include: ['orden_compra', 'empresa', 'partidas', 'partidas.almacen', 'partidas.material', 'partidas.inventario', 'partidas.concepto', 'partidas.movimiento'] }
                }).then(data => {
                    this.$store.commit('compras/entrada-almacen/SET_ENTRADA', data);
                    this.partidas = this.entrada.partidas.data;
                    $(this.$refs.modal).modal('show');
                })
            },
            eliminar() {
                this.cargando = true;
                return this.$store.dispatch('compras/entrada-almacen/eliminar', {
                    id: this.id,
                    params: {data: [this.$data.motivo]}
                })
                    .then(data => {
                        this.$store.commit('compras/entrada-almacen/DELETE_ENTRADA', {id: this.id})
                        $(this.$refs.modal).modal('hide');
                        this.$store.dispatch('compras/entrada-almacen/paginate', {
                            params: {
                                include: 'empresa', sort: 'numero_folio', order: 'desc', limit:10, offset:this.pagina
                            }
                        })
                            .then(data => {
                                this.$store.commit('compras/entrada-almacen/SET_ENTRADAS', data.data);
                                this.$store.commit('compras/entrada-almacen/SET_META', data.meta);
                            })
                    })
                    .finally( ()=>{
                        this.cargando = false;
                    });
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        if(this.motivo == '') {
                            swal('¡Error!', 'Debe colocar un motivo para realizar la operación.', 'error')
                        }
                        else {
                            this.eliminar()
                        }
                    }
                });
            },
        },
        computed: {
            entrada() {
                return this.$store.getters['compras/entrada-almacen/currentEntrada'];
            }
        }
    }
</script>

<style scoped>

</style>