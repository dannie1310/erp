<template>
    <span>

        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <label>
                            Indique si el concepto extraordinario pertenecerá al Costo Directo o al Costo Indirecto:
                        </label>
                    </div>
                </div>
                <br />
                <div class="row">
                    <div class="col-md-12">

                        <div class="btn-group btn-group-toggle">
                            <label class="btn btn-outline-secondary" :class="tipo_costo === Number(llave) ? 'active': ''" v-for="(tipo, llave) in tipos_costo" :key="llave">
                                <i :class="llave==1 ?'fa fa-building':'fa fa-boxes'"></i>
                                <input type="radio"
                                       class="btn-group-toggle"
                                       name="id_tipo"
                                       :id="'tipo' + llave"
                                       :value="llave"
                                       autocomplete="on"
                                       v-model.number="tipo_costo">
                                        {{ tipo}}
                            </label>
                        </div>

                    </div>
                </div>

                <br>

                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-sm">
                            <tr >
                                <th class="encabezado" colspan="2">
                                    Descripción
                                </th>
                                <th class="encabezado cantidad_input">
                                    Unidad
                                </th>
                                <th class="encabezado cantidad_input">
                                    Cantidad
                                </th>
                                <th class="encabezado cantidad_input">
                                    Precio Unitario
                                </th>
                                <th class="encabezado cantidad_input" colspan="2">
                                    Monto Presupuestado
                                </th>
                            </tr>
                            <tr>
                                <td style="text-align: center" colspan="2">
                                    <input type="text"
                                           class="form-control"
                                           name="descipcion"
                                           data-vv-as="Descripcion Concepto"
                                           v-model="descripcion"
                                           v-validate="{required: true}"
                                           :class="{'is-invalid': errors.has('descripcion')}"
                                           id="descripcion">
                                    <div class="invalid-feedback" v-show="errors.has('descripcion')">{{ errors.first('descripcion') }}</div>
                                </td>
                                <td style="text-align: center">
                                    <model-list-select
                                        :disabled="cargando"
                                        name="id_unidad"
                                        v-model="unidad"
                                        option-value="unidad"
                                        option-text="unidad"
                                        :list="unidades"
                                        :placeholder="!cargando?'Seleccionar o buscar':'Cargando...'"
                                        :isError="errors.has(`id_unidad`)">
                                    </model-list-select>
                                    <div class="invalid-feedback" v-show="errors.has('id_unidad')">{{ errors.first('id_unidad') }}</div>
                                </td>
                                <td style="text-align: center">
                                    <input type="text"
                                           class="form-control"
                                           name="cantidad"
                                           data-vv-as="Cantidad"
                                           v-model="cantidad"
                                           v-validate="{required: true, min_value:0, regex: /^[0-9]\d*(\.\d+)?$/}"
                                           :class="{'is-invalid': errors.has('cantidad')}"
                                           style="text-align: right"
                                           id="cantidad">
                                    <div class="invalid-feedback" v-show="errors.has('cantidad')">{{ errors.first('cantidad') }}</div>
                                </td>
                                <td style="text-align: right">
                                    ${{precio_unitario.formatMoney(2)}}
                                </td>
                                <td style="text-align: right" colspan="2">
                                    ${{monto_presupuestado.formatMoney(2)}}
                                </td>
                            </tr>

                            <tr>
                                <td style="border:none">&nbsp;</td>
                            </tr>
                            <tr>
                                <td colspan="5" style="border:none"><h6>Materiales</h6></td>
                                <td colspan="2" style="border:none"></td>
                            </tr>

                            <tr >
                                <th class="encabezado icono">
                                    #
                                </th>
                                <th class="encabezado">
                                    Descripción
                                </th>
                                <th class="encabezado cantidad_input">
                                    Unidad
                                </th>
                                <th class="encabezado cantidad_input">
                                    Cantidad
                                </th>
                                <th class="encabezado cantidad_input">
                                    Precio Unitario
                                </th>
                                <th class="encabezado cantidad_input">
                                    Importe
                                </th>
                                <th class="encabezado icono">
                                    <button type="button" class="btn btn-success btn-sm"   :title="cargando?'Cargando...':'Agregar Partidas'" :disabled="cargando" @click="addPartidaMaterial()">
                                        <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                                        <i class="fa fa-plus" v-else></i>
                                    </button>
                                </th>
                            </tr>
                            <tr v-for="(partida_material, i) in partidas_material">
                                <td style="text-align: center">
                                    {{i+1}}
                                </td>
                                <td >
                                    <span v-if="partida_material.material === ''">
                                        <MaterialSelect
                                            :name="`material[${i}]`"
                                            :scope="scopeMaterial"
                                            sort = "descripcion"
                                            v-model="partida_material.material"
                                            data-vv-as="Material"
                                            v-validate="{required: true}"
                                            :placeholder="!cargando?'Seleccionar o buscar material por descripcion':'Cargando...'"
                                            :class="{'is-invalid': errors.has(`material[${i}]`)}"
                                            ref="MaterialSelect"
                                            :disableBranchNodes="false"/>
                                        <div class="invalid-feedback" v-show="errors.has(`material[${i}]`)">{{ errors.first(`material[${i}]`) }}</div>
                                    </span>
                                    <span v-else>
                                        {{partida_material.material.descripcion}}
                                    </span>
                                </td>
                                <td >
                                    {{partida_material.material.unidad}}
                                </td>
                                <td >
                                    <input type="text"
                                           v-on:keyup="calcular"
                                           class="form-control"
                                           :name="`cantidad_material[${i}]`"
                                           :data-vv-as="`Cantidad Material ${i+1}`"
                                           v-model="partida_material.cantidad"
                                           v-validate="{required: true, min_value:0, regex: /^[0-9]\d*(\.\d+)?$/}"
                                           :class="{'is-invalid': errors.has(`cantidad_material[${i}]`)}"
                                           :id="`cantidad_material[${i}]`"
                                           style="text-align: right"
                                    >
                                    <div class="invalid-feedback" v-show="errors.has(`cantidad_material[${i}]`)">{{ errors.first(`cantidad_material[${i}]`) }}</div>
                                </td>
                                <td >
                                    <input type="text"
                                           v-on:keyup="calcular"
                                           class="form-control"
                                           :name="`precio_unitario[${i}]`"
                                           :data-vv-as="`Precio Unitario ${i+1}`"
                                           v-model="partida_material.precio_unitario"
                                           v-validate="{required: true, min_value:0, regex: /^[0-9]\d*(\.\d+)?$/}"
                                           :class="{'is-invalid': errors.has(`precio_unitario[${i}]`)}"
                                           :id="`precio_unitario[${i}]`"
                                           style="text-align: right"
                                    >
                                    <div class="invalid-feedback" v-show="errors.has(`precio_unitario[${i}]`)">{{ errors.first(`precio_unitario[${i}]`) }}</div>

                                </td>
                                <td style="text-align: right">
                                    ${{partida_material.importe.formatMoney(2)}}
                                </td>
                                <td >
                                    <button  type="button" class="btn btn-outline-danger btn-sm" @click="eliminaPartidaMaterial(i)"  ><i class="fa fa-trash"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="5" style="text-align: right; border: none">Suma de Partidas de Material:</td>
                                <td style="text-align: right; border: none">${{suma_partidas_material.formatMoney(2)}}</td>
                                <td style="border: none"></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <br>

                <span v-if="tipo_costo != ''">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group row error-content">
                                <label for="motivo" class="col-sm-2 col-form-label">Motivo: </label>
                                <textarea
                                    name="motivo"
                                    id="motivo"
                                    class="form-control"
                                    data-vv-as="Motivo"
                                    v-model="motivo"
                                    :class="{'is-invalid': errors.has('motivo')}"
                                ></textarea>
                                <div class="invalid-feedback" v-show="errors.has('motivo')">{{ errors.first('motivo') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group row error-content">
                                <label for="area_solicitante" class="col-form-label">Área Solicitante: </label>
                                <input type="text"
                                       class="form-control"
                                       name="area_solicitante"
                                       data-vv-as="Area Solicitante"
                                       v-model="area_solicitante"
                                       v-validate="{required: true}"
                                       :class="{'is-invalid': errors.has('area_solicitante')}"
                                       id="area_solicitante">
                                <div class="invalid-feedback" v-show="errors.has('area_solicitante')">{{ errors.first('area_solicitante') }}</div>
                            </div>
                        </div>
                    </div>
                </span>


            </div>
            <div class="card-footer">
                <button class="btn btn-primary float-right" type="button" @click="validate"  :disabled="errors.count() > 0">
                    <i class="fa fa-save"></i>
                    Guardar
                </button>
            </div>
        </div>

    </span>
</template>

<script>
import {ModelListSelect} from 'vue-search-select';
import CreateConceptoExtaordinario from "../../contratos/solicitud-cambio/partials/CreateConceptoExtaordinario";
import ExtraordinarioDirectoCreate from "./partials/CreateDirecto";
import MaterialSelect from "../../cadeco/material/SelectAutocomplete";
export default {
    name: "variacion-volumen-create",
    components: {MaterialSelect, ExtraordinarioDirectoCreate, CreateConceptoExtaordinario, ModelListSelect},
    props: [],
    data() {
        return {
            scopeMaterial : ['materialesParaCompras'],
            descripcion :'',
            unidad : '',
            unidades : [],
            cantidad : 0,
            cargando: false,
            motivo:'',
            area_solicitante:'',
            concepto:null,
            suma_importe_cambio : 0,
            tipo_costo : '',
            tipos_costo: {
                2: "Costo Indirecto",
                1: "Costo Directo"
            },
            suma_partidas_material : 0,
            suma_partidas_mo : 0,
            suma_partidas_he : 0,
            suma_partidas_maq : 0,
            suma_partidas_sub : 0,
            suma_partidas_gas : 0,
            partidas_material: [
                {
                    i : 0,
                    material : "",
                    unidad : "",
                    numero_parte : "",
                    descripcion : "",
                    cantidad : "",
                    importe : 0,
                    precio_unitario : 0,
                }
            ],
        }
    },
    methods: {
        getUnidades() {
            this.cargando = true;
            return this.$store.dispatch('cadeco/unidad/index',{
                params: {sort: 'unidad',  order: 'asc'}
            })
                .then(data => {
                    this.unidades = data.data;
                }).finally(() => {
                    this.cargando = false;
                })
        },
        eliminaPartidaMaterial(i){
            this.partidas_material.splice(i, 1);
            this.calcular();
        },
        validate() {
            this.$validator.validate().then(result => {
                if (result) {
                    this.store()
                }
            });
        },
        store() {
            this.cargando = true;

            var datos_solicitud_cambio = {
                'motivo' : this.motivo,
                'area_solicitante' : this.area_solicitante,
                'concepto' : this.concepto
            }

            return this.$store.dispatch('control-presupuesto/variacion-volumen/store', datos_solicitud_cambio)
            .then(data => {
                this.$router.push({name: 'variacion-volumen'});
            })
            .finally(() => {
                this.cargando = false;
            })
        },
        calcular() {
            let _self = this;
            this.suma_partidas_material = 0;
            this.partidas_material.forEach(function (partida, i) {
                let cantidad = 0;
                let precio_unitario = 0;
                if(isNaN(parseFloat(partida.cantidad)))
                {
                    cantidad = 0;
                }else{
                    cantidad = partida.cantidad;
                }

                if(isNaN(parseFloat(partida.precio_unitario)))
                {
                    precio_unitario = 0;
                }else{
                    precio_unitario = partida.precio_unitario;
                }

                partida.importe = parseFloat(cantidad) * parseFloat(precio_unitario);
                _self.suma_partidas_material += parseFloat(partida.importe);
            });
        },
        addPartidaMaterial(){
            this.partidas_material.splice(this.partidas_material.length + 1, 0, {
                i : 0,
                material : "",
                unidad : "",
                numero_parte : "",
                descripcion : "",
                cantidad : "",
                importe : 0,
                precio_unitario : 0,
            });
            this.index = this.index+1;
        }
    },
    computed: {
        precio_unitario() {
            return this.suma_partidas_material +
                this.suma_partidas_mo +
                this.suma_partidas_he +
                this.suma_partidas_maq +
                this.suma_partidas_sub +
                this.suma_partidas_gas;
        },
        monto_presupuestado() {
            return this.precio_unitario * this.cantidad;
        },
    },
    mounted() {
        this.getUnidades();
    },
}
</script>

<style scoped>
.encabezado{
    text-align: center; background-color: #f2f4f5
}

td, th{
    border: 1px #dee2e6 solid;
}

</style>
