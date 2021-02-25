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
                                                :disableBranchNodes="true"
                                            />
                                            <div class="error-label" v-show="errors.has('id_concepto')">{{ errors.first('id_concepto') }}</div>
                                        </div>
                                    </div>
                                 </div>
                                 <hr />
                                 <div class="row">
                                    <div  class="col-md-12 table-responsive-xl">
                                        <div>
                                            <table class="table table-bordered">
                                                <thead>
                                                <tr>
                                                    <th class="index_corto">#</th>
                                                    <th>Concepto</th>
                                                    <th class="cantidad_input">Cantidad</th>
                                                    <th class="unidad">Monto</th>
                                                    <th>Destino</th>
                                                    <th class="icono"></th>
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
                                                    <td v-if="partida.material === ''">
                                                    </td>
                                                    <td v-else>{{partida.material.numero_parte}}</td>
                                                    <td v-if="partida.material === ''">
                                                        <MaterialSelect
                                                            :name="`material[${i}]`"
                                                            :scope="scope"
                                                            v-model="partida.material"
                                                            data-vv-as="Material"
                                                            v-validate="{required: true}"
                                                            :placeholder="!cargando?'Seleccionar o buscar material por descripcion':'Cargando...'"
                                                            :class="{'is-invalid': errors.has(`material[${i}]`)}"
                                                            ref="MaterialSelect"
                                                            :disableBranchNodes="false"/>
                                                        <div class="invalid-feedback" v-show="errors.has(`material[${i}]`)">{{ errors.first(`material[${i}]`) }}</div>
                                                    </td>
                                                    <td v-else>{{partida.material.descripcion}}</td>
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
                                                    <td style="width:120px;" v-if="partida.material">{{partida.material.unidad}}</td>
                                                    <td style="width:120px;" v-else></td>
                                                    <td class="fecha" v-if="materiales.length != 0">
                                                        <datepicker v-model="partida.fecha"
                                                                    :name="`fecha[${i}]`"
                                                                    :format = "formatoFecha"
                                                                    :language = "es"
                                                                    :bootstrap-styling = "true"
                                                                    class = "form-control"
                                                                    v-validate="{required: true}"
                                                                    :class="{'is-invalid': errors.has(`fecha[${i}]`)}"
                                                        ></datepicker>
                                                        <div class="invalid-feedback" v-show="errors.has(`fecha[${i}]`)">{{ errors.first(`fecha[${i}]`) }}</div>
                                                    </td>
                                                    <td class="fecha" v-else></td>
                                                    <td  v-if="partida.destino ===  ''" >
                                                         <small class="badge badge-secondary">
                                                            <i class="fa fa-sign-in button" aria-hidden="true" v-on:click="modalDestino(i)" ></i>
                                                        </small>
                                                    </td>
                                                    <td v-else>
                                                        <small class="badge badge-success" v-if="partida.destino.tipo_destino === 1" >
                                                            <i class="fa fa-stream button" aria-hidden="true" v-on:click="modalDestino(i)" ></i>
                                                        </small>
                                                        <small class="badge badge-success" v-else="partida.destino.tipo_destino === 2" >
                                                            <i class="fa fa-boxes button" aria-hidden="true" v-on:click="modalDestino(i)" ></i>
                                                        </small>
                                                        <span v-if="partida.destino.tipo_destino === 1" style="text-decoration: underline"  :title="partida.destino.destino.path">{{partida.destino.destino.descripcion}}</span>
                                                        <span v-if="partida.destino.tipo_destino === 2">{{partida.destino.destino.descripcion}}</span>
                                                    </td>
                                                    <td class="icono">
                                                        <i class="far fa-copy button" v-on:click="copiar_destino(partida)" title="Copiar" ></i>
                                                        <i class="fas fa-paste button" v-on:click="pegar_destino(partida)" title="Pegar"></i>
                                                    </td>
                                                    <td style="width:150px;">
                                                        <textarea class="form-control"
                                                                  :name="`observaciones[${i}]`"
                                                                  data-vv-as="Observaciones"
                                                                  v-validate="{}"
                                                                  :class="{'is-invalid': errors.has(`observaciones[${i}]`)}"
                                                                  v-model="partida.observaciones"/>
                                                        <div class="invalid-feedback" v-show="errors.has(`observaciones[${i}]`)">{{ errors.first(`observaciones[${i}]`) }}</div>
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
                                    <div class="col-md-12">
                                        <label for="observaciones" class="col-form-label">Observaciones: </label>
                                    </div>
                                </div>
                                 <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group row error-content">
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
                                </div>
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
    export default {
        name: "comprobante-fondo-create",
        components: {ModelListSelect, Datepicker, es, ConceptoSelect},
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
                observaciones : ''
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
        }
    }
</script>

<style scoped>

</style>
