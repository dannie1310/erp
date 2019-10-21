<template>
    <span>
         <div class="row">
            <div class="col-12">
                <div class="invoice p-3 mb-3">
                     <form role="form" @submit.prevent="validate">
                         <div class="modal-body">
                             <div class="row" v-if="id_almacen">
                                <div class="col-12">
                                    <div class="invoice p-3 mb-3">
                                        <div class="row">
                                            <div class="col-12 table-responsive">
                                                <table class="table table-striped">
                                                    <thead>
                                                    <tr>
                                                        <th class="bg-gray-light">#</th>
                                                        <th class="bg-gray-light">No de Parte</th>
                                                        <th class="bg-gray-light">Item</th>
                                                        <th class="bg-gray-light">Unidad</th>
                                                        <th class="bg-gray-light">Cantidad</th>
                                                        <th class="bg-gray-light">Monto Total</th>
                                                        <th class="bg-gray-light">Monto Pagado</th>
                                                        <th class="bg-gray-light">
                                                            <button type="button" class="btn btn-sm btn-outline-success" @click="agregar">
                                                                <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                                                                <i class="fa fa-plus" v-else></i>
                                                            </button>
                                                        </th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr v-for="(item, i) in items">
                                                        <td>{{ i + 1}}</td>
                                                        <td style="width: 180px;">
                                                             <select
                                                                     :disabled = "!bandera"
                                                                     class="form-control"
                                                                     :name="`id_material[${i}]`"
                                                                     v-model="item.id_material"
                                                                     v-validate="{required: true }"
                                                                     data-vv-as="No de Parte"
                                                                     :class="{'is-invalid': errors.has(`id_material[${i}]`)}"
                                                             >

                                                                 <option v-for="numero in materiales" :value="numero">{{ numero.numero_parte }}</option>
                                                            </select>
                                                            <div class="invalid-feedback"
                                                                 v-show="errors.has(`id_material[${i}]`)">{{ errors.first(`id_material[${i}]`) }}
                                                            </div>
                                                        </td>
                                                        <td>
                                                              <select
                                                                      :disabled = "!bandera"
                                                                      class="form-control"
                                                                      :name="`id_material[${i}]`"
                                                                      v-model="item.id_material"
                                                                      v-validate="{required: true }"
                                                                      data-vv-as="Item"
                                                                      :class="{'is-invalid': errors.has(`id_material[${i}]`)}"
                                                              >

                                                                 <option v-for="material in materiales" :value="material">{{ material.descripcion }}</option>
                                                            </select>
                                                            <div class="invalid-feedback"
                                                                 v-show="errors.has(`id_material[${i}]`)">{{ errors.first(`id_material[${i}]`) }}
                                                            </div>
                                                        </td>
                                                        <td>
                                                            {{item.id_material.unidad}}
                                                        </td>
                                                        <td style="width: 120px;">
                                                            <input
                                                                    :disabled = "!item.id_material"
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
                                                                    :disabled = "!item.id_material"
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
                                                                    :disabled = "!item.id_material"
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
                                                        <td>
                                                            <button class="btn btn-outline-danger btn-sm" @click="destroy(i)"><i class="fa fa-trash"></i></button>
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
    </span>
</template>

<script>
    import MaterialSelect from "../../../cadeco/material/Select";
    export default {
        name: "nuevo-lote",
        propos:['id_almacen', 'referencia', 'fecha'],
        components: {MaterialSelect},
        data() {
            return {
                id_material:'',
                cargando: false,
                id_almacen: this.$attrs.id_almacen,
                referencia: '',
                fecha: '',
                observaciones: '',
                items: [],
                materiales: [],
                bandera: 0,
                tipos:[
                    {id: 1, descripcion: 'Materiales'},
                    {id: 4, descripcion: 'Herramienta y Equipo'},
                ]
            }
        },

        methods: {
            init() {
                this.cargando = true;
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
            getMateriales(id_almacen){
                this.cargando = true;
                this.materiales = [];
                return this.$store.dispatch('cadeco/material/index', {
                    params: {
                        scope: ['tipos:1,4'],
                        sort: 'descripcion',
                        order: 'asc'
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
                    'material':'',
                    'id_material' : '',
                    'cantidad' : '',
                    'monto_total' : '',
                    'monto_pagado' : '',
                }
                if( this.materiales.length === 0 ) {
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
