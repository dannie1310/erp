<template>
    <span>
        <nav>
            <div class="card">
                <form role="form" @submit.prevent="validate">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-2">
                                <label for="id_concepto">Concepto: </label>
                            </div>
                            <div class="col-md-10">
                                <concepto-select
                                    name="id_concepto"
                                    data-vv-as="Concepto"
                                    id="id_concepto"
                                    v-model="id_concepto"
                                    :error="errors.has('id_concepto')"
                                    ref="conceptoSelect"
                                    :disableBranchNodes="false"
                                    :placeholder="'Seleccione el concepto'"
                                ></concepto-select>
                                <div style="display:block" class="invalid-feedback" v-show="errors.has('id_concepto')">{{ errors.first('id_concepto') }}</div>
                            </div>
                        </div>
                        <hr />
                        <div class="row" v-if="hijos">
                            <div  class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered " id="tabla-resumen-monedas">
                                        <thead>
                                        <tr>
                                             <th class="index_corto" rowspan="2">#</th>
                                            <th rowspan="2">Clave</th>
                                            <th rowspan="2">Unidad</th>
                                            <th colspan="3">Cantidad</th>
                                            <th rowspan="2">Precio Venta</th>
                                            <th class="cantidad_input" rowspan="2">Monto Avance</th>
                                            <th class="cantidad_input" rowspan="2">Cantidad Actual</th>
                                            <th class="cantidad_input" rowspan="2">Monto Actual</th>
                                            <th rowspan="2">Cumplido</th>
                                        </tr>
                                        <tr>
                                            <th>Presupuesto</th>
                                            <th>Anterior</th>
                                            <th>Avance</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(partida, i) in hijos">
                                                <td style="text-align:center; vertical-align:inherit;">{{i+1}}</td>
                                                <td style="text-align:center;">{{partida.descripcion}}</td>
                                                <td style="text-align:center;">{{partida.unidad}}</td>
                                                <td v-if="partida.concepto_medible == 3">{{partida.cantidad_presupuestada}}</td>
                                                <td v-else></td>
                                                <td v-if="partida.concepto_medible == 3">{{partida.cantidad_presupuestada}}</td>
                                                <td v-else></td>
                                                <td v-if="partida.concepto_medible == 3">
                                                    <input type="text"
                                                           class="form-control"
                                                           value="0.0"
                                                           :name="`avance[${i}]`"
                                                           data-vv-as="Precio"
                                                           v-validate="{required: true, min_value:0, regex: /^[0-9]\d*(\.\d{0,6})?$/}"
                                                           :class="{'is-invalid': errors.has(`avance[${i}]`)}"
                                                           v-model="partida.avance"
                                                           style="text-align: right"
                                                    />
                                                    <div class="invalid-feedback" v-show="errors.has(`avanceavance[${i}]`)">{{ errors.first(`precio[${i}]`) }}</div>
                                                </td>
                                                <td v-else></td>
                                                <td v-if="partida.concepto_medible == 3">{{partida.cantidad_presupuestada}}</td>
                                                <td v-else></td>
                                                <td v-if="partida.concepto_medible == 3">{{partida.cantidad_presupuestada}}</td>
                                                <td v-else></td>
                                                <td v-if="partida.concepto_medible == 3">{{partida.cantidad_presupuestada}}</td>
                                                <td v-else></td>
                                                <td v-if="partida.concepto_medible == 3">{{partida.cantidad_presupuestada}}</td>
                                                <td v-else></td>
                                                <td v-if="partida.concepto_medible == 3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" value="" id="cumplido" :name="`cumplido[${i}]`" v-model="partida.cumplido">
                                                        <label class="form-check-label" for="cumplido">Si</label>
                                                    </div>
                                                </td>
                                                <td v-else></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" v-on:click="salir">
                            <i class="fa fa-angle-left"></i>
                            Regresar</button>
                        <button type="submit" :disabled="id_concepto == ''" class="btn btn-primary">
                            Continuar
                            <i class="fa fa-angle-right"></i>
                        </button>
                    </div>
                </form>
            </div>
        </nav>
    </span>
</template>

<script>
    import {ModelListSelect} from 'vue-search-select';
    import ConceptoSelect from "../../cadeco/concepto/Select";
    export default {
        name: "create-avance-obra",
        components: {ModelListSelect, ConceptoSelect},
        data() {
            return {
                cargando: false,
                id_concepto: '',
                hijos: null
            }
        },
        mounted() {
            this.$validator.reset();
        },
        methods : {
            salir()
            {
                 this.$router.push({name: 'avance-obra'});
            },
            getConceptos() {
                this.hijos = null;
                this.cargando = true;
                return this.$store.dispatch('cadeco/concepto/hijosMedibles', {
                    id: this.id_concepto,
                    params: {
                    }
                })
                .then(data => {
                    this.hijos = data;
                })
                .finally(()=>{
                    this.cargando = false;
                })
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.store()
                    }
                });
            },
            store() {
                return this.$store.dispatch('controlObra/avance-obra/store', this.post).then((data) => {
                    this.salir();
                });
            },
        },
        watch: {
            id_concepto(value)
            {
                if(value !== '' && value !== null && value !== undefined)
                {
                    this.getConceptos();
                }
            },
        }
    }
</script>

<style scoped>
    table#tabla-resumen-monedas, table.tabla {
        word-wrap: unset;
        width: 100%;
        background-color: white;
        border-color: transparent;
        border-collapse: collapse;
        clear: both;
    }
</style>
