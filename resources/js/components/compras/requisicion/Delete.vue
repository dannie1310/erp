<template>
    <span>
        <button @click="find" type="button" class="btn btn-sm btn-outline-danger" title="Eliminar">
            <i class="fa fa-trash"></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-trash"></i> REQUISICIÓN DE COMPRA</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row"  v-if="requisicion">
                                <div class="col-12">
                                    <div class="invoice p-3 mb-3">
                                        <div class="row">
                                            <div class="col-12">
                                                <h5>Datos de la Requisición</h5>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="table-responsive col-md-12">
                                                <table class="table table-striped">
                                                    <tbody>
                                                        <tr>
                                                            <td colspan="2"><b>Fecha:</b></td>
                                                            <td colspan="2" class="bg-gray-light">{{requisicion.fecha}}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="bg-gray-light"><b>Folio:</b></td>
                                                            <td class="bg-gray-light">{{requisicion.numero_folio_format}}</td>
                                                            <td class="bg-gray-light"><b>Folio Compuesto:</b></td>
                                                            <td class="bg-gray-light" v-if = "requisicion.complemento">{{requisicion.complemento.folio}}</td>
                                                            <td v-else></td>
                                                        </tr>
                                                        <tr  v-if="requisicion.complemento">
                                                            <td class="bg-gray-light"><b>Departamento Responsable:</b></td>
                                                            <td class="bg-gray-light">{{requisicion.complemento.area_compradora.descripcion}}</td>
                                                            <td class="bg-gray-light"><b>Tipo</b></td>
                                                            <td class="bg-gray-light">{{requisicion.complemento.tipo.descripcion}}</td>
                                                        </tr>
                                                        <tr v-if="requisicion.complemento">
                                                            <td class="bg-gray-light"><b>Área Solicitante:</b></td>
                                                            <td class="bg-gray-light">{{requisicion.complemento.area_solicitante.descripcion}}</td>
                                                            <td class="bg-gray-light"><b>Concepto:</b></td>
                                                            <td class="bg-gray-light">{{requisicion.complemento.concepto}}</td>
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
                                        <div class="row" v-if="requisicion.partidas">
                                            <div class="table-responsive col-md-12">
                                                <table class="table table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>No. de Parte</th>
                                                            <th>Material</th>
                                                            <th>Cantidad</th>
                                                            <th>Unidad</th>
                                                            <th>Fecha Entrega</th>
                                                            <th>Observaciones</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr v-for="(doc, i) in requisicion.partidas.data">
                                                            <td>{{i+1}}</td>
                                                            <td v-if="doc.material">{{doc.material.numero_parte}}</td>
                                                            <td v-else-if="doc.complemento">{{doc.complemento.numero_parte}}</td>
                                                            <td v-else></td>
                                                            <td v-if="doc.material">{{doc.material.descripcion}}</td>
                                                            <td v-else-if="doc.complemento">{{doc.complemento.descripcion}}</td>
                                                            <td v-else></td>
                                                            <td>{{doc.cantidad}}</td>
                                                            <td v-if="doc.material">{{doc.material.unidad}}</td>
                                                            <td v-else-if="doc.complemento">{{doc.complemento.unidad}}</td>
                                                            <td v-else></td>
                                                            <td v-if="doc.complemento">{{doc.complemento.fecha_entrega}}</td>
                                                            <td v-if="doc.complemento">{{doc.complemento.observaciones}}</td>
                                                         </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <br />
                                        <div class="row">
                                            <div class="col-md-2">
                                                <h6><b>Observaciones:</b></h6>
                                            </div>
                                            <div class="col-sm-10">
                                               <h6>{{requisicion.observaciones}}</h6>
                                            </div>
                                        </div>
                                        <hr />
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
        name: "requisicion-delete",
        props: ['id'],
        data() {
            return {
                motivo: '',
                requisicion: ''
            }
        },
        methods: {
            find() {
                this.$store.commit('compras/requisicion/SET_REQUISICION', null);
                return this.$store.dispatch('compras/requisicion/find', {
                    id: this.id,
                    params: {include: ['partidas.complemento', 'complemento.area_solicitante', 'partidas.material', 'complemento.tipo', 'complemento.area_compradora']}
                }).then(data => {
                    this.$store.commit('compras/requisicion/SET_REQUISICION', data);
                    this.requisicion = data;
                    $(this.$refs.modal).modal('show');
                })
            },
            eliminar() {
                this.cargando = true;
                return this.$store.dispatch('compras/requisicion/eliminar', {
                    id: this.id,
                    params: {data: [this.$data.motivo]}
                })
                    .then(data => {
                        this.$store.commit('compras/requisicion/DELETE_ENTRADA', {id: this.id})
                        $(this.$refs.modal).modal('hide');
                        this.$store.dispatch('compras/requisicion/paginate', {
                            params: {
                               sort: 'numero_folio', order: 'desc', limit:10, offset:this.pagina
                            }
                        })
                            .then(data => {
                                this.$store.commit('compras/requisicion/SET_ENTRADAS', data.data);
                                this.$store.commit('compras/requisicion/SET_META', data.meta);
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
        }
    }
</script>

<style scoped>

</style>