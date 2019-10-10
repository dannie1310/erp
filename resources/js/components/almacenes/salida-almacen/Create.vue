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
<!--                                                            <th>Existencia</th>-->
                                                            <th>Cantidad</th>
                                                            <th>Destino</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr v-for="(partida, index) in dato.partidas">
                                                            <td>{{partida[0].numero_parte}}</td>
                                                            <td>{{partida[0].descripcion}}</td>
                                                            <td>{{partida[0].unidad}}</td>
<!--                                                            <td>{{// partida[0].existencia}}</td>-->
                                                            <td>{{partida[1]}}</td>
                                                            <td>{{partida[2].descripcion}}</td>
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
                             <div class="row"  v-if="materiales">
                                 <div class="col-md-4">
                                    <div class="form-group error-content">
                                        <label for="id_materiales">No. de Parte:</label>
                                           <select
                                                   class="form-control"
                                                   name="id_materiales"
                                                   data-vv-as="Material"
                                                   v-model="partida.id_materiales"
                                                   v-validate="{required: true}"
                                                   id="id_materiales"
                                                   :class="{'is-invalid': errors.has('id_materiales')}">
                                            <option value>-- Seleccione --</option>
                                            <option v-for="(material, index) in materiales" :value="material.id_material"
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
                                                   v-validate="{required: true}"
                                                   id="id_materiales"
                                                   :class="{'is-invalid': errors.has('id_materiales')}">
                                            <option value>-- Seleccione --</option>
                                            <option v-for="(material, index) in materiales" :value="material.id_material"
                                                    data-toggle="tooltip" data-placement="left" :title="material.descripcion ">
                                                {{ material.descripcion }}
                                            </option>
                                        </select>
                                         <div class="invalid-feedback" v-show="errors.has('id_materiales')">{{ errors.first('id_materiales') }}</div>
                                    </div>
                                </div>
                            </div>
                             <div class="row"  v-if="materiales">
                                 <div class="col-md-6">
                                    <div class="form-group error-content">
                                        <label for="id_materiales">Unidad:</label>
                                           <select
                                                   class="form-control"
                                                   name="id_materiales"
                                                   data-vv-as="Material"
                                                   v-model="partida.id_materiales"
                                                   v-validate="{required: true}"
                                                   id="id_materiales"
                                                   :class="{'is-invalid': errors.has('id_materiales')}"
                                                   :disabled="true"
                                           >
                                            <option value>-- Unidad --</option>
                                            <option v-for="(material, index) in materiales" :value="material.id_material"
                                                    data-toggle="tooltip" data-placement="left" :title="material.unidad ">
                                                {{ material.unidad }}
                                            </option>
                                            </select>
                                         <div class="invalid-feedback" v-show="errors.has('id_materiales')">{{ errors.first('id_materiales') }}</div>
                                    </div>
                                </div>
                                  <div class="col-md-6">
                                    <div class="form-group error-content">
                                        <label for="id_materiales">Existencia:</label>
                                           <select
                                                   class="form-control"
                                                   name="id_materiales"
                                                   data-vv-as="Material"
                                                   v-model="partida.id_materiales"
                                                   v-validate="{required: true}"
                                                   id="id_materiales"
                                                   :class="{'is-invalid': errors.has('id_materiales')}"
                                                   :disabled="true"
                                           >
                                            <option value>-- Existencia --</option>
                                            <option v-for="(material, index) in materiales" :value="material.id_material"
                                                    data-toggle="tooltip" data-placement="left" :title="material.saldo ">
                                                {{ material.saldo }}
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
                            <div class="row" v-if="almacenes && dato.id_tipo == 65537">
                                <div class="col-md-12">
                                    <div class="form-group error-content">
                                        <label for="id_almacenes">Almacén:</label>
                                           <select
                                                   class="form-control"
                                                   name="id_almacenes"
                                                   data-vv-as="Almacen"
                                                   v-model="partida.destino"
                                                   v-validate="{required: true}"
                                                   id="id_almacenes"
                                                   :class="{'is-invalid': errors.has('id_almacenes')}"
                                           >
                                            <option value>-- Seleccione un Almacén --</option>
                                            <option v-for="(almacen, index) in almacenes" :value="almacen.id"
                                                    data-toggle="tooltip" data-placement="left" :title="almacen.descripcion" @click="validarAlmacen">
                                                {{ almacen.descripcion }}
                                            </option>
                                        </select>
                                         <div class="invalid-feedback" v-show="errors.has('id_almacenes')">{{ errors.first('id_almacenes') }}</div>
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
    import material from "../../../store/modules/cadeco/material";
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
                    65537: "Transferencia"
                },
                partida:{
                    id_materiales:'',
                    cantidad:'',
                    destino:'',
                    id_concepto:'',
                },
                empresas:[],
                almacenes:[],
                materiales:[],
                material:'',
                almacen:'',
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
            findMaterial() {
                this.$store.commit('cadeco/material/SET_MATERIAL', null);
                return this.$store.dispatch('cadeco/material/find', {
                    id: this.partida.id_materiales,
                    params: {}
                }).then(data => {
                    this.material = data;
                })
            },
            findAlmacen() {
                this.$store.commit('cadeco/almacen/SET_ALMACEN', null);
                return this.$store.dispatch('cadeco/almacen/find', {
                    id: this.partida.destino,
                    params: {}
                }).then(data => {
                    this.almacen = data;
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
                        this.store()
                    }
                });
            },
            store() {
                return this.$store.dispatch('almacenes/salida-almacen/store', this.dato)
                    .then((data) => {
                        $(this.$refs.modal).modal('hide');
                        this.$emit('created', data);
                    });
            },
            borrar(){
                this.dato.partidas=[];
            },
            validarAlmacen() {
                if(this.dato.id_almacen == this.partida.id_almacenes){
                    swal('¡Error!', 'No puede seleccionar el mismo almacén en el destino.', 'error');
                    this.partida.id_almacenes='';
                }
            },
            validatePartida() {
                this.findMaterial();
                this.findAlmacen().finally(() => {
                    this.dato.partidas.push([this.material,this.partida.cantidad,this.almacen]);
                });

                $(this.$refs.modal).modal('hide');

            },
        }
    }
</script>
