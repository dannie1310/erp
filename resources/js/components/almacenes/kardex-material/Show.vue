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
            <div class="card" v-if="!historial_cargado">
                 <div class="card-header" v-if="almacen">
                    <h4>{{almacen.descripcion}}</h4>
                </div>
                <div class="card-body" v-if="materiales">

                    <div class="row">
                        <div class="table-responsive" >
                            <table class="table table-hover table-bordered table-sm" id="table">
                                <thead>
                                    <tr>
                                        <th class="encabezado">
                                            Material
                                         </th>
                                         <th class="encabezado c70">
                                             Unidad
                                         </th>
                                         <th class="encabezado c90">
                                             Existencia
                                         </th>
                                         <th class="encabezado c120">
                                             Importe Total
                                         </th>
                                         <th class="encabezado c120">
                                             Importe Pagado
                                         </th>
                                         <th class="encabezado c120">
                                             Importe Por Pagar
                                         </th>
                                     </tr>
                                 </thead>
                                 <tbody>
                                     <tr v-for="(material, i) in materiales" :key="material.id" @click="getKardex(material.id)" >
                                         <td style="text-align: left;">
                                             {{material.descripcion}}
                                         </td>
                                         <td >
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
                                <!-- <tfoot v-if="total_mat">
                                    <tr>
                                        <th style="text-align: right"><b>TOTAL</b></th>
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
                                 </tfoot>-->
                             </table>
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

export default {
    name: "kardex-show",
    props: ['id_almacen'],
    mounted() {
        this.findAlmacen();
        this.find();
    },
    data() {
        return {
            cargando: false,
            cargando_historial : false,
            historial_cargado: false,
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
                    id: this.id_almacen
                }
            }).then(data => {
                this.materiales = data.materiales;
                this.total_mat = data.totales;
            }).finally(() => {
                this.cargando = false;
            })
        },
        findAlmacen() {
            this.cargando = true;
            return this.$store.dispatch('cadeco/almacen/find',{id: this.id_almacen
            }).then(data => {
                this.almacen = data;
            })
        },
        getKardex(i){
            this.$router.push({name: 'kardex', params: {id_almacen: this.id_almacen, id_material:i}});
        },
    },
}
</script>

<style scoped>

</style>
