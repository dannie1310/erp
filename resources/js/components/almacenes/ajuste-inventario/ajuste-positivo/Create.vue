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
                                                        <th class="bg-gray-light th_index">#</th>
                                                        <th class="bg-gray-light">No. de Parte</th>
                                                        <th class="bg-gray-light">Item</th>
                                                        <th class="bg-gray-light th_unidad">Unidad</th>
                                                        <th class="bg-gray-light th_money_input">Cantidad Ingresada</th>
                                                        <th class="bg-gray-light th_money_input">Saldo Inventarios</th>
                                                        <th class="bg-gray-light th_money_input">Cantidad a Sumar</th>
                                                        <th class="bg-gray-light th_index">
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
                                                        <td style="width: 180px;">
                                                             <select

                                                                     :disabled = "!bandera"
                                                                     class="form-control"
                                                                     :name="`id_material[${i}]`"
                                                                     v-model="item.material"
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
                                                                      v-model="item.material"
                                                                      v-validate="{required: true }"
                                                                      data-vv-as="Descripción"
                                                                      :class="{'is-invalid': errors.has(`id_material[${i}]`)}"
                                                              >

                                                                 <option v-for="material in materiales" :value="material">{{ material.descripcion }}</option>
                                                            </select>
                                                            <div class="invalid-feedback"
                                                                 v-show="errors.has(`id_material[${i}]`)">{{ errors.first(`id_material[${i}]`) }}
                                                            </div>
                                                        </td>
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
                                                                    v-model="item.material.cantidad"
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
                            <button type="submit" class="btn btn-primary" :disabled="errors.count() || id_almacen == '' || items.length == 0 || observaciones == '' || cargando">Registrar</button>
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
        propos:['id_almacen', 'referencia','fecha'],
        data() {
            return {
                cargando: false,
                id_almacen: this.$attrs.id_almacen,
                referencia: '',
                observaciones: '',
                fecha: '',
                items: [],
                materiales: [],
                bandera: 0
            }
        },

        methods: {
            init() {
                this.cargando = true;
            },
            /*changeSelect(item){
                item.material = this.materiales.find(x=>x.id === item.id_material);
            },*/
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
            getMateriales(){
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
            /*getMateriales(id_almacen){
                this.materiales = [];
                this.cargando = true;
                return this.$store.dispatch('cadeco/material/index', {
                    params: {
                        scope: ['inventariosDiferenciaSaldo:'+id_almacen],
                        sort: 'descripcion',
                        order: 'asc'
                    }
                })
                    .then(data => {
                        this.materiales = data.data;
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
            },*/
            agregar() {
                var array = {
                    'material' : '',
                }
                if(this.materiales.length === 0 ) {
                    this.getMateriales();
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
                            swal('¡Error!', 'Debe agregar al menos una partida.', 'error')
                        } else if(this.referencia == ''){
                            swal('¡Error!', 'Debe capturar una referencia.', 'error')
                        }
                        else {
                            this.store()
                        }
                    }
                });
            },
            store() {
                return this.$store.dispatch('almacenes/ajuste-positivo/store', this.$data)
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
