<template>
    <span>
        <nav>
            <div class="row">
                <div class="col-md-12" >
                    <div class="invoice p-3 mb-3">
                     <form role="form" @submit.prevent="validate">
                        <div class="body">
                             <div class="row">
                                <!--Referencia-->
                                 <div class="col-md-6">
                                    <div class="form-group error-content">
                                        <label for="referencia">Referencia:</label>
                                        <div class="col-sm-10">
                                               <input class="form-control"
                                                      style="width: 100%"
                                                      placeholder="Referencia"
                                                      name="referencia"
                                                      id="referencia"
                                                      data-vv-as="Referencia"
                                                      v-validate="{required: true}"
                                                      v-model="dato.referencia"
                                                      :class="{'is-invalid': errors.has('referencia')}"
                                               >
                                          <div class="invalid-feedback" v-show="errors.has('referencia')">{{ errors.first('referencia') }}</div>
                                        </div>
                                    </div>
                                 </div>
                                <!--Almacen-->
                                 <div class="col-md-6">
                                    <div class="form-group error-content">
                                        <label for="id_almacen">Almacen:</label>
                                               <Almacen
                                                   class="form-control"
                                                   name="id_almacen"
                                                   id="id_almacen"
                                                   data-vv-as="Almacén"
                                                   v-validate="{required: true}"
                                                   v-model="dato.id_almacen"
                                                   :class="{'is-invalid': errors.has('id_almacen')}"
                                               ></Almacen>
                                          <div class="invalid-feedback" v-show="errors.has('id_almacen')">{{ errors.first('id_almacen') }}</div>
                                    </div>
                                 </div>

                                <div class="col-md-12" v-if="empresas">
                                    <div class="form-group error-content">
                                        <label for="id_empresa">Contratista/Destajista solicitante del material:</label>
                                           <select
                                               class="form-control"
                                               name="id_empresa"
                                               data-vv-as="Empresa"
                                               v-model="dato.id_empresa"
                                               v-validate="{required: true}"
                                               id="id_empresa"
                                               :class="{'is-invalid': errors.has('id_empresa')}">
                                            <option value>-- Seleccione una Empresa --</option>
                                            <option v-for="(empresa, index) in empresas" :value="empresa.id"
                                                    data-toggle="tooltip" data-placement="left" :title="empresa.razon_social ">
                                                {{ empresa.razon_social }}
                                            </option>
                                        </select>
                                         <div class="invalid-feedback" v-show="errors.has('id_empresa')">{{ errors.first('id_empresa') }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                 <div class="col-md-6">
                                    <div class="form-group row error-content">
                                        <label for="id_tipo" class="col-sm-3 col-form-label">Tipo: </label>
                                        <div class="col-sm-10">
                                            <div class="btn-group btn-group-toggle">
                                                <label class="btn btn-outline-secondary" :class="dato.id_tipo === Number(key) ? 'active': ''" v-for="(tipo, key) in tipos" :key="key">
                                                    <input type="radio"
                                                       class="btn-group-toggle"
                                                       name="id_tipo"
                                                       :id="'id_tipo' + key"
                                                       :value="key"
                                                       autocomplete="on"
                                                       @click="borrar"
                                                       v-model.number="dato.id_tipo">
                                                        {{ tipo }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12" v-if="dato.id_tipo == 1">
                                    <div class="form-group row error-content">
                                        <div class="form-group row error-content">
                                        <label for="id_concepto" class="col-sm-2 col-form-label">Concepto:</label>
                                            <div class="col-sm-10">
                                                <concepto-select
                                                    name="id_concepto"
                                                    data-vv-as="Concepto"
                                                    v-validate="{required: true}"
                                                    id="id_concepto"
                                                    v-model="dato.id_concepto"
                                                    :error="errors.has('id_concepto')"
                                                    ref="conceptoSelect"
                                                    :disableBranchNodes="false"
                                                ></concepto-select>
                                            <div class="error-label" v-show="errors.has('id_concepto')">{{ errors.first('id_concepto') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12" v-if="dato.id_almacen && dato.id_tipo">
                                    <div class="form-group">
                                        <div class="col-12">
                                            <button @click="agregar_partida"class="btn btn-app btn-info pull-right" :disabled="cargando">
                                                <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                                                <i class="fa fa-plus" v-else></i>
                                                Agregar Partida
                                            </button>
                                        </div>
                                        <div v-if="dato.partidas.length > 0">
                                             <div class="table-responsive">
                                                <table class="table table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>No. de Parte</th>
                                                            <th>Material</th>
                                                            <th>Unidad</th>
                                                            <th>Existencia</th>
                                                            <th>Cantidad</th>
                                                            <th>Destino</th>
                                                            <th></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr v-for="(partida, index) in dato.partidas">
                                                            <td>select no de parte</td>
                                                            <td>select material</td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                             </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group error-content">
                                        <label for="observaciones">Observaciones:</label>
                                        <textarea
                                                name="observaciones"
                                                id="observaciones"
                                                class="form-control"
                                                v-model="dato.observaciones"
                                                v-validate="{required: true}"
                                                data-vv-as="Observaciones"
                                                :class="{'is-invalid': errors.has('observaciones')}"
                                        ></textarea>
                                        <div class="invalid-feedback" v-show="errors.has('observaciones')">{{ errors.first('observaciones') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                         <div class="footer">
                           <button type="button" class="btn btn-secondary">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </div>
                     </form>
                    </div>
                </div>
            </div>
        </nav>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-th"></i> AGREGAR PARTIDA</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                     <form role="form" @submit.prevent="validatePartida">
                        <div class="modal-body">
                             <div class="row">
                                 <div class="col-md-4">
                                    <div class="form-group error-content">
                                        <label for="id_materiales">No. de Parte:</label>
                                           <select
                                                   class="form-control"
                                                   name="id_materiales"
                                                   data-vv-as="Material"
                                                   v-model="partida.id_materiales"
                                                   v-validate="{required: false}"
                                                   id="id_materiales"
                                                   :class="{'is-invalid': errors.has('id_materiales')}">
                                            <option value>-- Seleccione --</option>
                                            <option v-for="(material, index) in materiales" :value="material.id"
                                                    data-toggle="tooltip" data-placement="left" :title="material.numero_parte ">
                                                {{ material.numero_parte }}
                                            </option>
                                            </select>
                                         <div class="invalid-feedback" v-show="errors.has('id_materiales')">{{ errors.first('id_materiales') }}</div>
                                    </div>
                                </div>
                                  <div class="col-md-8">
                                    <div class="form-group error-content">
                                        <label for="id_materiales">Material:</label>
                                           <select
                                                   class="form-control"
                                                   name="id_materiales"
                                                   data-vv-as="Material"
                                                   v-model="partida.id_materiales"
                                                   v-validate="{required: false}"
                                                   id="id_materiales"
                                                   :class="{'is-invalid': errors.has('id_materiales')}">
                                            <option value>-- Seleccione --</option>
                                            <option v-for="(material, index) in materiales" :value="material.id"
                                                    data-toggle="tooltip" data-placement="left" :title="material.descripcion ">
                                                {{ material.descripcion }}
                                            </option>
                                        </select>
                                         <div class="invalid-feedback" v-show="errors.has('id_materiales')">{{ errors.first('id_materiales') }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                        <label for="cantidad">Cantidad:</label>
                                        <input
                                            step="any"
                                            type="number"
                                            name="cantidad"
                                            data-vv-as="Total"
                                            v-validate="{required: true}"
                                            class="form-control"
                                            id="cantidad"
                                            placeholder="Cantidad"
                                            v-model="partida.cantidad"
                                            :class="{'is-invalid': errors.has('cantidad')}">
                                    <div class="invalid-feedback" v-show="errors.has('cantidad')">{{ errors.first('cantidad') }}</div>
                                </div>
                            </div>
                            <div class="row" v-if="almacenes && dato.id_tipo == 2">
                                <div class="col-md-12">
                                    <div class="form-group error-content">
                                        <label for="id_almacenes">Almacén:</label>
                                           <select
                                                   class="form-control"
                                                   name="id_almacenes"
                                                   data-vv-as="Almacen"
                                                   v-model="partida.id_almacenes"
                                                   v-validate="{required: true}"
                                                   id="id_almacenes"
                                                   :class="{'is-invalid': errors.has('id_almacenes')}">
                                            <option value>-- Seleccione un Almacén --</option>
                                            <option v-for="(almacen, index) in almacenes" :value="almacen.id"
                                                    data-toggle="tooltip" data-placement="left" :title="almacen.descripcion ">
                                                {{ almacen.descripcion }}
                                            </option>
                                        </select>
                                         <div class="invalid-feedback" v-show="errors.has('id_almacen')">{{ errors.first('id_almacen') }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="row" v-if="dato.id_tipo == 1">
                                <div class="col-md-12">
                                    <div class="form-group row error-content">
                                    <label for="id_conceptos">Concepto:</label>
                                        <concepto-select
                                                name="id_conceptos"
                                                data-vv-as="Concepto"
                                                v-validate="{required: true}"
                                                id="id_conceptos"
                                                v-model="partida.id_conceptos"
                                                :error="errors.has('id_conceptos')"
                                                ref="conceptoSelect"
                                                :disableBranchNodes="false"
                                        ></concepto-select>
                                    <div class="error-label" v-show="errors.has('id_conceptos')">{{ errors.first('id_conceptos') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                         <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                <button type="submit" class="btn btn-primary" :disabled="errors.count() > 0">Registrar</button>
                        </div>
                     </form>
                </div>
            </div>
          </div>
    </span>
</template>

<script>
    import Almacen from "../../cadeco/almacen/Select";
    import ConceptoSelect from "../../cadeco/concepto/Select";
    export default {
        name: "salida-almacen-create",
        components: {Almacen, ConceptoSelect},
        data() {
            return {
                dato:{
                    id_concepto:'',
                    id_almacen:'',
                    id_empresa:'',
                    id_tipo:'',
                    referencia:'',
                    observaciones:'',
                    partidas:[]
                },
                tipos: {
                    1: "Consumo",
                    2: "Transferencia"
                },
                partida:{
                    id_materiales:'',
                    descripcion:'',
                    unidad:'',
                    existencia:'',
                    cantidad:'',
                    destino:'',
                    id_concepto:'',
                    id_almacenes:''
                },
                empresas:[],
                almacenes:[],
                materiales:[],
                cargando: false

            }
        },
        mounted() {
            this.getEmpresas();
        },
        methods: {
            agregar_partida(){
                this.getMateriales();
                this.getAlmacenes();
                this.cargando = true;
                this.partida.id_materiales = '';
                this.partida.descripcion='';
                this.partida.unidad='';
                this.partida.existencia='';
                this.partida.cantidad='';
                this.partida.destino='';
                this.partida.id_concepto='';
                $(this.$refs.modal).modal('show');
                this.$validator.reset();
                this.cargando = false;
            },
            getEmpresas() {
                return this.$store.dispatch('cadeco/empresa/index', {
                    params: {sort: 'razon_social', order: 'asc', scope:'TipoContratista' }
                })
                    .then(data => {
                        this.empresas = data.data;
                    })
            },
            getMateriales() {
                return this.$store.dispatch('cadeco/material/almacen', {
                    params: {almacen:this.dato.id_almacen}
                })
                    .then(data => {
                        this.materiales = data;
                    })
            },
            getAlmacenes() {
                return this.$store.dispatch('cadeco/almacen/index', {
                    params: {sort: 'descripcion', order: 'asc'}
                })
                    .then(data => {
                        this.almacenes = data.data;

                    })
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        alert('panda');
                    }
                });
            },
            borrar(){
                this.dato.partidas=[];
            },
            validatePartida() {
                this.dato.partidas.push(this.partida);
                $(this.$refs.modal).modal('hide');
            },
        }
    }
</script>
