<template>
    <nav>
        <div class="row">
            <div class="col-12">
                <div class="invoice p-3 mb-3">
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-2">
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
                                    <button  type="button" :disabled="cargando" class="btn btn-info" @click="lista">
                                        <i v-show="!cargando" class="fa fa-list-ul "></i>
                                        <i v-show="cargando" class="spinner-border spinner-border-sm"></i>
                                        Lista de Materiales</button>
                                    &nbsp;
                                    <Layout v-model="partidas"></Layout>
                                </div>
                            </div>
                            <div class="row">
                                <div  class="col-md-12 table-responsive-xl">
                                    <div>
                                        <table class="table table-bordered">
                                            <thead>
                                            <tr>
                                                <th class="index_corto">#</th>
                                                <th style="width:130px;">No. de Parte</th>
                                                <th>Descripción</th>
                                                <th class="icono"></th>
                                                <th class="cantidad_input">Cantidad</th>
                                                <th class="unidad">Unidad</th>
                                                <th style="width:140px;">Fecha Entrega</th>
                                                <th class="icono"></th>
                                                <th>Destino</th>
                                                <th>Observaciones</th>
                                                <th class="icono">
                                                    <button type="button" class="btn btn-success btn-sm" @click="addPartidas()">
                                                        <i class="fa fa-plus"></i>
                                                    </button>
                                                </th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr v-for="(partida, i) in partidas">
                                                <td style="text-align:center; vertical-align:inherit;">{{i+1}}</td>
                                                <td v-if="partida.i === 0 && partida.material === ''">
                                                </td>
                                                <td v-else-if="partida.i === 1">
                                                    <input
                                                        type="text"
                                                        data-vv-as="Número Parte"
                                                        v-validate="{required: true}"
                                                        class="form-control"
                                                        :name="`numero_parte[${i}]`"
                                                        placeholder="Número Parte"
                                                        v-model="partida.numero_parte"
                                                        :class="{'is-invalid': errors.has(`numero_parte[${i}]`)}">
                                                    <div class="invalid-feedback" v-show="errors.has(`numero_parte[${i}]`)">{{ errors.first(`numero_parte[${i}]`) }}</div>
                                                </td>
                                                <td v-else>{{partida.material.numero_parte}}</td>
                                                <td v-if="partida.i === 0 && partida.material === ''">
                                                    <model-list-select
                                                        :name="`material[${i}]`"
                                                        v-validate="{required: true}"
                                                        v-model="partida.id_material"
                                                        :onchange="changeSelect(partida)"
                                                        option-value="id"
                                                        :custom-text="idAndNumeroParteAndDescripcion"
                                                        :list="materiales"
                                                        :placeholder="!cargando?'Seleccionar o buscar material por descripcion':'Cargando...'"
                                                        :isError="errors.has(`material[${i}]`)">
                                                    </model-list-select>
                                                    <div class="invalid-feedback" v-show="errors.has('id_material')">{{ errors.first('id_material') }}</div>
                                                </td>
                                                <td v-else-if="partida.i === 1">
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
                                                </td>
                                                <td v-else>{{partida.material.descripcion}}</td>
                                                <td v-if="partida.i === 0">
                                                    <button  type="button" class="btn btn-outline-primary btn-sm" @click="manual(i)" title="Ingresar material manualmente"><i class="fa fa-hand-paper-o" /></button>
                                                </td>
                                                <td v-else-if="partida.i === 1">
                                                    <button type="button" class="btn btn-outline-primary btn-sm" @click="busqueda(i)" title="Buscar material"><i class="fa fa-refresh" /></button>
                                                </td>
                                                <td style="width: 30px;" v-else></td>
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
                                                <td style="width:120px;" v-if="partida.i === 1">
                                                    <select
                                                        type="text"
                                                        :name="`unidad[${i}]`"
                                                        data-vv-as="Unidad"
                                                        v-validate="{required: true}"
                                                        class="form-control"
                                                        id="unidad"
                                                        v-model="partida.unidad"
                                                        :class="{'is-invalid': errors.has(`unidad[${i}]`)}">
                                                        <option value>--Unidad--</option>
                                                        <option v-for="unidad in unidades" :value="unidad.unidad">{{ unidad.descripcion }}</option>
                                                    </select>
                                                    <div class="invalid-feedback" v-show="errors.has(`unidad[${i}]`)">{{ errors.first(`unidad[${i}]`) }}</div>
                                                </td>
                                                <td style="width:120px;" v-else-if="partida.unidad">{{partida.unidad}}</td>
                                                <td style="width:120px;" v-else>{{partida.material.unidad}}</td>
                                                <td class="fecha">
                                                    <datepicker v-model="partida.fecha"
                                                                :name="`fecha[${i}]`"
                                                                :format = "formatoFecha"
                                                                :language = "es"
                                                                :bootstrap-styling = "true"
                                                                class = "form-control"
                                                                v-validate="{required: true}"
                                                                :disabled-dates="fechasDeshabilitadasHasta"
                                                                :class="{'is-invalid': errors.has(`fecha[${i}]`)}"
                                                    ></datepicker>
                                                    <div class="invalid-feedback" v-show="errors.has(`fecha[${i}]`)">{{ errors.first(`fecha[${i}]`) }}</div>
                                                </td>
                                                <td style="text-align:center;"><small class="badge badge-secondary">
                                                    <i class="fa fa-sign-in button" aria-hidden="true" v-on:click="modalDestino(i)"></i>
                                                </small></td>
                                                <td style="width:140px;" v-if="partida.clave_concepto"><u>{{partida.clave_concepto.descripcion}}</u></td>
                                                <td style="width:140px; text-align:center;" v-else-if="partida.destino">{{partida.destino.descripcion}}</td>
                                                <td style="width:140px; text-align:center;" v-else></td>
                                                <td style="width:200px;">
                                                            <textarea class="form-control"
                                                                      :name="`observaciones[${i}]`"
                                                                      data-vv-as="Observaciones"
                                                                      v-validate="{required: true}"
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
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" v-on:click="salir">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Registrar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </nav>
</template>

<script>
    import MaterialSelect from "../../cadeco/material/SelectAutocomplete"
    import SelectDestino from "../../cadeco/destino/Select";
    export default {
        name: "solicitud-compra-create",
        components: {MaterialSelect, SelectDestino},
        data(){
            return{
                areas_compradoras: [],
                areas_solicitantes:[],
                tipos: [],
                marcas: [],
                modelos: [],
                cargando: false,
                disabled: true,
                index:0,
                id_area_compradora: '',
                id_area_solicitante: '',
                id_tipo: '',
                fecha_requisicion: '',
                folio_requisicion: '',
                concepto: '',
                observaciones: '',
                destino: '',
                items: [
                    {
                        aux:'',
                        material: "",
                        id_material:"",
                        numero_parte: "",
                        marca: "",
                        modelo: "",
                        cantidad:"",
                        unidad: "",
                        fecha:"",
                        destino:"",
                        id_destino:"",
                        observaciones:"",
                        destino_concepto: null,
                        destino_almacen: null,
                    }
                ],
            }

        },
        mounted() {
            this.$validator.reset()
            this.getAreasCompradoras();
            this.getTipos();
            this.getAreasSolicitantes();
            this.getMarcas();
            this.getModelos();
        },
        methods : {
            getAreasCompradoras(){
                return this.$store.dispatch('configuracion/area-compradora/index', {
                    params: { scope: 'asignadas', sort: 'descripcion',  order: 'asc'}
                })
                    .then(data => {
                        this.areas_compradoras = data;
                        this.disabled = false;
                    })
            },
            getTipos() {
                return this.$store.dispatch('configuracion/ctg-tipo/index', {
                    params: {sort: 'descripcion',  order: 'asc'}
                })
                    .then(data => {
                        this.tipos = data.data;
                        this.disabled = false;
                    })
            },
            getAreasSolicitantes(){
                return this.$store.dispatch('configuracion/area-solicitante/index', {
                    params: { scope: 'asignadas', sort: 'descripcion',  order: 'asc'}
                })
                    .then(data => {
                      this.areas_solicitantes = data;
                        this.disabled = false;
                    })
            },
            addRow(index){
                    this.items.splice(index + 1, 0, {});
                    this.index = index+1;
            },
            removeRow(index){
                this.items.splice(index, 1);
            },
            getMarcas() {
                return this.$store.dispatch('sci/marca/index',{
                    params: { sort: 'marca', order: 'asc'}
                })
                    .then(data => {
                       this.marcas = data.data;
                    })
                    .finally(() => {
                        this.disabled = false;
                    })
            },
            getModelos(){
                return this.$store.dispatch('sci/modelo/index',{
                    params: { sort: 'modelo', order: 'asc'}
                })
                    .then(data => {
                      this.modelos = data.data;
                    })
                    .finally(() => {
                        this.disabled = false;
                    })
            },
            salir(){
                this.$router.push({name: 'solicitud-compra'});
            },
            store() {
                return this.$store.dispatch('compras/solicitud-compra/store',  this.$data )
                    .then((data) => {
                        $(this.$refs.modal).modal('hide');
                        this.$emit('created',data)

                    })

            },
            setRowValues(material,i){
                this.items[i].id_material = material.id;
                this.items[i].numero_parte = material.numero_parte;
                this.items[i].unidad = material.unidad;
                this.items[i].material_status = true;
            },
            setDestinoValues(dest, i){

                if(dest.text){
                    /*Almacen*/
                    this.items[i].destino_concepto=false;
                    this.items[i].destino_almacen=true;
                    this.items[i].id_destino = dest.value;
                    this.items[i].destino = dest.text;
                }
                if(dest.path){
                   /*Concepto*/
                    this.items[i].destino_concepto=true;
                    this.items[i].destino_almacen=false;
                    this.items[i].id_destino = dest.id;
                    this.items[i].destino = dest.path;

                }

            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.store()
                    }
                });
            },

        },
        computed :{
            areas(){
                return this.$store.getters['configuracion/area-compradora/areas']
            },
        }

    }
</script>

<style>
    .error > .vue-treeselect__control {
        border-color: #dc3545
    }
     .error {
         border-color: #dc3545
     }

</style>
