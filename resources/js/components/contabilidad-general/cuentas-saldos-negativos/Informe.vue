<template>
    <span>
        <div class="row" v-if="cargando">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="spinner-border text-success" role="status">
                           <span class="sr-only">Cargando...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card" v-else-if="informe.encabezado && cargando== false">
            <div class="card-body">
                <div class="row" >
                    <div class="col-md-12">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td style="text-align: center" rowspan="3">{{informe.encabezado.empresa}}</td>
                                    <td style="text-align: center" colspan="4">Cuenta</td>
                                </tr>
                                <tr>
                                    <td style="text-align: center">Tipo</td>
                                    <td style="text-align: center">Código</td>
                                    <td style="text-align: center">Nombre</td>
                                    <td style="text-align: center">Saldo</td>
                                </tr>
                                <tr >
                                    <td style="text-align: center">{{informe.encabezado.tipo_cuenta}}</td>
                                    <td style="text-align: center"><b>{{informe.encabezado.codigo_cuenta}}</b></td>
                                    <td style="text-align: center"><b>{{informe.encabezado.nombre_cuenta}}</b></td>
                                    <td style="text-align: center" :style="informe.encabezado.saldo_cuenta<0?`color : #F00`:``"><b>{{informe.encabezado.saldo_cuenta_format}}</b></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row" >
                    <div class="col-md-3">
                        <table class="table">
                            <tbody>
                            <template v-for="(item,i) in informe.data">
                                <template v-if="i>0">
                                    <tr style="background-color: #bbb" v-if="item.anio != informe.data[i-1].anio">
                                        <td style="text-align: center" colspan="3">{{item.anio}}</td>
                                    </tr>
                                    <tr style="background-color: #bbb" v-if="item.anio != informe.data[i-1].anio">
                                        <td style="text-align: center">Mes</td>
                                        <td style="text-align: center">Monto</td>
                                        <td style="text-align: center">Saldo</td>
                                    </tr>
                                </template>
                                <template v-else>
                                    <tr style="background-color: #bbb" >
                                        <td style="text-align: center" colspan="3">{{item.anio}}</td>
                                    </tr>
                                    <tr style="background-color: #bbb">
                                        <td style="text-align: center">Periodo</td>
                                        <td style="text-align: center">Monto</td>
                                        <td style="text-align: center">Saldo</td>
                                    </tr>
                                </template>

                                <tr v-on:click="getMovimientos(item)" style="cursor: pointer" :style="(item.anio == anio_seleccionado && item.mes == mes_seleccionado)?`background-color : #AADBA2FF`:``">
                                    <td style="text-align: center">{{item.mes_txt}}</td>
                                    <td style="text-align: right" :style="item.monto<0?`color : #F00`:``">{{item.monto_format}}</td>
                                    <td style="text-align: right" :style="item.saldo<0?`color : #F00`:``">{{item.saldo_format}}</td>
                                </tr>
                            </template>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-9">
                        <router-view></router-view>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
export default {
    name: "Informe",
    data() {
        return {
            informe : [],
            cargando: false,
        }
    },
    mounted() {
        this.getInforme();
    },
    props: ['id'],
    methods: {
        getInforme() {
            this.cargando = true;
            return this.$store.dispatch('contabilidadGeneral/cuenta-saldo-negativo/obtenerInforme', {
                id:this.id

            })
            .then(data => {
                this.informe = data.informe;
            })
            .finally(() => {
                this.cargando = false;
            });
        },
        getMovimientos(item)
        {

            this.$router.push({name: 'cuenta-saldo-negativo-detalle-movimientos', params: {aniomes: item.anio+'-'+item.mes}});
        }
    },
    computed: {
        anio_seleccionado(){
            return this.$store.getters['contabilidadGeneral/cuenta-saldo-negativo/anioSeleccionado'];
        },

        mes_seleccionado(){
            return this.$store.getters['contabilidadGeneral/cuenta-saldo-negativo/mesSeleccionado'];
        },

    },
}
</script>

<style scoped>

.table th, .table td{
    padding: 0.25rem;
    vertical-align: middle;
    border: 1px solid #dee2e6;
}

</style>
