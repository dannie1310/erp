<template>
    <span>
        <div  v-if="cargando">
            <div class="card">
                <div class="card-body">
                    <div class="col-md-12">
                        <div class="spinner-border text-success" role="status">
                            <span class="sr-only">Cargando...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-else>
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="table-responsive" style="max-height: 250px; overflow-y: scroll;">
                            <table class="table table-hover table-bordered table-sm" id="table">
                                <thead>
                                    <tr>
                                        <th class="encabezado">
                                            Material
                                         </th>
                                         <th class="encabezado">
                                             Unidad
                                         </th>
                                         <th class="encabezado">
                                             Existencia
                                         </th>
                                         <th class="encabezado">
                                             Total
                                         </th>
                                         <th class="encabezado">
                                             Pagado
                                         </th>
                                         <th class="encabezado">
                                             x Pagar
                                         </th>
                                     </tr>
                                 </thead>
                                 <tbody>
                                     <tr v-for="(material, i) in materiales" :key="material.id" @click="getHistorial(material.id, i+1)">
                                         <th style="text-align: left; width:200px;">
                                             {{material.descripcion}}
                                         </th>
                                         <td style="text-align: right; width:100px;">
                                             {{material.unidad}}
                                         </td>
                                         <td style="text-align: right">
                                             {{material.existencia}}
                                         </td>
                                         <td style="text-align: right">
                                             {{material.total}}
                                         </td>
                                         <td style="text-align: right">
                                             {{material.pagado}}
                                         </td>
                                         <td style="text-align: right">
                                             {{material.por_pagar}}
                                         </td>
                                     </tr>
                                 </tbody>
                                 <tfoot>
                                    <tr>
                                        <th style="text-align: center"><b>TOTAL</b></th>
                                        <td></td>
                                        <td></td>
                                        <td style="text-align: right">
                                            <b>{{total_mat.total}}</b>
                                        </td>
                                        <td style="text-align: right">
                                            <b>{{total_mat.pagado}}</b>
                                        </td>
                                        <td style="text-align: right">
                                            <b>{{total_mat.x_pagar}}</b>
                                        </td>
                                    </tr>
                                 </tfoot>
                             </table>
                        </div>
                    </div>
                    <br />
                    <hr />
                    <nav v-if="inventarios">
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            <a aria-controls="nav-home" aria-selected="true" class="nav-item nav-link active" data-toggle="tab" href="#nav-home"
                            id="nav-home-tab" role="tab">Entradas</a>
                            <a aria-controls="nav-profile" aria-selected="false" class="nav-item nav-link" data-toggle="tab"
                            href="#nav-profile" id="nav-profile-tab" role="tab">Salidas</a>
                        </div>
                    </nav>
                    <div class="tab-content" id="nav-tabContent" v-if="inventarios">
                        <div aria-labelledby="nav-home-tab" class="tab-pane fade show active" id="nav-home" role="tabpanel">
                            <entrada v-bind:inventarios="inventarios"  v-bind:totales="total_inv"/>
                        </div>
                        <div aria-labelledby="nav-profile-tab" class="tab-pane fade" id="nav-profile" role="tabpanel">
                            <salida v-bind:salidas="salidas" v-bind:totales="total_sal"/>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" v-on:click="salir">
                        <i class="fa fa-angle-left"></i>
                        Regresar
                    </button>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
import Entrada from './partials/Entrada.vue';
import Salida from './partials/Salida.vue';
export default {
    name: "kardex-show",
    components: {Entrada, Salida},
    props: ['id'],
    mounted() {
        this.find();
    },
    data() {
        return {
            cargando: false,
            materiales: [],
            inventarios: null,
            salidas: null,
            total_inv: null,
            total_mat: null,
            num_pas: 0,
            total_sal: null,
        }
    },
    methods: {
        salir(){
            this.$router.push({name: 'kardex-material'});
        },
        find() {
            this.cargando = true;
            return this.$store.dispatch('cadeco/material/porAlmacen', {
                params: {
                    id: this.id
                }
            }).then(data => {
                this.materiales = data.materiales;
                this.total_mat = data.totales;
            }).finally(() => {
                this.cargando = false;
            })
        },
        getHistorial(i, key){
            this.inventarios = null;
            this.total_inv = null;
            this.salidas = null;
            this.total_sal = null;
            document.getElementById('table').rows[this.num_pas].style.backgroundColor = "white";
            document.getElementById('table').rows[key].style.backgroundColor = "#CFFCBC";
            return this.$store.dispatch('cadeco/material/historico', {
                params: {
                    id: i,
                    id_almacen: this.id
                }
            }).then(data => {
                this.inventarios = data.inventarios;
                this.total_inv = data.totales;
                this.num_pas = key;
                return this.$store.dispatch('cadeco/material/historicoSalida', {
                    params: {
                        id: i,
                        id_almacen: this.id
                    }
                }).then(data => {
                    this.salidas = data.salidas;
                    this.total_sal = data.totales;
                })
            })
        }
    },
}
</script>

<style scoped>

</style>
