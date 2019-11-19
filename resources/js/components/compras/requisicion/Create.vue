<template>
    <span>
        <nav>
            <div class="row">
                <div class="col-12">
                    <div class="invoice p-3 mb-3">
                        <form role="form" @submit.prevent="validate">
                            <div class="modal-body">
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
                                <div class="d-flex flex-row-reverse">
                                    <div class="p-2">
                                        <Layout></Layout>
                                        &nbsp;
                                        <button type="button" class="btn btn-primary" @click="addPartidas()"> + Agregar Partida</button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div  class="col-12">
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
                                                </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(partida, i) in partidas">
                                                        <td>{{i+1}}</td>
                                                        <td style="width: 180px;">
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
                                                        <td v-if="partida.material != ''">
                                                            <!--{{partida.material.numero_parte}}-->
                                                        </td>
                                                        <td v-else></td>
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
                                                        <td v-if="partida.material != ''">{{partida.material.unidad}}</td>
                                                        <td v-else></td>
                                                        <td>
                                                            <input type="date"
                                                                   :name="`fecha[${i}]`"
                                                                   class="form-control"
                                                                   data-vv-as="Fecha"
                                                                   v-validate="{required: true}"
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
    import MaterialSelect from "../../cadeco/material/SelectAutocomplete";
    import Layout from "./CargaLayout"
    export default {
        name: "requisicion-create",
        components: {MaterialSelect,Layout},
        data() {
            return {
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
                        aux : '',
                        material : "",
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
            getAreasCompradoras() {
                return this.$store.dispatch('seguridad/compras/ctg-area-compradora/index', {
                    params: {scope: 'asignadas', sort: 'descripcion', order: 'asc'}
                })
                    .then(data => {
                        this.areas_compradoras = data.data;
                        this.disabled = false;
                    })
            },
            getTipos() {
                return this.$store.dispatch('seguridad/compras/ctg-tipo/index', {
                    params: {sort: 'descripcion', order: 'asc'}
                })
                    .then(data => {
                        this.tipos = data.data;
                        this.disabled = false;
                    })
            },
            getAreasSolicitantes() {
                return this.$store.dispatch('seguridad/compras/ctg-area-solicitante/index', {
                    params: {scope: 'asignadas', sort: 'descripcion', order: 'asc'}
                })
                    .then(data => {
                        this.areas_solicitantes = data.data;
                        this.disabled = false;
                    })
            },
            addPartidas(){
                this.partidas.splice(this.index + 1, 0, {});
                this.index = this.index+1;
            },
            salir(){
                this.$router.push({name: 'requisicion'});
            },
        }
    }
</script>

<style scoped>

</style>