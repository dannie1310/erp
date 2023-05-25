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
            <div class="card" >
                <div class="card-header">
                    <h6>{{titulo_almacen}} - <strong>{{titulo_material}}</strong></h6>
                </div>
                <div class="card-body">
                    <nav >
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            <a aria-controls="nav-home" aria-selected="false" class="nav-item nav-link active" data-toggle="tab" href="#nav-home"
                               id="nav-home-tab" role="tab">Movimientos</a>
                            <a aria-controls="nav-profile" aria-selected="true" class="nav-item nav-link" data-toggle="tab"
                               href="#nav-profile_2" id="nav-profile-tab-2" role="tab">Detalle</a>
                            <a aria-controls="nav-profile" aria-selected="false" class="nav-item nav-link" data-toggle="tab"
                               href="#nav-profile" id="nav-profile-tab" role="tab">Distribución</a>
                        </div>
                    </nav>
                    <div class="tab-content" id="nav-tabContent" >
                        <div aria-labelledby="nav-home-tab" class="tab-pane fade show active" id="nav-home" role="tabpanel">
                            <movimiento v-bind:movimientos="movimientos"/>
                        </div>
                        <div aria-labelledby="nav-profile-tab" class="tab-pane fade" id="nav-profile_2" role="tabpanel">
                            <entrada v-bind:inventarios="inventarios"  v-bind:totales="total_inv"/>
                        </div>
                        <div aria-labelledby="nav-profile-tab" class="tab-pane fade" id="nav-profile" role="tabpanel">
                            <salida v-bind:salidas="salidas" v-bind:totales="total_sal"/>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" v-on:click="salir">
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
import Movimiento from './partials/Movimiento.vue';
export default {
    name: "kardex",
    components: {Entrada, Salida, Movimiento},
    props: ['id_almacen','id_material'],
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
            almacen: null,
            movimientos : null,
            titulo_material : null,
            titulo_almacen : null,
        }
    },
    methods: {
        salir(){
            this.$router.push({name: 'consultar-materiales-almacen', params: {id_almacen: this.id_almacen}});

        },

        find(){
            this.cargando = true;
            this.inventarios = null;
            this.total_inv = null;
            this.salidas = null;
            this.total_sal = null;
            this.movimientos = null;
            this.titulo_material = null;
            this.titulo_almacen = null;
            this.getHistorico();
            return this.$store.dispatch('cadeco/material/historico', {
                params: {
                    id: this.id_material,
                    id_almacen: this.id_almacen
                }
            }).then(data => {
                this.inventarios = data.inventarios;
                this.total_inv = data.totales;
                this.titulo_material = data.material;
                this.titulo_almacen = data.almacen;
                return this.$store.dispatch('cadeco/material/historicoSalida', {
                    params: {
                        id: this.id_material,
                        id_almacen: this.id_almacen
                    }
                }).then(data => {
                    this.salidas = data.salidas;
                    this.total_sal = data.totales;
                    this.getHistorico();
                }).finally(()=>{
                    this.cargando = false;
                })
            })

        },
        getHistorico(){
            this.movimientos = null;
            return this.$store.dispatch('cadeco/material/historicoMovimientos', {
                params: {
                    id: this.id_material,
                    id_almacen: this.id_almacen
                }
            }).then(data => {
                this.movimientos = data;
            })
        }
    },
}
</script>

<style scoped>

</style>
