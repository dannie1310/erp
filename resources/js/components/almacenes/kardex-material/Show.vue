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
                         <div class="table-responsive">
                             <table class="table table-hover table-bordered">
                                 <thead>
                                     <tr>
                                         <th class="encabezado" scope="col">
                                             Material
                                         </th>
                                         <th class="encabezado" scope="col">
                                             Unidad
                                         </th>
                                         <th class="encabezado" scope="col">
                                             Existencia
                                         </th>
                                         <th class="encabezado" scope="col">
                                             Total
                                         </th>
                                         <th class="encabezado" scope="col">
                                             Pagado
                                         </th>
                                         <th class="encabezado" scope="col">
                                             x Pagar
                                         </th>
                                     </tr>
                                 </thead>
                                 <tbody>
                                     <tr v-for="material in materiales" :key="material.id" @click="getHistorial(material.id)">
                                         <th style="text-align: left" scope="row">
                                             {{material.descripcion}}
                                         </th>
                                         <td style="text-align: right">
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
                    <br>
                    <div class="row" v-if="inventarios">
                         <div class="table-responsive">
                             <table class="table table-bordered table-sm">
                                 <tr>
                                     <th class="encabezado">
                                         Fecha
                                     </th>
                                     <th class="encabezado">
                                         Unidad
                                     </th>
                                     <th class="encabezado">
                                         Entradas
                                     </th>
                                     <th class="encabezado">
                                         Salidas
                                     </th>
                                     <th class="encabezado">
                                         Existencia
                                     </th>
                                     <th class="encabezado">
                                         Adquirido
                                     </th>
                                     <th class="encabezado">
                                         Pagado
                                     </th>
                                     <th class="encabezado">
                                         x Pagar
                                     </th>
                                     <th class="encabezado">
                                         Referencia
                                     </th>
                                 </tr>
                                 <tr v-for="inventario in inventarios">
                                     <td style="text-align: center">
                                         {{inventario.fecha}}
                                     </td>
                                     <td style="text-align: center">
                                         {{inventario.unidad}}
                                     </td>
                                     <td style="text-align: right">
                                         {{inventario.entrada}}
                                     </td>
                                     <td style="text-align: right">
                                         {{inventario.salida}}
                                     </td>
                                     <td style="text-align: right">
                                         {{inventario.existencia}}
                                     </td>
                                     <td style="text-align: right">
                                         {{inventario.adquirido}}
                                     </td>
                                     <td style="text-align: right">
                                         {{inventario.pagado}}
                                     </td>
                                     <td style="text-align: right">
                                         {{inventario.x_pagar}}
                                     </td>
                                     <td style="text-align: center">
                                         {{inventario.referencia}}
                                     </td>
                                 </tr>
                                 <tr v-if="total_inv">
                                     <td class="encabezado"><b>TOTAL</b></td>
                                     <td></td>
                                     <td style="text-align: right">
                                         <b>{{total_inv.entrada}}</b>
                                     </td>
                                     <td style="text-align: right">
                                         <b>{{total_inv.salida}}</b>
                                     </td>
                                     <td style="text-align: right">
                                         <b>{{total_inv.existencia}}</b>
                                     </td>
                                     <td style="text-align: right">
                                         <b>{{total_inv.adquirido}}</b>
                                     </td>
                                     <td style="text-align: right">
                                         <b>{{total_inv.pagado}}</b>
                                     </td>
                                     <td style="text-align: right">
                                         <b>{{total_inv.x_pagar}}</b>
                                     </td>
                                     <td style="text-align: center">
                                     </td>
                                 </tr>
                             </table>
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
export default {
    name: "kardex-show",
    props: ['id'],
    mounted() {
        this.find();
    },
    data() {
        return {
            cargando: false,
            materiales: [],
            inventarios: null,
            total_inv: null,
            total_mat: null
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
        getHistorial(i){
            this.inventarios = null;
            this.total_inv = null;
            return this.$store.dispatch('cadeco/material/historico', {
                params: {
                    id: i,
                    id_almacen: this.id
                }
            }).then(data => {
                this.inventarios = data.inventarios;
                this.total_inv = data.totales;
            })
        }
    },
}
</script>

<style scoped>

</style>
