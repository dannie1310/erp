<template>
    <span>
         <button @click="init" v-if="$root.can('consultar_entrada_almacen')" class="btn btn-app btn-info pull-right" :disabled="cargando">
            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
            <i class="fa fa-plus" v-else></i>
            Registrar Ajuste
        </button>

        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-th"></i> REGISTRAR AJUSTE POSITIVO (+) DE INVENTARIO</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                     <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
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
                                                                    placeholder="cantidad">
                                                            <div class="invalid-feedback"
                                                                 v-show="errors.has(`cantidad[${i}]`)">{{ errors.first(`cantidad[${i}]`) }}
                                                        </div>
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                             </div>
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
                almacenes: [],
                items: [

                ],
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
            agregar() {
                var array = {
                    'id' : this.items.length + 1,
                    'numero_parte' : 2,
                    'id_material' : '',
                    'id_unidad' : '',
                    'cantidad' : 0,
                    'monto_total' : 0,
                    'monto_pagado' : 0
                }
                this.items.push(array);
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