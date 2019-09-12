<template>
    <span>
         <div class="row">
            <div class="col-12">
                <div class="invoice p-3 mb-3">
                     <form role="form" @submit.prevent="validate">
                         <div class="modal-body">
                             <div class="row justify-content-between">
                                <div class="col-md-4">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group row error-content">
                                                <label for="referencia" class="col-sm-2 col-form-label">Referencia: </label>
                                                <div class="col-sm-10">
                                                    <input
                                                            type="text"
                                                            step="any"
                                                            name="referencia"
                                                            data-vv-as="Referencia"
                                                            v-validate="{required: true}"
                                                            class="form-control"
                                                            id="referencia"
                                                            placeholder="Referencia"
                                                            v-model="referencia"
                                                            :class="{'is-invalid': errors.has('referencia')}">
                                                    <div class="invalid-feedback" v-show="errors.has('referencia')">{{ errors.first('referencia') }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group row error-content">
                                                <label for="id_almacen" class="col-sm-2 col-form-label">Almacén: </label>
                                                <div class="col-sm-10">
                                                    <select
                                                            type="text"
                                                            name="id_almacen"
                                                            data-vv-as="Almacén"
                                                            v-validate="{required: true}"
                                                            class="form-control"
                                                            id="id_almacen"
                                                            v-model="id_almacen"
                                                            :class="{'is-invalid': errors.has('id_almacen')}"
                                                    >
                                                            <option value>-- Seleccione un almacén --</option>
                                                            <option v-for="almacen in almacenes" :value="almacen.id">{{ almacen.descripcion }}</option>
                                                    </select>
                                                    <div class="invalid-feedback" v-show="errors.has('id_almacen')">{{ errors.first('id_almacen') }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
                                                                <i class="fa fa-plus"></i>
                                                            </button>
                                                        </th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr v-for="(item, i) in items">
                                                        <td>{{ i + 1}}</td>
                                                        <td>
                                                             <select
                                                                     class="form-control"
                                                                     :name="`id_material[${i}]`"
                                                                     v-model="item.id_material"
                                                                     v-validate="{required: true }"
                                                                     data-vv-as="No de Parte"
                                                                     :class="{'is-invalid': errors.has(`id_material[${i}]`)}"
                                                             >

                                                                 <option v-for="numero in numero_partes" :value="numero">{{ numero.numero_parte }}</option>
                                                            </select>
                                                            <div class="invalid-feedback"
                                                                 v-show="errors.has(`id_material[${i}]`)">{{ errors.first(`id_material[${i}]`) }}
                                                            </div>
                                                        </td>
                                                        <td>
                                                              <select
                                                                      class="form-control"
                                                                      :name="`id_material[${i}]`"
                                                                      v-model="item.id_material"
                                                                      v-validate="{required: true }"
                                                                      data-vv-as="No de Parte"
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
                                                        <td>
                                                            <input
                                                                    type="number"
                                                                    step="any"
                                                                    :name="`cantidad[${i}]`"
                                                                    v-model="item.cantidad"
                                                                    data-vv-as="Cantidad"
                                                                    v-validate="{required: true, numeric:true}"
                                                                    class="form-control"
                                                                    :class="{'is-invalid': errors.has(`cantidad[${i}]`)}"
                                                                    id="cantidad"
                                                                    placeholder="Cantidad">
                                                            <div class="invalid-feedback"
                                                                 v-show="errors.has(`cantidad[${i}]`)">{{ errors.first(`cantidad[${i}]`) }}
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <input
                                                                    type="number"
                                                                    step="any"
                                                                    :name="`monto_total[${i}]`"
                                                                    v-model="item.monto_total"
                                                                    data-vv-as="Monto Total"
                                                                    v-validate="{required: true, numeric:true}"
                                                                    class="form-control"
                                                                    :class="{'is-invalid': errors.has(`monto_total[${i}]`)}"
                                                                    id="monto_total"
                                                                    placeholder="Monto Total">
                                                            <div class="invalid-feedback"
                                                                 v-show="errors.has(`monto_total[${i}]`)">{{ errors.first(`monto_total[${i}]`) }}
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <input
                                                                    type="number"
                                                                    step="any"
                                                                    :name="`monto_pagado[${i}]`"
                                                                    v-model="item.monto_pagado"
                                                                    data-vv-as="Monto Pagado"
                                                                    v-validate="{required: true, numeric:true}"
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
                                        <label for="observaciones" class="col-sm-2 col-form-label">Observaciones: </label>
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
                            <button type="submit" class="btn btn-primary" :disabled="errors.count() || id_almacen == '' || referencia == '' || items.length == 0 || observaciones == ''">Registrar</button>
                        </div>
                     </form>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "ajuste-positivo-create",
        data() {
            return {
                cargando: false,
                id_almacen: '',
                referencia: '',
                observaciones: '',
                almacenes: [],
                items: [],
                numero_partes: [],
                materiales: []
            }
        },
        mounted(){
            this.getAlmacen();
        },
        methods: {
            init() {
                this.cargando = true;
                $(this.$refs.modal).modal('show');
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
                return this.$store.dispatch('cadeco/material/index', {
                    params: {
                        scope: ['materialInventario:'+id_almacen],
                        sort: 'descripcion',
                        order: 'asc'
                    }
                })
                    .then(data => {
                        this.materiales = data.data;
                    })
            },
            getNumeroPartes(id_almacen) {
                this.numero_partes = [];
                return this.$store.dispatch('cadeco/material/index', {
                    params: {
                        scope: ['materialInventario:'+id_almacen],
                        sort: 'numero_parte',
                        order: 'asc'
                    }
                })
                    .then(data => {
                        this.numero_partes = data.data;
                    })
            },
            agregar() {
                var array = {
                    'id_material' : '',
                    'cantidad' : '',
                    'monto_total' : '',
                    'monto_pagado' : ''
                }
                this.items.push(array);
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        if(this.items.length == 0){
                            swal('¡Error!', 'Debe agregar ajustes de inventarios.', 'error')
                        }else {
                            this.store()
                        }
                    }
                });
            },
            store() {
                return this.$store.dispatch('almacenes/ajuste-positivo/store', this.$data)
                    .then((data) => {
                        this.$router.push({name: 'ajuste-positivo'});
                    });
            },
            destroy(index){
                this.items.splice(index, 1);
            },
            salir(){
                this.$router.push({name: 'ajuste-positivo'});
            }
        },
        watch: {
            id_almacen(value){
                if(value != ''){
                    this.getMateriales(value)
                    this.getNumeroPartes(value)
                }
            },
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