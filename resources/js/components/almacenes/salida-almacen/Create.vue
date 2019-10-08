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
                                        <label for="referencia">       Referencia</label>
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
                                        <label for="id_almacen">Almacen</label>
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
                                        <label for="id_concepto" class="col-sm-2 col-form-label">Concepto</label>
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
                                <div class="col-md-12">
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
                    partidas:[]
                },
                tipos: {
                    1: "Consumo",
                    2: "Transferencia"
                },
                partida:{
                    no_parte:'',
                    id_material:'',
                    descripcion:'',
                    unidad:'',
                    existencia:'',
                    cantidad:'',
                    destino:'',
                    id_concepto:''
                },
                empresas:[],
                cargando: false

            }
        },
        mounted() {
            this.getEmpresas()
        },
        methods: {
            agregar_partida(){
                this.dato.partidas.push(this.partida);
            },
            getEmpresas() {
                return this.$store.dispatch('cadeco/empresa/index', {
                    params: {sort: 'razon_social', order: 'asc', scope:'TipoContratista' }
                })
                    .then(data => {
                        this.empresas = data.data;
                    })

            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        alert('panda');
                    }
                });
            },
        }
    }
</script>
