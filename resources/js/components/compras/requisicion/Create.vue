<template>
    <span>
        <nav>
            <div class="row">
                <div class="col-12">
                    <div class="invoice p-3 mb-3">
                        <form role="form" @submit.prevent="validate">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="offset-md-10 col-md-2">
                                        <div class="form-group error-content">
                                            <label for="fecha" class="col-form-label">Fecha:</label>
                                            <datepicker v-model = "fecha"
                                                    name = "fecha"
                                                    :format = "formatoFecha"
                                                    :language = "es"
                                                    :bootstrap-styling = "true"
                                                    class = "form-control"
                                                    v-validate="{required: true}"
                                                    :disabled-dates="fechasDeshabilitadas"
                                                    :class="{'is-invalid': errors.has('fecha')}"
                                            ></datepicker>
                                            <div class="invalid-feedback" v-show="errors.has('fecha')">{{ errors.first('fecha') }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row justify-content-between">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="id_area_compradora">Departamento Responsable</label>
                                            <select class="form-control"
                                                    name="id_area_compradora"
                                                    data-vv-as="Departamento Responsable"
                                                    v-model="id_area_compradora"
                                                    v-validate="{required: true}"
                                                    :error="errors.has('id_area_compradora')"
                                                    id="id_area_compradora">
                                                  <option value>-- Seleccionar--</option>
                                                  <option v-for="area in areas_compradoras" :value="area.id" >{{ area.descripcion}}</option>
                                            </select>
                                            <div style="display:block" class="invalid-feedback" v-show="errors.has('id_area_compradora')">{{ errors.first('id_area_compradora') }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="id_tipo">Tipo</label>
                                            <select class="form-control"
                                                    data-vv-as="Tipo"
                                                    id="id_tipo"
                                                    name="id_tipo"
                                                    :error="errors.has('id_tipo')"
                                                    v-validate="{required: true}"
                                                    v-model="id_tipo">
                                                  <option value>-- Selecionar --</option>
                                              <option v-for="(tipo, index) in tipos" :value="tipo.id" >{{ tipo.descripcion}}</option>
                                            </select>
                                            <div style="display:block" class="invalid-feedback" v-show="errors.has('id_tipo')">{{ errors.first('id_tipo') }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="id_area_solicitante">Área Solicitante</label>
                                            <select class="form-control"
                                                    id="id_area_solicitante"
                                                    data-vv-as="Área Solicitante"
                                                    name="id_area_solicitante"
                                                    v-model="id_area_solicitante"
                                                    v-validate="{required: true}"
                                                    :error="errors.has('id_area_solicitante')">
                                                    <option value>-- Seleccionar --</option>
                                                    <option v-for="area_s in areas_solicitantes" :value="area_s.id" >{{ area_s.descripcion}}</option>
                                            </select>
                                            <div style="display:block" class="invalid-feedback" v-show="errors.has('id_area_solicitante')">{{ errors.first('id_area_solicitante') }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="concepto" class="col-form-label">Concepto: </label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group row error-content">
                                            <textarea
                                                    name="concepto"
                                                    id="concepto"
                                                    class="form-control"
                                                    v-model="concepto"
                                                    v-validate="{required: true}"
                                                    data-vv-as="Concepto"
                                                    :class="{'is-invalid': errors.has('concepto')}"
                                            ></textarea>
                                            <div class="invalid-feedback" v-show="errors.has('concepto')">{{ errors.first('concepto') }}</div>
                                        </div>
                                    </div>
                                </div>
                                <hr />
                                <div class="d-flex flex-row-reverse">
                                    <div class="p-2">
                                        <Layout></Layout>
                                    </div>
                                </div>
                                <div class="row">
                                    <div  class="col-md-12">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>No. de Parte</th>
                                                    <th>Descripción</th>
                                                    <th>Cantidad</th>
                                                    <th>Unidad</th>
                                                    <th>Fecha Entrega</th>
                                                    <th>Observaciones</th>
                                                    <th>
                                                        <button type="button" class="btn btn-success btn-sm" @click="addPartidas()">
                                                            <i class="fa fa-plus"></i>
                                                        </button>
                                                    </th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(partida, i) in partidas">
                                                        <td v-model="partida.i">{{i+1}}</td>
                                                        <td style="width: 180px;" v-if="partida.descripcion == ''">
                                                            <MaterialSelect
                                                                    scope="insumos"
                                                                    :name="`material[${i}]`"
                                                                    v-model="partida.material"
                                                                    data-vv-as="Material"
                                                                    v-validate="{required: true}"
                                                                    ref="MaterialSelect"
                                                                    :disableBranchNodes="false"
                                                                    :error="errors.has(`material[${i}]`)"/>
                                                            <div class="invalid-feedback" v-show="errors.has(`material[${i}]`)">{{ errors.first(`material[${i}]`) }}</div>
                                                        </td>
                                                        <td v-else></td>
                                                        <td v-if="partida.material === ''">
                                                            <div class="row ">
                                                                <div class="col-md-12">
                                                                    <div class="form-group row error-content">
                                                                        <div class="col-md-12" >
                                                                            <input
                                                                                    type="text"
                                                                                    data-vv-as="Descripción"
                                                                                    v-validate="{required: true}"
                                                                                    class="form-control"
                                                                                    :name="`descripcion[${i}]`"
                                                                                    placeholder="Descripción"
                                                                                    v-model="partida.descripcion"
                                                                                    :class="{'is-invalid': errors.has(`descripcion[${i}]`)}">
                                                                            <div class="invalid-feedback" v-show="errors.has(`descripcion[${i}]`)">{{ errors.first(`descripcion[${i}]`) }}</div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td v-else>{{partida.material.label}}</td>
                                                        <td>
                                                             <input type="number"
                                                                    class="form-control"
                                                                    :name="`cantidad[${i}]`"
                                                                    data-vv-as="Cantidad"
                                                                    v-validate="{required: true}"
                                                                    :class="{'is-invalid': errors.has(`cantidad[${i}]`)}"
                                                                    v-model="partida.cantidad"/>
                                                         <div class="invalid-feedback" v-show="errors.has(`cantidad[${i}]`)">{{ errors.first(`cantidad[${i}]`) }}</div>

                                                        </td>
                                                        <td v-if="partida.material === ''"></td>
                                                        <td v-else>{{partida.material.unidad}}</td>
                                                        <td style="width: 100px;">
                                                            <input type="date"
                                                                   :name="`fecha[${i}]`"
                                                                   class="form-control datepicker"
                                                                   data-vv-as="Fecha"
                                                                   v-validate="{required: true}"
                                                                    :format = "formatoFecha"
                                                                   data-date-end-date="0d"
                                                                   :class="{'is-invalid': errors.has(`fecha[${i}]`)}"
                                                                   v-model="partida.fecha">
                                                            <div class="invalid-feedback" v-show="errors.has(`fecha[${i}]`)">{{ errors.first(`fecha[${i}]`) }}</div>
                                                        </td>
                                                        <td style="width: 160px;">
                                                            <textarea class="form-control"
                                                                      :name="`observaciones[${i}]`"
                                                                      data-vv-as="Observaciones"
                                                                      v-validate="{required: true}"
                                                                      :class="{'is-invalid': errors.has(`observaciones[${i}]`)}"
                                                                      v-model="partida.observaciones"/>
                                                             <div class="invalid-feedback" v-show="errors.has(`observaciones[${i}]`)">{{ errors.first(`observaciones[${i}]`) }}</div>
                                                        </td>
                                                         <td style="text-align:center">
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
                             <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" v-on:click="salir">Cerrar</button>
                                    <button type="submit" class="btn btn-primary" :disabled="errors.count() > 0">Registrar</button>
                             </div>
                        </form>
                    </div>
                </div>
            </div>
        </nav>
    </span>
</template>

<script>
    import MaterialSelect from "../../cadeco/material/SelectAutocomplete"
    import Datepicker from 'vuejs-datepicker';
    import {es} from 'vuejs-datepicker/dist/locale';
    import Layout from "./CargaLayout"
    export default {
        name: "requisicion-create",
        components: {MaterialSelect,Layout,Datepicker},
        data() {
            return {
                cargando: false,
                es:es,
                fechasDeshabilitadas:{},
                fecha : '',
                fecha_hoy : '',
                areas_compradoras : [],
                areas_solicitantes : [],
                tipos : [],
                id_area_compradora : '',
                id_tipo : '',
                id_area_solicitante : '',
                concepto : '',
                observaciones : '',
                index : 0,
                partidas: [
                    {
                        i : '',
                        material : "",
                        descripcion : "",
                        cantidad : "",
                        fecha : "",
                        observaciones : "",
                    }
                ],
            }
        },
        mounted() {
            this.$validator.reset()
            this.getAreasCompradoras();
            this.getAreasSolicitantes();
            this.getTipos();
        },
        methods : {
            init() {
                this.fecha = new Date();
                this.cargando = true;
                this.areas_compradoras = '';
                this.areas_solicitantes = [];
                this.tipos = [];
                this.id_area_compradora = '';
                this.id_tipo = '';
                this.id_area_solicitante = '';
                this.concepto = '';
                this.observaciones = '';
                this.index = 0;
                this.partidas = [{
                        i : '',
                        material : "",
                        descripcion : "",
                        cantidad : "",
                        fecha : "",
                        observaciones : ""
                }];
            },
            formatoFecha(date){
                return moment(date).format('DD/MM/YYYY');
            },
            getAreasCompradoras() {
                this.fecha_hoy = new Date();
                this.fecha = new Date();
                this.fechasDeshabilitadas.from= new Date();
                return this.$store.dispatch('configuracion/area-compradora/index', {
                    params: {scope: 'asignadas', sort: 'descripcion', order: 'asc'}
                })
                    .then(data => {
                        this.areas_compradoras = data;
                        this.disabled = false;
                    })
            },
            getTipos() {
                return this.$store.dispatch('configuracion/ctg-tipo/index', {
                    params: {sort: 'descripcion', order: 'asc'}
                })
                    .then(data => {
                        this.tipos = data.data;
                        this.disabled = false;
                    })
            },
            getAreasSolicitantes() {
                return this.$store.dispatch('configuracion/area-solicitante/index', {
                    params: {scope: 'asignadas', sort: 'descripcion', order: 'asc'}
                })
                    .then(data => {
                        this.areas_solicitantes = data;
                        this.disabled = false;
                    })
            },
            addPartidas(){
                this.partidas.splice(this.index + 1, 0, {
                    i : '',
                    material : "",
                    descripcion : "",
                    cantidad : "",
                    fecha : "",
                    observaciones : "",
                });
                this.index = this.index+1;
            },
            salir(){
                this.$router.push({name: 'requisicion'});
            },
            destroy(index){
                this.partidas.splice(index, 1);
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                            this.store()
                    }
                });
            },
            store() {
                return this.$store.dispatch('compras/requisicion/store', this.$data)
                    .then((data) => {
                        this.$router.push({name: 'requisicion'});
                    });
            },
        }
    }
</script>

<style scoped>

</style>
