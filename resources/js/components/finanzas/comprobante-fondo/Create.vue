<template>
    <span>
        <nav>
            <div class="row">
                 <div class="col-12">
                     <div class="invoice p-3 mb-3">
                         <form role="form" @submit.prevent="validate">
                             <div class="modal-body">
                                 <div class="row">
                                     <div class="col-md-8"></div>
                                     <div class="col-md-4">
                                         <div class="form-group error-content">
                                             <label class="col-form-label">Fecha:</label>
                                             <datepicker v-model = "fecha"
                                                        name = "fecha"
                                                        :format = "formatoFecha"
                                                        :language = "es"
                                                        :bootstrap-styling = "true"
                                                        class = "form-control"
                                                        v-validate="{required: true}"
                                                        :disabled-dates="fechasDeshabilitadas"
                                                        :class="{'is-invalid': errors.has('fecha')}"
                                             />
                                             <div class="invalid-feedback" v-show="errors.has('fecha')">{{ errors.first('fecha') }}</div>
                                         </div>
                                     </div>
                                 </div>
                                 <div class="row">
                                     <div class="col-md-7">
                                         <div class="form-group error-content">
                                            <label for="id_fondo">Fondo</label>
                                               <model-list-select
                                                   name="id_fondo"
                                                   placeholder="Seleccionar o buscar por descripción del fondo"
                                                   data-vv-as="Fondo"
                                                   v-model="id_fondo"
                                                   option-value="id"
                                                   v-validate="{required: true}"
                                                   :custom-text="descripcionFondo"
                                                   :list="fondos"/>
                                            <div class="error-label" v-show="errors.has('id_fondo')">{{ errors.first('id_fondo') }}</div>
                                        </div>
                                     </div>
                                     <div class="col-md-5">
                                    <div class="form-group error-content">
                                        <label for="referencia">Referencia</label>
                                        <input
                                            type="text"
                                            name="referencia"
                                            data-vv-as="Referencia"
                                            v-validate="{required: true}"
                                            class="form-control"
                                            id="referencia"
                                            placeholder="Referencia"
                                            v-model="referencia"
                                            :class="{'is-invalid': errors.has('referencia')}">
                                        <div class="error-label" v-show="errors.has('referencia')">{{ errors.first('referencia') }}</div>
                                    </div>
                                </div>
                                 </div>
                                 <div class="row">
                                     <div class="col-md-12">
                                        <div class="form-group error-content">
                                            <label for="id_concepto">Conceptos:</label>
                                            <concepto-select
                                                name="id_concepto"
                                                data-vv-as="Concepto"
                                                id="id_concepto"
                                                v-model="id_concepto"
                                                :error="errors.has('id_concepto')"
                                                ref="conceptoSelect"
                                                :disableBranchNodes="false"
                                            />
                                            <div class="error-label" v-show="errors.has('id_concepto')">{{ errors.first('id_concepto') }}</div>
                                        </div>
                                    </div>
                                 </div>
                                 <hr />
                                 <div class="row" v-if="id_concepto">
                                     <div  class="col-md-12 table-responsive-xl">
                                         <div>
                                             <table class="table table-bordered">
                                                 <thead>
                                                 <tr>
                                                     <th class="index_corto">#</th>
                                                     <th>Concepto</th>
                                                     <th class="cantidad_input">Cantidad</th>
                                                     <th class="cantidad_input">Precio</th>
                                                     <th class="unidad">Monto</th>
                                                     <th>Destino</th>
                                                     <th class="icono">
                                                         <button type="button" class="btn btn-success btn-sm" v-if="cargando"  title="Cargando..." :disabled="cargando">
                                                             <i class="fa fa-spin fa-spinner"></i>
                                                         </button>
                                                         <button type="button" class="btn btn-success btn-sm" @click="addPartidas()" v-else>
                                                             <i class="fa fa-plus"></i>
                                                         </button>
                                                     </th>
                                                 </tr>
                                                 </thead>
                                                 <tbody>
                                                 <tr v-for="(partida, i) in partidas">
                                                     <td style="text-align:center; vertical-align:inherit;">{{i+1}}</td>
                                                     <td>
                                                         <input type="text"
                                                               class="form-control"
                                                               :name="`referencia[${i}]`"
                                                               data-vv-as="Concepto"
                                                               v-validate="{required: true}"
                                                               :class="{'is-invalid': errors.has(`referencia[${i}]`)}"
                                                               v-model="partida.referencia"/>
                                                         <div class="invalid-feedback" v-show="errors.has(`referencia[${i}]`)">{{ errors.first(`referencia[${i}]`) }}</div>
                                                     </td>
                                                     <td>
                                                         <input type="number"
                                                               min="0.01"
                                                               step=".01"
                                                               class="form-control"
                                                               :name="`cantidad[${i}]`"
                                                               data-vv-as="Cantidad"
                                                               v-validate="{required: true}"
                                                               :class="{'is-invalid': errors.has(`cantidad[${i}]`)}"
                                                               v-model="partida.cantidad"/>
                                                         <div class="invalid-feedback" v-show="errors.has(`cantidad[${i}]`)">{{ errors.first(`cantidad[${i}]`) }}</div>
                                                     </td>
                                                     <td>
                                                         <input type="number"
                                                               min="0.01"
                                                               step=".01"
                                                               class="form-control"
                                                               :name="`precio[${i}]`"
                                                               data-vv-as="Precio"
                                                               v-validate="{required: true}"
                                                               :class="{'is-invalid': errors.has(`precio[${i}]`)}"
                                                               v-model="partida.precio"/>
                                                         <div class="invalid-feedback" v-show="errors.has(`precio[${i}]`)">{{ errors.first(`precio[${i}]`) }}</div>
                                                     </td>
                                                     <td>{{partida.monto = partida.precio * partida.cantidad}}</td>
                                                     <td>
                                                        <ConceptoSelectHijo
                                                            name="id_concepto"
                                                            data-vv-as="Concepto"
                                                            id="id_concepto"
                                                            v-model="partida.id_concepto"
                                                            ref="conceptoSelectHijo"
                                                            :disableBranchNodes="true"
                                                            v-bind:nivel_id="id_concepto"
                                                        ></ConceptoSelectHijo>
                                                         <div class="error-label" v-show="errors.has('id_concepto')">{{ errors.first('id_concepto') }}</div>
                                                    </td>
                                                    <td>
                                                        <button  type="button" class="btn btn-outline-danger btn-sm" @click="destroy(i)"><i class="fa fa-trash"></i></button>
                                                    </td>
                                                 </tr>
                                                 </tbody>
                                             </table>
                                         </div>
                                     </div>
                                 </div>
                                 <div class="row">
                                     <div class="col-md-9">
                                         <div class="form-group row error-content">
                                             <label for="observaciones" class="col-form-label">Observaciones: </label>
                                             <textarea
                                                 name="observaciones"
                                                 id="observaciones"
                                                 class="form-control"
                                                 v-model="observaciones"
                                                 v-validate="{required: true}"
                                                 data-vv-as="Observaciones"
                                                 :class="{'is-invalid': errors.has('observaciones')}"
                                             ></textarea>
                                             <div class="invalid-feedback" v-show="errors.has('observaciones')">{{ errors.first('observaciones') }}</div>
                                         </div>
                                     </div>
                                     <div class="col-md-3" align="left">
                                         <div class="table-responsive col-md-12">
                                             <div class="col-md-12">
                                                 <div class="table-responsive">
                                                     <table class="table table-borderless">
                                                         <tbody>
                                                             <tr>
                                                                 <th>Subtotal:</th>
                                                                 <td>$ {{(parseFloat(sumaMontos)).formatMoney(2,'.',',')}}</td>
                                                             </tr>
                                                             <tr>
                                                                 <th>IVA:</th>
                                                                 <td>
                                                                     <input type="number" min="0.01"
                                                                            step=".01"   class="form-control"
                                                                            :name="iva"  data-vv-as="IVA"
                                                                            v-validate="{required: true}"
                                                                            :class="{'is-invalid': errors.has(`iva`)}"
                                                                            v-model="iva"/>
                                                                 </td>
                                                             </tr>
                                                             <tr>
                                                                 <th>Total:</th>
                                                                  <td>$ {{(parseFloat(sumaTotal)).formatMoney(2,'.',',')}}</td>
                                                             </tr>
                                                         </tbody>
                                                     </table>
                                                 </div>
                                             </div>
                                         </div>
                                     </div>
                                 </div>
                             </div>
                             <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                <button type="submit" class="btn btn-primary" :disabled="errors.count() > 0" @click="validate"><i class="fa fa-save"></i> Guardar</button>
                             </div>
                         </form>
                     </div>
                 </div>
            </div>
        </nav>
    </span>
</template>

<script>
    import Datepicker from 'vuejs-datepicker';
    import {es} from 'vuejs-datepicker/dist/locale';
    import {ModelListSelect} from 'vue-search-select';
    import ConceptoSelect from "../../cadeco/concepto/Select";
    import ConceptoSelectHijo from "../../cadeco/concepto/SelectHijo";
    export default {
        name: "comprobante-fondo-create",
        components: {ModelListSelect, Datepicker, es, ConceptoSelect, ConceptoSelectHijo},
        data() {
            return {
                cargando : false,
                es : es,
                fechasDeshabilitadas : {},
                fecha : '',
                fecha_hoy : '',
                id_fondo : '',
                referencia : '',
                id_concepto : '',
                fondos : [],
                observaciones : '',
                concepto_copiado : '',
                partidas: [],
                subtotal : 0,
                iva : 0,
                total : 0
            }
        },
        computed: {
            sumaMontos() {
                let result = 0;
                this.partidas.forEach(function (doc, i) {
                    result += parseFloat(doc.monto);
                })
                this.subtotal = result;
                return result
            },
            sumaTotal() {
                this.total = this.subtotal + this.iva
                return this.total
            }
        },
        mounted() {
            this.$validator.reset()
            this.fecha_hoy = new Date();
            this.fecha = new Date();
            this.fechasDeshabilitadas.from = new Date();
            this.id_fondo = ''
            this.getFondos();
        },
        methods : {
            init() {
                this.fecha = new Date();
                this.cargando = true;
            },
            descripcionFondo (item) {
                return `[${item.descripcion}]`
            },
            formatoFecha(date)
            {
                return moment(date).format('DD/MM/YYYY');
            },
            getFondos() {
                return this.$store.dispatch('cadeco/fondo/index', {
                    config: {
                        params: {
                            sort: 'descripcion',
                            order: 'asc'
                        }
                    }
                }).then(fondos => {
                    this.fondos = fondos.data;
                })
            },
            addPartidas(){
                this.partidas.splice(this.partidas.length + 1, 0, {
                    referencia : "",
                    cantidad : 0,
                    precio : 0,
                    monto : 0,
                    id_concepto : "",
                });
                this.index = this.index+1;
            },
            destroy(index){
                this.partidas.splice(index, 1);
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.store();
                    }
                });
            },
            store() {
                return this.$store.dispatch('finanzas/comprobante-fondo/store', {
                    data: this.data,
                })
                    .then((data) => {
                        $(this.$refs.modal).modal('hide');
                        this.$emit('created', data)
                    });
            },
        },
        watch: {
            subtotal(value){
                if(value){
                    this.iva = (value * 16) / 100
                }
            }
        }
    }
</script>

<style scoped>

</style>
