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
                                                        <th class="bg-gray-light th_index">#</th>
                                                        <th class="bg-gray-light" style="width:120px;">No. de Parte</th>
                                                        <th class="bg-gray-light">Item</th>
                                                        <th class="bg-gray-light th_unidad">Unidad</th>
                                                        <th class="bg-gray-light th_money_input">Cantidad Ingresada</th>
                                                        <th class="bg-gray-light th_money_input">Saldo Inventarios</th>
                                                        <th class="bg-gray-light th_money_input">Cantidad a Restar</th>
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
                                                        <td>{{item.material.numero_parte}}</td>
                                                        <td v-if="item.i === 2 ">
                                                            <model-list-select
                                                                    :name="`id_material[${i}]`"
                                                                    :disabled = "!bandera"
                                                                    :onchange="changeSelect(item)"
                                                                    placeholder="Seleccionar o buscar id, número de parte o descripción del material"
                                                                    data-vv-as="Material"
                                                                    v-validate="{required: true}"
                                                                    v-model="item.id_material"
                                                                    option-value="id"
                                                                    :custom-text="idAndNumeroParteAndDescripcion"
                                                                    :list="materiales"
                                                                    :isError="errors.has(`id_material[${i}]`)">
                                                            </model-list-select>
                                                            <div class="invalid-feedback"
                                                                 v-show="errors.has(`id_material[${i}]`)">{{ errors.first(`id_material[${i}]`) }}
                                                            </div>
                                                        </td>
                                                        <td v-else-if="item.i === 3">{{item.material.descripcion}}</td>
                                                        <td v-else><model-list-select
                                                                    :name="`id_material[${i}]`"
                                                                    :disabled = "!bandera"
                                                                    :onchange="changeSelect(item)"
                                                                    placeholder="Seleccionar o buscar id, número de parte o descripción del material"
                                                                    data-vv-as="Material"
                                                                    v-validate="{required: true}"
                                                                    v-model="item.id_material"
                                                                    option-value="id"
                                                                    :custom-text="idAndNumeroParteAndDescripcion"
                                                                    :list="materiales"
                                                                    :isError="errors.has(`id_material[${i}]`)">
                                                            </model-list-select>
                                                            <div class="invalid-feedback"
                                                                 v-show="errors.has(`id_material[${i}]`)">{{ errors.first(`id_material[${i}]`) }}
                                                            </div></td>
                                                        <td>
                                                            {{item.material.unidad}}
                                                        </td>
                                                         <td class="td_money">
                                                            {{item.material.cantidad_almacen}}
                                                        </td>
                                                        <td class="td_money">
                                                            {{item.material.saldo_almacen}}
                                                        </td>
                                                        <td style="width: 120px;">
                                                            <input
                                                                    :disabled = "!item.material"
                                                                    type="number"
                                                                    step="any"
                                                                    :name="`cantidad[${i}]`"
                                                                    v-model="item.cantidad"
                                                                    data-vv-as="Cantidad"
                                                                    v-validate="{required: true, min_value: 0.1}"
                                                                    class="form-control"
                                                                    :class="{'is-invalid': errors.has(`cantidad[${i}]`)}"
                                                                    id="cantidad"
                                                                    placeholder="Cantidad">
                                                            <div class="invalid-feedback"
                                                                 v-show="errors.has(`cantidad[${i}]`)">{{ errors.first(`cantidad[${i}]`) }}
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
                            <button type="submit" class="btn btn-primary" :disabled="errors.count() || id_almacen == '' || items.length == 0 || cargando ">Registrar</button>
                        </div>
                     </form>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    import {ModelListSelect} from 'vue-search-select';
    import Layout from './CargaLayout';
    export default {
        name: "ajuste-negativo-create",
        components:{ModelListSelect, Layout},
        propos:['id_almacen', 'referencia', 'fecha'],
        data() {
            return {
                cargando: false,
                id_almacen: this.$attrs.id_almacen,
                fecha: '',
                referencia: '',
                observaciones: '',
                items: [],
                materiales: [],
                bandera: 0
            }
        },

        methods: {
            init() {
                this.cargando = true;
            },
            idAndNumeroParteAndDescripcion (item) {
                return `[${item.id}] - [${item.numero_parte}] -  ${item.descripcion}`
            },
            changeSelect(item){
                var busqueda = this.materiales.find(x=>x.id === item.id_material);
                if(busqueda != undefined)
                {
                    item.material = busqueda;
                    item.i = 3;
                }
                else{
                    item.id_material = null;
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
                    })
            },
            getMateriales(id_almacen){
                this.materiales = [];
                this.cargando = true;
                return this.$store.dispatch('cadeco/almacen/find', {
                    id: this.id_almacen,
                    params: { include: 'materiales_ajuste' }
                })
                    .then(data => {
                        this.materiales = data.materiales_ajuste.data;
                        if( this.materiales.length != 0 ) {
                            this.bandera = 1;
                            this.cargando = false
                        }
                    })
                    .finally(() => {
                        if( this.materiales.length == 0 ) {
                            swal('¡Error!', 'No existe ningun material disponible para ajustar.', 'error')
                        }

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
            agregar() {
                var array = {
                    'material' : ''
                }
                if(this.materiales.length === 0 ) {
                    this.getMateriales(this.id_almacen);
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
                return this.$store.dispatch('almacenes/ajuste-negativo/store', this.$data)
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
