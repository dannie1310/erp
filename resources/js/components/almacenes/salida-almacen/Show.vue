<template>
    <span>
        <button @click="find" type="button" class="btn btn-sm btn-outline-secondary" title="visualizar">
            <i class="fa fa-eye"></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content" v-if="cargando">
                    <div>
                        <h5 class="modal-title" id="exampleModalLongTitle"><i class="fa fa-spin fa-spinner"></i>CARGANDO</h5>
                    </div>
                </div>
                <div class="modal-content" v-else>
                    <div class="modal-header" v-if="salida">
                        <h5 class="modal-title" id="exampleModalLongTitle" v-if="salida.opciones == 1"> <i class="fa fa-th"></i> VISUALIZAR SALIDA DE  ALMACÉN</h5>
                        <h5 class="modal-title" id="exampleModalLongTitle" v-else> <i class="fa fa-th"></i> VISUALIZAR TRANSFERENCIA</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row" v-if="salida">
                            <div class="col-12">
                                <div class="invoice p-3 mb-3">
                                    <div class="row">
                                        <div class="col-12">
                                            <h5 v-if="salida.opciones == 1">Datos de Consumo</h5>
                                            <h5 v-else>Datos de Transferencia</h5>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="table-responsive col-md-12">
                                            <table class="table table-striped">
                                                <tbody>
                                                    <tr>
                                                        <td class="bg-gray-light"><b>Folio:</b></td>
                                                        <td class="bg-gray-light">{{salida.folio_format}}</td>
                                                        <td class="bg-gray-light"><b>Fecha:</b></td>
                                                        <td class="bg-gray-light">{{salida.fecha_format}}</td>

                                                    </tr>
                                                    <tr>
                                                        <td class="bg-gray-light"><b>Referencia:</b></td>
                                                        <td class="bg-gray-light">{{salida.referencia}}</td>
                                                        <td class="bg-gray-light"><b>Almacén:</b></td>
                                                        <td class="bg-gray-light">{{salida.almacen.descripcion}}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                     <div class="row">
                                        <div class="col-12">
                                            <h6><b>Detalle de las partidas</b></h6>
                                        </div>
                                     </div>
                                    <div class="row">
                                        <div class="table-responsive col-md-12">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>No. de Parte</th>
                                                        <th>Material</th>
                                                        <th>Unidad</th>
                                                        <th>Cantidad</th>
                                                        <th>Destino</th>
                                                        <th>Contratista</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(doc, i) in salida.partidas.data">
                                                        <td>{{i+1}}</td>
                                                        <td v-if="doc.material">{{doc.material.numero_parte}}</td>
                                                        <td v-if="doc.material">{{doc.material.descripcion}}</td>
                                                        <td>{{doc.unidad}}</td>
                                                        <td>{{doc.cantidad_decimal}}</td>
                                                        <td v-if="doc.concepto" :title="`${doc.concepto.path}`"><u>{{doc.concepto.descripcion}}</u></td>
                                                        <td v-else-if="doc.almacen">{{doc.almacen.descripcion}}</td>
                                                        <td class="text-danger"  v-else>No se encuentra ningun almacén asignado</td>
                                                        <td>
                                                            <button type="button" @click="agregarContratista(i)" class="btn btn-sm btn-outline-secondary" title="Modificar contratista">
                                                                    <i class="fa fa-user"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    <tr v-if="salida.observaciones" class="invoice p-3 mb-3">
                                                        <td colspan="7"><b>Observaciones: </b>{{salida.observaciones}}</td>
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
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" ref="contratista" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-th"></i> AGREGAR CONTRATISTA</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                     <form role="form" @submit.prevent="modificarContratista">
                        <div class="modal-body">
                            <fieldset class="form-group">
                                <div class="row"  v-if="contratistas">
                                      <div class="col-md-8">
                                        <div class="form-group error-content">
                                            <label for="empresa_contratista">Empresa Contratista:</label>
                                               <select
                                                       class="form-control"
                                                       name="empresa_contratista"
                                                       data-vv-as="Material"
                                                       v-model="cont.empresa_contratista"
                                                       v-validate="{required: true}"
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
                                                    <label class="btn btn-outline-secondary" :class="cont.opcion == Number(key) ? 'active': ''" v-for="(cargo, key) in cargos" :key="key">
                                                        <input type="radio"
                                                               class="btn-group-toggle"
                                                               name="opcion"
                                                               :id="'opcion' + key"
                                                               :value="key"
                                                               autocomplete="on"
                                                               v-validate="{required: true}"
                                                               v-model.number="cont.opcion">
                                                            {{ cargo }}
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                         <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="button" class="btn btn-danger" v-if="emp_cont" @click="quitarContratista">Quitar Contratista</button>
                            <button type="submit" class="btn btn-primary" :disabled="errors.count() > 0 || cont.opcion > 1">Registrar Contratista</button>
                        </div>
                     </form>
                </div>
            </div>
          </div>
    </span>
</template>

<script>
    export default {
        name: "salida-almacen-show",
        props: ['id','pagina'],
        data() {
            return {
                data: [],
                motivo: '',
                cont: {
                    empresa_contratista: '',
                    opcion:''
                },
                contratistas:[],
                indice: '',
                cargo: '',
                cargando: false,
                emp_cont:'',
                cargos: {
                    1: "Con Cargo",
                    0: "Sin Cargo"
                },
            }
        },
        methods: {
            find() {
                $(this.$refs.modal).modal('show');
                this.cargando = true;
                this.getContratista();
                this.motivo = '';
                this.$store.commit('almacenes/salida-almacen/SET_SALIDA', null);
                return this.$store.dispatch('almacenes/salida-almacen/find', {
                    id: this.id,
                    params: {include: ['almacen','partidas.movimiento.inventario','partidas.inventario','partidas.almacen','partidas.material','partidas.concepto','partidas.contratista.empresa']}
                }).then(data => {
                    this.$store.commit('almacenes/salida-almacen/SET_SALIDA', data);
                }).finally(() => {
                    this.cargando = false;
                })
            },
            findPartidas() {
                this.getContratista();
                this.motivo = '';
                this.$store.commit('almacenes/salida-almacen/SET_SALIDA', null);
                return this.$store.dispatch('almacenes/salida-almacen/find', {
                    id: this.id,
                    params: {include: ['almacen','partidas.movimiento.inventario','partidas.inventario','partidas.almacen','partidas.material','partidas.concepto','partidas.contratista.empresa']}
                }).then(data => {
                    this.$store.commit('almacenes/salida-almacen/SET_SALIDA', data);
                })
            },
            agregarContratista(i){
                this.indice = i;
                if(this.salida.partidas.data[this.indice].contratista){
                    this.cargo = this.salida.partidas.data[this.indice].contratista.con_cargo;
                    this.findContratista().then(data=>{
                        this.getContratista().then(data =>{
                            this.cont.opcion =  this.cargo;
                            this.cont.empresa_contratista = this.emp_cont.id;
                            $(this.$refs.contratista).modal('show');
                        });
                    });
                }else{
                    this.cont.opcion = 2;
                    this.cont.empresa_contratista = '';
                    this.emp_cont='';
                    this.getContratista().then(data =>{
                        $(this.$refs.contratista).modal('show');
                    });
                }
            },
            getContratista() {
                return this.$store.dispatch('cadeco/empresa/index', {
                    params: {sort: 'razon_social', order: 'asc', scope:'Contratista' }
                }).then(data => {
                    this.contratistas = data.data;
                })
            },
            findContratista() {
                this.$store.commit('cadeco/empresa/SET_EMPRESA', null);
                return this.$store.dispatch('cadeco/empresa/find', {
                    id: this.salida.partidas.data[this.indice].contratista.empresa.id,
                    params: {}
                }).then(data => {
                    this.emp_cont = data;
                })
            },
            modificarContratista(){
                return this.$store.dispatch('compras/item-contratista/update', {
                    id: this.salida.partidas.data[this.indice].id,
                    params:{data: this.cont}
                }).then(data => {
                    $(this.$refs.contratista).modal('hide');
                    this.find();
                });
            },
            quitarContratista(){
                return this.$store.dispatch('compras/item-contratista/eliminar', {
                    id: this.salida.partidas.data[this.indice].id,
                    params:{}
                }).then(data => {
                    $(this.$refs.contratista).modal('hide');
                    this.find();
                });
            },
        },
        computed: {
            salida() {
                return this.$store.getters['almacenes/salida-almacen/currentSalida'];
            }
        }
    }
</script>