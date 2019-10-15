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
                                                   v-model="id_almacen"
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
                                        <label for="opciones" class="col-sm-3 col-form-label">Tipo: </label>
                                        <div class="col-sm-10">
                                            <div class="btn-group btn-group-toggle">
                                                <label class="btn btn-outline-secondary" :class="dato.opciones === Number(key) ? 'active': ''" v-for="(tipo, key) in tipos" :key="key">
                                                    <input type="radio"
                                                       class="btn-group-toggle"
                                                       name="opciones"
                                                       :id="'opciones' + key"
                                                       :value="key"
                                                       autocomplete="on"
                                                       v-validate="{required: true}"
                                                       @click="borrar"
                                                       v-model.number="dato.opciones">
                                                        {{ tipo }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12" v-if="id_almacen && dato.opciones">
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
                                                            <th>Acciones</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr v-for="(partida, index) in dato.partidas">
                                                            <td>{{partida[0].numero_parte}}</td>
                                                            <td>{{partida[0].descripcion}}</td>
                                                            <td>{{partida[0].unidad}}</td>
                                                            <td>{{partida[3][1]}}</td>
                                                            <td>{{partida[1]}}</td>
                                                            <td v-if="partida[2].path" :title="partida[2].path">{{partida[2].descripcion}}</td>
                                                            <td v-else>{{partida[2].descripcion}}</td>
                                                            <td>
                                                                <button type="button" @click="borrarPartidas(index)" class="btn btn-sm btn-outline-danger" title="Eliminar partida">
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            </td>
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
                           <button type="button" class="btn btn-secondary"  @click="index">Cerrar</button>
                            <button type="submit" class="btn btn-primary" :disabled="errors.count() > 0 || dato.partidas > 0">Guardar</button>
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
                                        <label for="partida">No. de Parte:</label>
                                           <select
                                                   class="form-control"
                                                   name="partida"
                                                   data-vv-as="Material"
                                                   v-model="partida"
                                                   v-validate="{required: true}"
                                                   id="partida"
                                                   :class="{'is-invalid': errors.has('partida')}">
                                            <option value>-- Seleccione --</option>
                                            <option v-for="(material, index) in materiales" :value="[material.id_material,material.saldo]"
                                                    data-toggle="tooltip" data-placement="left" :title="material.numero_parte ">
                                                {{ material.numero_parte }}
                                            </option>
                                            </select>
                                         <div class="invalid-feedback" v-show="errors.has('partida')">{{ errors.first('partida') }}</div>
                                    </div>
                                </div>
                                  <div class="col-md-8">
                                    <div class="form-group error-content">
                                        <label for="partida">Material:</label>
                                           <select
                                                   class="form-control"
                                                   name="partida"
                                                   data-vv-as="Material"
                                                   v-model="partida"
                                                   v-validate="{required: true}"
                                                   id="partida"
                                                   :class="{'is-invalid': errors.has('partida')}">
                                            <option value>-- Seleccione --</option>
                                            <option v-for="(material, index) in materiales" :value="[material.id_material,material.saldo]"
                                                    data-toggle="tooltip" data-placement="left" :title="material.descripcion ">
                                                {{ material.descripcion }}
                                            </option>
                                        </select>
                                         <div class="invalid-feedback" v-show="errors.has('partida')">{{ errors.first('partida') }}</div>
                                    </div>
                                </div>
                            </div>
                             <div class="row"  v-if="materiales">
                                 <div class="col-md-6">
                                    <div class="form-group error-content">
                                        <label for="partida">Unidad:</label>
                                           <select
                                                   class="form-control"
                                                   name="partida"
                                                   data-vv-as="Material"
                                                   v-model="partida"
                                                   v-validate="{required: true}"
                                                   id="partida"
                                                   :class="{'is-invalid': errors.has('partida')}"
                                                   :disabled="true"
                                           >
                                            <option value>-- Unidad --</option>
                                            <option v-for="(material, index) in materiales" :value="[material.id_material,material.saldo]"
                                                    data-toggle="tooltip" data-placement="left" :title="material.unidad ">
                                                {{ material.unidad }}
                                            </option>
                                            </select>
                                         <div class="invalid-feedback" v-show="errors.has('partida')">{{ errors.first('partida') }}</div>
                                    </div>
                                </div>
                                  <div class="col-md-6">
                                    <div class="form-group error-content">
                                        <label for="partida">Existencia:</label>
                                           <select
                                                   class="form-control"
                                                   name="partida"
                                                   data-vv-as="Material"
                                                   v-model="partida"
                                                   v-validate="{required: true}"
                                                   id="partida"
                                                   :class="{'is-invalid': errors.has('partida')}"
                                                   :disabled="true"
                                           >
                                            <option value>-- Existencia --</option>
                                            <option v-for="(material, index) in materiales" :value="[material.id_material,material.saldo]"
                                                    data-toggle="tooltip" data-placement="left" :title="material.saldo ">
                                                {{ material.saldo }}
                                            </option>
                                        </select>
                                         <div class="invalid-feedback" v-show="errors.has('partida')">{{ errors.first('partida') }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                        <label for="cantidad">Cantidad:</label>
                                        <input
                                            step="any"
                                            ref="input"
                                            type="number"
                                            name="cantidad"
                                            data-vv-as="Total"
                                            v-validate="{required: true}"
                                            class="form-control"
                                            id="cantidad"
                                            placeholder="Cantidad"
                                            v-model="dato_partida.cantidad"
                                            :class="{'is-invalid': errors.has('cantidad')}"
                                            @change="validarCantidad"
                                        >
                                    <div class="invalid-feedback" v-show="errors.has('cantidad')">{{ errors.first('cantidad') }}</div>
                                </div>
                            </div>
                            <div class="row" v-if="almacenes && dato.opciones == 65537">
                                <div class="col-md-12">
                                    <div class="form-group error-content">
                                        <label for="id_almacenes">Almacén:</label>
                                           <select
                                                   class="form-control"
                                                   name="id_almacenes"
                                                   data-vv-as="Almacen"
                                                   v-model="dato_partida.destino"
                                                   v-validate="{required: true}"
                                                   id="id_almacenes"
                                                   :class="{'is-invalid': errors.has('id_almacenes')}"
                                                   @click="validarAlmacen"
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
                            <div class="row" v-if="dato.opciones == 1">
                                <div class="col-md-12">
                                    <div class="form-group row error-content">
                                    <label for="id_conceptos">Concepto:</label>
                                        <concepto-select
                                                name="id_conceptos"
                                                data-vv-as="Concepto"
                                                v-validate="{required: true}"
                                                id="id_conceptos"
                                                v-model="dato_partida.destino"
                                                :error="errors.has('id_conceptos')"
                                                ref="conceptoSelect"
                                                :disableBranchNodes="true"
                                        ></concepto-select>
                                    <div class="error-label" v-show="errors.has('id_conceptos')">{{ errors.first('id_conceptos') }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="row"  v-if="contratistas">
                                  <div class="col-md-8">
                                    <div class="form-group error-content">
                                        <label for="empresa_contratista">Empresa Contratista:</label>
                                           <select
                                                   class="form-control"
                                                   name="empresa_contratista"
                                                   data-vv-as="Material"
                                                   v-model="contratista.empresa_contratista"
                                                   v-validate="{required: false}"
                                                   id="empresa_contratista"
                                                   :class="{'is-invalid': errors.has('empresa_contratista')}">
                                            <option value>-- Seleccione --</option>
                                            <option v-for="(contratista, index) in contratistas" :value="contratista.id"
                                                    data-toggle="tooltip" data-placement="left" :title="contratista.id ">
                                                {{ contratista.razon_social }}
                                            </option>
                                        </select>
                                         <div class="invalid-feedback" v-show="errors.has('id')">{{ errors.first('id') }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                 <div class="col-md-6">
                                    <div class="form-group row error-content">
                                        <label for="opcion" class="col-sm-3 col-form-label">Tipo: </label>
                                        <div class="col-sm-10">
                                            <div class="btn-group btn-group-toggle">
                                                <label class="btn btn-outline-secondary" :class="contratista.opcion === Number(key) ? 'active': ''" v-for="(cargo, key) in cargos" :key="key">
                                                    <input type="radio"
                                                           class="btn-group-toggle"
                                                           name="opcion"
                                                           :id="'opcion' + key"
                                                           :value="key"
                                                           autocomplete="on"
                                                           v-validate="{required: false}"
                                                           v-model.number="contratista.opcion">
                                                        {{ cargo }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                         <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                <button type="submit" class="btn btn-primary" :disabled="errors.count() > 0 || dato_partida.cantidad == '' || dato_partida.destino == '' || partida == {}">Registrar</button>
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
                    opciones:'',
                    referencia:'',
                    observaciones:'',
                    partidas:[]
                },
                tipos: {
                    1: "Consumo",
                    65537: "Transferencia"
                },
                cargos: {
                    1: "Con Cargo",
                    0: "Sin Cargo"
                },
                dato_partida:{
                    cantidad:'',
                    destino:''
                },
                contratista: {
                    empresa_contratista: '',
                    opcion:''
                },
                emp_cont:'',
                contratistas:[],
                partida:{},
                empresas:[],
                almacenes:[],
                materiales:[],
                indice:'',
                material:'',
                almacen:'',
                id_almacen:'',
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
                this.getContratista();
                this.cargando = true;
                this.dato_partida.cantidad ='';
                this.dato_partida.destino ='';
                this.contratista.empresa_contratista ='';
                this.contratista.opcion ='';
                this.partida ={};
                $(this.$refs.modal).modal('show');
                this.$validator.reset();
                this.cargando = false;
            },
            getContratista() {
                return this.$store.dispatch('cadeco/empresa/index', {
                    params: {sort: 'razon_social', order: 'asc', scope:'Contratista' }
                })
                    .then(data => {
                        this.contratistas = data.data;
                    })
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
                    id: this.partida[0],
                    params: {}
                }).then(data => {
                    this.material = data;
                })
            },
            findContratista() {
                this.$store.commit('cadeco/empresa/SET_EMPRESA', null);
                return this.$store.dispatch('cadeco/empresa/find', {
                    id: this.contratista.empresa_contratista,
                    params: {}
                }).then(data => {
                    this.emp_cont = data;
                })
            },
            findAlmacen() {
                this.$store.commit('cadeco/almacen/SET_ALMACEN', null);
                if(this.dato.opciones == 65537){
                    return this.$store.dispatch('cadeco/almacen/find', {
                        id: this.dato_partida.destino,
                        params: {}
                    }).then(data => {
                        this.almacen = data;
                    })
                }else{
                    return this.$store.dispatch('cadeco/concepto/find', {
                        id: this.dato_partida.destino,
                        params: {}
                    }).then(data => {
                        this.almacen = data;
                    })
                }
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
                        this.$router.push({name: 'salida-almacen'});
                    });
            },
            borrar(){
                this.dato.partidas=[];
            },
            borrarPartidas(i){
                this.dato.partidas.splice(i,1);
            },
            validarCantidad() {
                if(parseInt(this.partida[1]) < parseInt(this.dato_partida.cantidad)) {
                    swal('¡Error!', 'La cantidad no puede ser mayor a la existencia.', 'error');
                    this.dato_partida.cantidad = '';
                    this.$nextTick(() => this.$refs.input.focus());
                }
            },
            validarAlmacen() {
                if(this.dato.id_almacen == this.partida.id_almacenes){
                    swal('¡Error!', 'No puede seleccionar el mismo almacén en el destino.', 'error');
                    this.partida.id_almacenes='';
                }
            },
            index(){
                this.$router.push({name: 'salida-almacen'});
            },
            validatePartida() {
                this.findMaterial();
                if(this.contratista.empresa_contratista != '' && this.contratista.opcion != '') {
                    this.findContratista();
                }
                this.findAlmacen().finally(() => {
                    this.dato.id_concepto = this.almacen.id_padre;
                    if(this.emp_cont != '') {
                        this.dato.partidas.push([this.material, this.dato_partida.cantidad, this.almacen, this.partida, this.emp_cont, this.contratista.opcion]);
                    }else{
                        this.dato.partidas.push([this.material, this.dato_partida.cantidad, this.almacen, this.partida]);
                    }
                });

                $(this.$refs.modal).modal('hide');

            }
        },
        watch:{
            id_almacen(value){
                if(value != ''){
                    this.dato.id_almacen = value
                    this.dato.partidas=[];
                }
            }
        }
    }
</script>
