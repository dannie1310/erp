<template>
    <span>
        <div  v-if="cargando">
            <div class="row" >
                <div class="col-md-12">
                    <div class="spinner-border text-success" role="status">
                        <span class="sr-only">Cargando...</span>
                    </div>
                </div>
            </div>
        </div>
        <div v-else>
            <div class="card">
                <div class="card-body">
                    <div class="row">
                         <div class="table-responsive">
                             <table class="table table-bordered table-sm">
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
                                 <tr v-for="(material, i) in materiales">
                                     <td style="text-align: center">
                                         {{material.descripcion}}
                                     </td>
                                     <td style="text-align: right" >
                                         {{material.unidad}}
                                     </td>
                                     <td style="text-align: right" >
                                         {{material.saldo_inventario}}
                                     </td>
                                     <td style="text-align: right" >
                                         {{material.unidad}}
                                     </td>
                                     <td style="text-align: right" >
                                         {{material.unidad}}
                                     </td>
                                     <td style="text-align: right" >
                                         {{material.unidad}}
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
            materiales: []
        }
    },
    methods: {
        salir(){
            this.$router.push({name: 'kardex-material'});
        },
        find() {
            this.cargando = true;
            return this.$store.dispatch('cadeco/material/index', {
                params: {
                    sort: 'descripcion',
                    order: 'asc',
                    scope:'MaterialPorAlmacen:' + this.id,
                }
            }).then(data => {
                this.materiales = data.data;
            }).finally(() => {
                this.cargando = false;
            })
        },
    },
}
</script>

<style scoped>

</style>
