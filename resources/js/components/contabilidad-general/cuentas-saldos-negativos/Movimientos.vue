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
        <div class="row" v-else>
            <div class="col-md-12">
                <table class="table">
                    <tbody>
                        <tr>
                            <td style="text-align: center; background-color: #bbb" colspan="9" >Movimientos del periodo {{this.mes_txt}} del ejercicio {{this.anio}}</td>
                        </tr>
                        <tr>
                            <td style="text-align: center; background-color: #bbb">#</td>
                            <td style="text-align: center; background-color: #bbb">Fecha</td>
                            <td style="text-align: center; background-color: #bbb">Tipo</td>
                            <td style="text-align: center; background-color: #bbb">Número</td>
                            <td style="text-align: center; background-color: #bbb">Concepto</td>
                            <td style="text-align: center; background-color: #bbb">Referencia</td>
                            <td style="text-align: center; background-color: #bbb">Cargos</td>
                            <td style="text-align: center; background-color: #bbb">Abonos</td>
                            <td style="text-align: center; background-color: #bbb">Saldo</td>
                        </tr>
                        <tr>
                            <td style="text-align: right" colspan="8">Saldo Inicial:</td>
                            <td style="text-align: right" :style="informe.data.saldo_inicial<0?`color : #F00`:``">{{informe.data.saldo_inicial_format}}</td>
                        </tr>
                        <tr v-for="(item,i) in informe.data.movimientos">
                            <td>{{i+1}}</td>
                            <td>{{item.Fecha}}</td>
                            <td>{{item.Tipo}}</td>
                            <td>{{item.Folio}}</td>
                            <td>{{item.Concepto}}</td>
                            <td>{{item.Referencia}}</td>
                            <td style="text-align: right">{{item.CargoFormat}}</td>
                            <td style="text-align: right">{{item.AbonoFormat}}</td>
                            <td style="text-align: right" :style="item.Saldo<0?`color : #F00`:``">{{item.SaldoFormat}}</td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>
    </span>
</template>

<script>
export default {
    name: "Movimientos",
    data() {
        return {
            informe : [],
            cargando: false,
            anio:'',
            mes:''
        }
    },
    mounted() {
        this.getInforme();
    },
    props: ['id','aniomes'],
    methods: {
        getInforme() {
            let valores = this.aniomes.split("-");
            this.anio = valores[0];
            this.mes = valores[1];
            this.mes_txt = "";
            this.cargando = true;
            this.$store.commit('contabilidadGeneral/cuenta-saldo-negativo/SET_ANIO_SELECCIONADO', this.anio);
            this.$store.commit('contabilidadGeneral/cuenta-saldo-negativo/SET_MES_SELECCIONADO', this.mes);
            return this.$store.dispatch('contabilidadGeneral/cuenta-saldo-negativo/obtenerInformeMovimientos', {
                id:this.id,
                anio:this.anio,
                mes:this.mes,
            })
                .then(data => {
                    this.informe = data.informe;
                    this.mes_txt = data.informe.encabezado.mes;
                })
                .finally(() => {
                    this.cargando = false;
                });
        },
    },
    watch:{
        aniomes: function(){
            this.getInforme();
        },
    }
}
</script>

<style scoped>

.table th, .table td{
    padding: 0.25rem;
    vertical-align: middle;
    border: 1px solid #dee2e6;
}

</style>
