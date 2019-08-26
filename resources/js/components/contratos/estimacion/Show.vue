<template>
     <span>
    <div class="row">


        <div class="col-12 mb-5">
            <div class="card">
                <div class="card-body">
                        <div class="row mt-5 mb-5">

                                                <div class="col-8">
                                                    <div class="row">
                                                        <div class="col-4">

                                <!--                    <img v-bind:src="logo" class="img-thumbnail">-->

                                                </div>
                                                <div class="col-8 text-center mt-5">

                                                    <h1>{{ obra.facturar }}</h1>

                                                       <h3 class="mt-5">Cuerpo de Estimación</h3>
                                                </div>
                                                </div>
                                                </div>
                                                <div class="col-4">
                                                            <table class="table table-bordered table-hover">
                                  <thead>
                                    <tr>
                                      <th scope="col">Folio SAO</th>
                                      <th class="text-right" scope="col">#{{ estimacion.folio }}</th>

                                    </tr>
                                  </thead>
                                  <tbody>
                                    <tr>
                                      <th scope="row">No. Estimación</th>
                                      <td class="text-right"> {{ estimacion.subcontratoEstimacion.NumeroFolioConsecutivo }}</td>

                                    </tr>
                                    <tr>
                                      <th scope="row">Semana de Contrato</th>
                                      <td></td>

                                    </tr>
                                    <tr>
                                      <th scope="row">Fecha</th>
                                      <td class="text-right"> {{ estimacion.fecha }}</td>

                                    </tr>
                                      <tr>
                                      <th scope="row">Periodo</th>
                                      <td class="text-right">De : {{ estimacion.fecha_inicial }} A: {{ estimacion.fecha_final }}</td>

                                    </tr>
                                  </tbody>
                                </table>

                </div>
</div>

                    <table class="table table-hover table-bordered" v-if="estimacion">
                      <thead>
                        <tr>
                          <th>Organización:</th>
                          <th class="text-center" scope="row">{{ obra.nombre }}</th>

                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <th>Contratista: </th>
                          <th class="text-center" scope="row">{{ estimacion.empresa.razon_social }}</th>

                        </tr>
                        <tr>
                          <th>No. de Contrato: </th>
                          <th class="text-center" scope="row">{{ estimacion.subcontrato.referencia }}</th>

                        </tr>

                      </tbody>
                    </table>

                    <!-- Partidas--->
                                <div class="col-12 mt-5">

            <table class="table table-hover table-bordered">

                        <thead class="thead-dark text-center">
                            <tr>
                                <th rowspan="2">Concepto</th>
                                <th rowspan="2">U.M.</th>
                                <th rowspan="2">P.U.</th>
                                <th colspan="2">Contrato y Aditamentos</th>
                                <th colspan="2">Acum. A Estimación Anterior</th>
                                <th colspan="2">Esta Estimación </th>
                                <th colspan="2">Acum. A Esta Estimación </th>
                                <th colspan="2">Saldo por Estimar</th>
                            </tr>
                            <tr>
                                <th scope="col">Cantidad</th>
                                <th scope="col"> Importe</th>
                                <th scope="col">Cantidad</th>
                                <th scope="col"> Importe</th>
                                <th scope="col">Cantidad</th>
                                <th scope="col"> Importe</th>
                                <th scope="col">Cantidad</th>
                                <th scope="col"> Importe</th>
                                <th scope="col">Cantidad</th>
                                <th scope="col"> Importe</th>
                            </tr>


                        </thead>

                        <tbody>
                            <tr>
                                <th class="text-secondary">OBRA EJECUTADA</th>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td class="text-center">{{ estimacion.moneda.nombre }}</td>
                                  <td></td>
                                <td class="text-center">{{ estimacion.moneda.nombre }}</td>
                                  <td></td>
                                <td class="text-center">{{ estimacion.moneda.nombre }}</td>
                                 <td></td>
                                <td class="text-center">{{ estimacion.moneda.nombre }}</td>
                                  <td></td>
                                <td class="text-center">{{ estimacion.moneda.nombre }}</td>
                            </tr>

                            <!--Items de la Estimación -->
                   <tr v-for="data in items">

                                <th v-if="data">{{ data.concepto }}</th>
                                <td class="text-center">{{ data.unidad }}</td>
                                <td class="text-right">{{ data.precio_unitario }} </td>
                                <td class="text-right">{{ data.cantidad_original }}</td>
                                <td class="text-right">{{ data.contrato_importe }}</td>
                                <td class="text-right">{{ data.estimaAnterior }}</td>
                                <td class="text-right">{{ data.anteriorImporte }}</td>
                                <td class="text-right">{{ data.cantidad }}</td>
                                <td class="text-right">{{ data.importe }}</td>
                                <td class="text-right">{{ data.acumulada }}</td>
                                <td class="text-right">{{ data.acumuladaImporte }}</td>
                                <td class="text-right">{{ data.cantidad_estimar}} </td>
                                <td class="text-right">{{ data.importe_estimar }}</td>
                            </tr>

                    <!--Sumas totales de la s partidas-->

                              <tr class="bg-dark">
                                <th class="text-right">Subtotales Obra Ejecutada</th>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td class="text-right">{{ contratoSuma }}</td>
                                <td></td>
                                <td class="text-right"> {{ sumaAnterior }}</td>
                                <td></td>
                                <td class="text-right"> {{ sumaEstimacion }}</td>
                                <td></td>
                                <td class="text-right"> {{ sumaAcumulada }}</td>
                                <td> </td>
                                <td class="text-right">{{ sumaPorEstimar }}</td>
                            </tr>


                        </tbody>
                    </table>

                    </div>

                </div>
            </div>

        </div>
    </div>
     </span>
</template>


<script>

    import IndexEstimacion from "./Index";

    export default {
        name: "estimacion-show",
        components: {IndexEstimacion},
        // props: ['id'],
        data() {
            return {
                cargando: false,
                logo: '',
                obra: [],
                items: [],
                estimaciones: [],
                estimaAnterior: 0,
                suma_contrato: 0,
                contratoSuma: '',
                suma_anteriorAcumulada: 0,
                sumaAnterior: '',
                suma_estimacion: 0,
                sumaEstimacion: '',
                suma_acumulada: 0,
                sumaAcumulada: '',
                suma_porEstimar: 0,
                sumaPorEstimar: '',
                id: '',
            }
        },
        mounted() {
            this.obra = this.$session.get('obra');
            this.id = this.$route.params.id;
            this.form = JSON.parse(JSON.stringify(this.obra));
            setTimeout(() => {
                if (this.form.configuracion.logotipo_original) {
                    this.logo = `data:image/png;base64,${this.form.configuracion.logotipo_original}`;
                }
            }, 100);
            this.find();
        },
        methods: {
            find() {

                this.cargando = true;

                this.$store.commit('contratos/estimacion/SET_ESTIMACION', null);
                return this.$store.dispatch('contratos/estimacion/find', {
                    id: this.id,
                    params: {
                        include: ['empresa', 'subcontratoEstimacion', 'moneda', 'item', 'item.concepto', 'item.contrato', 'subcontrato',]
                    }
                }).then(data => {
                    this.$store.commit('contratos/estimacion/SET_ESTIMACION', data);

                }).finally(() => {
                    this.remakeItems();
                });
            },
            getAcumulada(item) {
                return this.$store.dispatch('contratos/estimacion/estimaAnterior', {
                    params: {
                        id: this.id,
                        antecedente: item
                    }
                })
                    .then(data => {
                        return data;
                    })

            },
            remakeItems() {

                this.estimacion.item.data.forEach((item) => {
                    console.log(item);

                    this.getAcumulada(item.item_antecedente).then((estimaAnterior) => {

                        this.suma_contrato += parseFloat(item.precio_unitario * item.contrato.cantidad_original);
                        this.suma_anteriorAcumulada += parseFloat(estimaAnterior * item.precio_unitario);
                        this.suma_estimacion += parseFloat(item.cantidad * item.precio_unitario);
                        this.suma_acumulada += (parseFloat(estimaAnterior) + parseFloat(item.cantidad) * item.precio_unitario);
                        this.suma_porEstimar += parseFloat(((item.contrato.cantidad_original) - (parseFloat(estimaAnterior) + parseFloat(item.cantidad))) * item.precio_unitario)
                        this.items.push({
                            'concepto': item.concepto.descripcion,
                            'unidad': item.contrato.unidad,
                            'precio_unitario': parseFloat(item.precio_unitario).formatMoney(3, '.', ','),
                            'cantidad_original': parseFloat(item.contrato.cantidad_original).formatMoney(3, '.', ','),
                            'contrato_importe': parseFloat(item.precio_unitario * item.contrato.cantidad_original).formatMoney(3, '.', ','),
                            'cantidad': parseFloat(item.cantidad).formatMoney(3, '.', ','),
                            'importe': parseFloat(item.cantidad * item.precio_unitario).formatMoney(3, '.', ','),
                            'estimaAnterior': parseFloat(estimaAnterior).formatMoney(3, '.', ','),
                            'anteriorImporte': parseFloat(estimaAnterior * item.precio_unitario).formatMoney(3, '.', ','),
                            'acumulada': (parseFloat(estimaAnterior) + parseFloat(item.cantidad)).formatMoney(3, '.', ','),
                            'acumuladaImporte': (parseFloat(estimaAnterior) + parseFloat(item.cantidad) * item.precio_unitario).formatMoney(3, '.', ','),
                            'cantidad_estimar': parseFloat((item.contrato.cantidad_original) - (parseFloat(estimaAnterior) + parseFloat(item.cantidad))).formatMoney(3, '.', ','),
                            'importe_estimar': parseFloat(((item.contrato.cantidad_original) - (parseFloat(estimaAnterior) + parseFloat(item.cantidad))) * item.precio_unitario).formatMoney(3, '.', ','),
                        });
                        this.totales();
                    });
                    ;

                });
            },
            totales() {
                this.contratoSuma = this.suma_contrato.formatMoney(3, '.', ',');
                this.sumaAnterior = this.suma_anteriorAcumulada.formatMoney(3, '.', ',');
                this.sumaEstimacion = this.suma_estimacion.formatMoney(3, '.', ',');
                this.sumaAcumulada = this.suma_acumulada.formatMoney(3, '.', ',');
                this.sumaPorEstimar = this.suma_porEstimar.formatMoney(3, '.', ',');
            }


        },
        computed: {
            estimacion() {
                this.estimaciones = this.$store.getters['contratos/estimacion/currentEstimacion']
                return this.$store.getters['contratos/estimacion/currentEstimacion']
            },


        }
    }
</script>

<style scoped>

</style>
