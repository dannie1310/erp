<template>
    <span>
         <div class="row">
            <div class="col-12">
                <div class="invoice p-3 mb-3">
                     <form role="form" @submit.prevent="validate">
                         <div class="modal-body">
                             <div class="d-flex flex-row-reverse">
                                    <div class="p-3">
                                        <button  type="button" v-if="id_almacen" class="btn btn-info" @click="lista">
                                                <i class="fa fa-list-ul "></i>
                                                 Lista de Materiales</button>
                                        &nbsp;
                                        <Layout @created="getMateriales()" v-if="id_almacen" v-model="items"></Layout>
                                    </div>
                                </div>

                             <div class="row" v-if="id_almacen">
                                <div class="col-12">
                                    <div class="invoice p-3 mb-3">
                                        <div class="row">
                                            <div class="col-12 table-responsive-xl">
                                                <table class="table table-striped">
                                                    <thead>
                                                    <tr>
                                                        <th class="bg-gray-light index_corto ">#</th>
                                                        <th class="bg-gray-light" style="width:120px;">No. de Parte</th>
                                                        <th class="bg-gray-light">Item</th>
                                                        <th class="bg-gray-light th_unidad">Unidad</th>
                                                        <th class="bg-gray-light th_money_input">Cantidad</th>
                                                        <th class="bg-gray-light th_money_input">Monto Total</th>
                                                        <th class="bg-gray-light th_money_input">Monto Pagado</th>
                                                        <th class="bg-gray-light icono">
                                                            <button type="button" class="btn btn-sm btn-outline-success" @click="agregar" :disabled="cargando">
                                                                <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                                                                <i class="fa fa-plus" v-else></i>
                                                            </button>
                                                        </th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr v-for="(item, i) in items">
                                                        <td>{{ i + 1}}</td>
                                                        <td v-if="item.i === 2">{{item.material.numero_parte}}</td>
                                                        <td v-else></td>
                                                        <td v-if="item.i === 2">{{item.material.descripcion}}</td>
                                                        <td v-else>
                                                            <input type="text" class="form-control"
                                                                readonly="readonly"
                                                                @click="modalMaterial(i)"
                                                                :name="`id_material[${i}]`"
                                                                data-vv-as="Material"
                                                                placeholder="Seleccionar Material"
                                                                v-validate="{required:true}"
                                                                :class="{'is-invalid': errors.has(`id_material[${i}]`)}"
                                                                :id="`descripcion[${i}]`">
                                                            <div class="invalid-feedback" v-show="errors.has(`id_material[${i}]`)">{{ errors.first(`id_material[${i}]`) }}</div>
                                                        </td>
                                                        <td>
                                                            {{item.material.unidad}}
                                                        </td>
                                                        <td style="width: 120px;">
                                                            <input
                                                                    :disabled = "!item.material"
                                                                    type="number"
                                                                    step="any"
                                                                    :name="`cantidad[${i}]`"
                                                                    v-model="item.cantidad"
                                                                    data-vv-as="Cantidad"
                                                                    v-validate="{required: true,min_value: 0.1}"
                                                                    class="form-control"
                                                                    :class="{'is-invalid': errors.has(`cantidad[${i}]`)}"
                                                                    id="cantidad"
                                                                    placeholder="Cantidad">
                                                            <div class="invalid-feedback"
                                                                 v-show="errors.has(`cantidad[${i}]`)">{{ errors.first(`cantidad[${i}]`) }}
                                                            </div>
                                                        </td>
                                                        <td style="width: 120px;">
                                                            <input
                                                                    :disabled ="!item.material"
                                                                    type="number"
                                                                    step="any"
                                                                    :name="`monto_total[${i}]`"
                                                                    v-model="item.monto_total"
                                                                    data-vv-as="Monto Total"
                                                                    v-validate="{min_value: 0, required:true}"
                                                                    class="form-control"
                                                                    :class="{'is-invalid': errors.has(`monto_total[${i}]`)}"
                                                                    id="monto_total"
                                                                    placeholder="Monto Total">
                                                            <div class="invalid-feedback"
                                                                 v-show="errors.has(`monto_total[${i}]`)">{{ errors.first(`monto_total[${i}]`) }}
                                                            </div>
                                                        </td>
                                                        <td style="width: 120px;">
                                                            <input
                                                                    :disabled = "!item.material"
                                                                    type="number"
                                                                    step="any"
                                                                    :name="`monto_pagado[${i}]`"
                                                                    v-model="item.monto_pagado"
                                                                    data-vv-as="Monto Pagado"
                                                                    v-validate="{min_value: 0, max_value:item.monto_total, required:true}"
                                                                    class="form-control"
                                                                    :class="{'is-invalid': errors.has(`monto_pagado[${i}]`)}"
                                                                    id="monto_pagado"
                                                                    placeholder="Monto Pagado">
                                                            <div class="invalid-feedback"
                                                                 v-show="errors.has(`monto_pagado[${i}]`)">{{ errors.first(`monto_pagado[${i}]`) }}
                                                            </div>
                                                        </td>
                                                        <td style="text-align:center">
                                                            <button type="button" class="btn btn-outline-danger btn-sm" @click="destroy(i)"><i class="fa fa-trash"></i></button>
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                             </div>
                              <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row error-content">
                                        <label for="observaciones" class="col-sm-2 col-form-label">Observaciones:  </label>
                                        <div class="col-sm-10">
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
                         </div>
                         <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" v-on:click="salir">Cerrar</button>
                            <button type="submit" class="btn btn-primary" :disabled="errors.count() || id_almacen == '' || items.length == 0 ">Registrar</button>
                        </div>
                     </form>
                </div>
            </div>
        </div>
        <div ref="modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"><i class="fa fa-list" style="padding-right:3px"></i>Agregar Item</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" >
                        <div class="col-md-12">
                            <div class="form-group error-content">
                                <label for="id_material">Material</label>
                                <div class="row">
                                    <div class="col-md-10">
                                        <input
                                            type="text"
                                            name="busqueda"
                                            class="form-control"
                                            data-vv-as="Busqueda"
                                            v-model="busqueda">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-primary" @click="buscarMateriales()" :disabled="busqueda === ''">Buscar</button>

                                    </div>
                                </div>

                            </div>
                            <div class="form-group error-content" v-if="resultados.length > 0">
                                <label for="id_material">Seleccionar</label>
                                <div class="row">
                                     <div class="col-md-12">
                                        <select
                                                :disabled="resultados.length == 0"
                                                type="text"
                                                name="seleccion"
                                                data-vv-as="Seleccionar Material"
                                                class="form-control"
                                                id="seleccion"
                                                v-model="id_seleccion"
                                            >
                                                    <option value>-- SELECCIONAR --</option>
                                                    <option v-for="(material, i) in resultados" :value="i">{{ material.descripcion }}</option>
                                            </select>
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="form-group error-content">
                                 <label for="id_material">Material</label>
                                <MaterialSelect
                                    :scope="scope"
                                    :name="material"
                                    v-model="material"
                                    data-vv-as="Material"
                                    v-validate="{required: true}"
                                    ref="MaterialSelect"
                                    :disableBranchNodes="false"/> -->

                                <!-- <input type="text" autofocus class="form-control"
                                    name="descripcion"
                                    data-vv-as="Descripción"
                                    v-model="descrip_temporal"
                                    v-on:keyup.enter="()"
                                    id="descripcion"> -->

                            </div>
                       </div>
                       <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="button" class="btn btn-primary" @click="agregarMaterial()" :disabled="id_seleccion === ''">Agregar</button>
                       </div>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    import MaterialSelect from "../../../cadeco/material/SelectAutocomplete";
    import {ModelListSelect} from 'vue-search-select';
    import Layout from "./CargaLayout";
    export default {
        name: "nuevo-lote",
        propos:['id_almacen', 'referencia', 'fecha'],
        components: {MaterialSelect, ModelListSelect, Layout},
        data() {
            return {
                busqueda:'',
                resultados:[],
                id_seleccion:'',
                material:[],
                scope: ['insumos', 'tipo:1,4'],
                id_material:'',
                cargando: false,
                id_almacen: this.$attrs.id_almacen,
                tipo_almacen: this.$attrs.tipo_almacen,
                referencia: '',
                items: [],
                index: '',
                fecha: '',
                observaciones: '',
                materiales: [],
                bandera: 0,
                tipos:[
                    {id: 1, descripcion: 'Materiales'},
                    {id: 4, descripcion: 'Herramienta y Equipo'},
                ]
            }
        },

        methods: {
            buscarMateriales(){
                this.cargando = true;
                this.resultados = [];
                this.id_seleccion = '';
                return this.$store.dispatch('cadeco/material/buscarMateriales', {
                    params: {
                        busqueda:this.busqueda
                    }
                })
                .then(data => {
                    this.resultados = data.data;
                    this.cargando = false;
                })
            },
            init() {
                this.cargando = true;
            },
            idAndNumeroParteAndDescripcion (item) {
                return `[${item.id}] - [${item.numero_parte}] -  ${item.descripcion}`
            },
            agregarMaterial(){
                this.items[this.index].id_material = this.resultados[this.id_seleccion].id;
                this.items[this.index].material = this.resultados[this.id_seleccion];
                this.items[this.index].i = 2;
                $(this.$refs.modal).modal('hide')
            },
            modalMaterial(index){
                this.resultados = [];
                this.busqueda = '';
                this.id_seleccion = '';
                this.index =index;
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show')

            },
            changeSelect(item){

                var busqueda = this.materiales.find(x=>x.id === item.id_material);
                if(busqueda != undefined)
                {
                    item.material = busqueda;
                    item.i = 2;

                }
            },
            getAlmacen(){
                this.almacenes = [];
                return this.$store.dispatch('cadeco/almacen/index', {
                    params: {
                        scope: ['tipoMaterialYHerramienta'],
                        sort: 'descripcion',
                        order: 'asc'
                    }
                })
                    .then(data => {
                        this.almacenes = data.data;
                        this.cargando = false;

                    })
            },
            lista()
            {
                 this.cargando = true;
                return this.$store.dispatch('cadeco/material/lista_materiales', {scope: 'materialesParaCompras', sort: 'descripcion', order: 'desc'})
                    .then(() => {
                        this.$emit('success')
                    }).finally(() => {
                        this.cargando = false;
                    })
            },
            getMateriales(id_almacen){
                this.cargando = true;
                this.materiales = [];
                return this.$store.dispatch('cadeco/material/index', {
                    params: {
                        scope: ['tipos:1,4'],
                        sort: 'descripcion',
                        order: 'asc',
                    }
                })
                    .then(data => {
                        this.materiales = data.data;
                        this.bandera = 1;
                        this.cargando = false;
                    })
            },
            agregar() {
                var array = {
                    'i': 0,
                    'material':'',
                    'id_material' : '',
                    'descripcion' : '',
                    'cantidad' : '',
                    'monto_total' : '',
                    'monto_pagado' : '',
                }
                if( this.materiales.length === 0 ) {
                    // this.getMateriales(this.id_almacen);
                }
                this.referencia = this.$attrs.referencia;
                this.fecha = this.$attrs.fecha;
                this.items.push(array);
            },
            validate() {
                this.referencia = this.$attrs.referencia;
                this.fecha = this.$attrs.fecha;
                this.$validator.validate().then(result => {
                    if (result) {
                        if(this.items.length == 0){
                            swal('¡Error!', 'Debe agregar ajustes de inventarios.', 'error')
                        } else if(this.referencia == ''){
                            swal('¡Error!', 'Debe agregar una referencia.', 'error')
                        }
                        else {
                            this.store()
                        }
                    }
                });
            },
            store() {
                return this.$store.dispatch('almacenes/nuevo-lote/store', this.$data)
                    .then((data) => {
                        this.$router.push({name: 'ajuste-inventario'});
                    });
            },
            destroy(index){
                this.items.splice(index, 1);
            },
            salir(){
                this.$router.push({name: 'ajuste-inventario'});
            }
        }
    }
</script>

<style scoped>
    .error-label {
        width: 100%;
        margin-top: 0.25rem;
        font-size: 80%;
        color: #dc3545;
    }
</style>
