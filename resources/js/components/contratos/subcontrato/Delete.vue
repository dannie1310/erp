<template>
    <span>
        <div class="row" v-if="!subcontrato">
            <div class="col-md-12">
                <div class="spinner-border text-success" role="status">
                    <span class="sr-only">Cargando...</span>
                </div>
            </div>
        </div>
        <div class="card" v-else>
            <div class="card">
                <div class="card-body">
                    <div class="card">
                        <div class="card-header">
                            <h5>Subcontrato: <b>{{subcontrato.numero_folio_format}}</b></h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive col-md-12">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <td class="bg-gray-light" align="center" colspan="6"><b>{{subcontrato.empresa}}</b></td>
                                        </tr>
                                        <tr>
                                            <td class="bg-gray-light"><b>Referencia:</b></td>
                                            <td class="bg-gray-light">{{subcontrato.referencia}}</td>
                                            <td class="bg-gray-light"><b>Contrato:</b></td>
                                            <td class="bg-gray-light">{{subcontrato.contrato_folio_format}}</td>
                                            <td class="bg-gray-light"><b>Fecha:</b></td>
                                            <td class="bg-gray-light">{{subcontrato.fecha_format}}</td>
                                        </tr>
                                        <tr>
                                            <td class="bg-gray-light"><b>Tipo de Gasto:</b></td>
                                            <td class="bg-gray-light">{{subcontrato.costo}}</td>
                                            <td class="bg-gray-light"><b>Tipo de Contrato:</b></td>
                                            <td class="bg-gray-light">{{subcontrato.tipo_subcontrato}}</td>
                                            <td class="bg-gray-light"><b>Personalidad Contratista:</b></td>
                                            <td class="bg-gray-light">{{subcontrato.personalidad_contratista}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h6><b>Detalle de las partidas</b></h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive col-md-12">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Clave</th>
                                            <th>Descripción</th>
                                            <th>Unidad</th>
                                            <th>Cantidad</th>
                                            <th>Precio</th>
                                            <th>Importe</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(doc, i) in subcontrato.partidas.data">
                                            <td>{{i+1}}</td>
                                            <td class="td_numero_folio"></td>
                                            <td>{{doc.contratos.descripcion}}</td>
                                            <td align="center">{{doc.contratos.unidad}}</td>
                                            <td class="td_money">{{doc.cantidad_format}}</td>
                                            <td class="td_money">{{doc.precio_unitario_format}}</td>
                                            <td class="td_money">{{doc.importe_total}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class=" col-md-12" align="right">
                                <label class="col-md-3 col-form-label" style="text-align: left">Subtotal Antes Descuento:</label>
                                <label class="col-md-2 col-form-label" style="text-align: right">{{subcontrato.subtotal_antes_descuento}}</label>
                            </div>
                            <div class=" col-md-12" align="right">
                                <label class="col-md-3 col-form-label" style="text-align: left">Descuento(%):</label>
                                <label class="col-md-2 col-form-label" style="text-align: right">{{subcontrato.descuento}}</label>
                            </div>
                            <div class=" col-md-12" align="right">
                                <label class="col-md-3 col-form-label" style="text-align: left">Subtotal:</label>
                                <label class="col-md-2 col-form-label" style="text-align: right">{{subcontrato.subtotal_format}}</label>
                            </div>
                            <div class=" col-md-12" align="right">
                                <label class="col-md-3 col-form-label" style="text-align: left">IVA:</label>
                                <label class="col-md-2 col-form-label" style="text-align: right">{{subcontrato.impuesto_format}}</label>
                            </div>
                            <div class=" col-md-12" align="right">
                                <label class="col-md-3 col-form-label" style="text-align: left">Retención IVA:</label>
                                <label class="col-md-2 col-form-label" style="text-align: right">{{subcontrato.impuesto_retenido}}</label>
                            </div>
                            <div class=" col-md-12" align="right">
                                <label class="col-md-3 col-form-label" style="text-align: left">Total:</label>
                                <label class="col-md-2 col-form-label" style="text-align: right">{{subcontrato.monto_format}}</label>
                            </div>
                            <div class=" col-md-12" align="right">
                                <label class="col-md-3 col-form-label" style="text-align: left">Moneda:</label>
                                <label class="col-md-2 col-form-label" style="text-align: right">{{subcontrato.moneda.nombre}}</label>
                            </div>
                            <div class=" col-md-12" align="right">
                                <label class="col-md-3 col-form-label" style="text-align: left">Anticipo({{subcontrato.anticipo_format}}):</label>
                                <label class="col-md-2 col-form-label" style="text-align: right">{{subcontrato.anticipo_monto_format}}</label>
                            </div>
                            <div class=" col-md-12" align="right">
                                <label class="col-md-3 col-form-label" style="text-align: left">Fondo de Garantia:</label>
                                <label class="col-md-2 col-form-label" style="text-align: right">{{subcontrato.retencion}}</label>
                            </div>
                        </div>
                        <hr>
                        <div class="card-body">
                            <div class="row col-md-12">
                                <div class="col-md-2"><b>Plazo de Ejecución:</b></div>
                                <div class="col-md-4" v-if="subcontrato.subcontratos"><b>Del</b>&nbsp; {{subcontrato.subcontratos.fecha_ini}} &nbsp;<b>Al</b>&nbsp; {{subcontrato.subcontratos.fecha_fin}}</div>
                                <div class="col-md-4" v-else><b>Del</b>&nbsp; -------- &nbsp;<b>Al</b>&nbsp; --------</div>
                                <div class="col-md-2" v-if="subcontrato.subcontratos"><b>Descripción:</b></div>
                                <div class="col-md-4" v-if="subcontrato.subcontratos">{{subcontrato.subcontratos.descripcion}}</div>
                            </div>
                            <br>
                            <div class="row col-md-12">
                                <div class="col-md-2"><b>Observaciones:</b></div>
                                <div class="col-md-10">{{subcontrato.observaciones}}</div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h5>Motivo: </h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group row error-content">
                                <div class="col-md-12">
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
                    <button type="button" class="btn btn-secondary" v-on:click="salir">Cerrar</button>
                    <button type="button" class="btn btn-danger" :disabled="errors.count() > 0 || motivo == ''" v-on:click="validate">Eliminar</button>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "Delete",
        props: ['id'],
        data() {
            return {
                motivo: ''
            }
        },
        mounted() {
            this.find();
        },
        methods: {
            find() {
                this.$store.commit('contratos/subcontrato/SET_SUBCONTRATO', null);
                return this.$store.dispatch('contratos/subcontrato/find', {
                    id: this.id,
                    params: {include: ['partidas', 'moneda', 'partidas.contratos', 'subcontrato']}
                }).then(data => {
                    this.$store.commit('contratos/subcontrato/SET_SUBCONTRATO', data);
                })
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
            eliminar() {
                return this.$store.dispatch('contratos/subcontrato/eliminar', {
                    id: this.id,
                    params: {data: this.$data.motivo}
                })
                    .then(data => {
                        this.salir()
                    })
            },
            salir(){
                this.$router.push({name: 'subcontrato'});
            },
        },
        computed: {
            subcontrato() {
                return this.$store.getters['contratos/subcontrato/currentSubcontrato'];
            }
        },
    }
</script>

<style scoped>

</style>
