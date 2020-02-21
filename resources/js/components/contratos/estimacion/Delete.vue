<template>
    <span>
    <div class="d-flex flex-row-reverse">
        <div class="p-2">
            <Resumen v-bind:id="id_estimacion"></Resumen>
        </div>
    </div>
    <div class="row">  
        <div class="col-md-12">
            <div class="invoice p-3 mb-3">
                <form role="form" @submit.prevent="validate">
                    <div v-if="estimacion" class="row">
                        <div class="card-body">
                            <div class="row mt-5 mb-5">
                                <div class="col-8">
                                    <div class="row">
                                        <div class="col-8 text-center mt-5">
                                            <h1>{{ obra.facturar }}</h1>
                                            <h6 class="mt-5">Cuerpo de Estimación</h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th scope="col">Folio SAO</th>
                                                <th class="text-right" scope="col">#{{ estimacion.estimacion.numero_folio.padStart(5,"0") }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <th scope="row">No. Estimación</th>
                                                <td class="text-right" v-if="estimacion.numEstimacion"> {{ estimacion.numEstimacion.NumeroFolioConsecutivo }}</td>
                                                <td class="text-right" v-else></td>
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
                                        <th class="text-center" scope="row">{{ estimacion.razon_social }}</th>
                                    </tr>
                                    <tr>
                                        <th>No. de Contrato: </th>
                                        <th class="text-center" scope="row">{{ estimacion.referencia }}</th>
                                    </tr>
                                </tbody>
                            </table>

                            <!-- Partidas--->
                            <div class="col-12 mt-5" >
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
                                        <template  v-for="(item,index) in estimacion.items">
                                            <tr v-if="!isObject(item)">
                                                <th>{{ identacionTabla(index) }} {{ item }}</th>
                                                <td class="text-center"></td>
                                                <td class="text-right"> </td>
                                                <td class="text-right"></td>
                                                <td class="text-right"></td>
                                                <td class="text-right"></td>
                                                <td class="text-right"></td>
                                                <td class="text-right"></td>
                                                <td class="text-right"></td>
                                                <td class="text-right"></td>
                                                <td class="text-right"></td>
                                                <td class="text-right"></td>
                                                <td class="text-right"></td>
                                            </tr>
                                            <tr v-else>
                                                <td>{{ identacionItem(index.length/4) }} {{ item.concepto }}</td>
                                                <td class="text-center">{{ item.unidad }}</td>
                                                <td class="text-right">{{ parseFloat(item.precioUnitario).formatMoney(4,'.',',') }} </td>
                                                <td class="text-right">{{  parseFloat(item.cantidadContrato).formatMoney(4,'.',',') }}</td>
                                                <td class="text-right">{{  parseFloat(item.importeContrato).formatMoney(4,'.',',') }}</td>
                                                <td class="text-right">{{  parseFloat(item.cantidadEstimadoAnterior).formatMoney(4,'.',',')  }}</td>
                                                <td class="text-right">{{  parseFloat(item.importeEstimadoAnterior).formatMoney(4,'.',',') }}</td>
                                                <td class="text-right">{{  parseFloat(item.cantidadEstimacion).formatMoney(4,'.',',') }}</td>
                                                <td class="text-right">{{  parseFloat(item.importeEstimacion).formatMoney(4,'.',',')  }}</td>
                                                <td class="text-right">{{  parseFloat(item.cantidadAcumulado).formatMoney(4,'.',',') }}</td>
                                                <td class="text-right">{{  parseFloat(item.importeAcumulado).formatMoney(4,'.',',') }}</td>
                                                <td class="text-right">{{  parseFloat(item.cantidadPorEstimar).formatMoney(4,'.',',') }} </td>
                                                <td class="text-right">{{  parseFloat(item.importePorEstimar).formatMoney(4,'.',',')  }}</td>
                                            </tr>
                                        </template>

                                        <!--Sumas totales de la s partidas-->
                                        <tr class="bg-dark">
                                            <th class="text-right">Subtotales Obra Ejecutada</th>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td class="text-right">{{estimacion.suma_contrato }}</td>
                                            <td></td>
                                            <td class="text-right"> {{ estimacion.suma_estimadoAnterior }}</td>
                                            <td></td>
                                            <td class="text-right"> {{ estimacion.suma_estimacion }}</td>
                                            <td></td>
                                            <td class="text-right"> {{ estimacion.suma_acumulado }}</td>
                                            <td> </td>
                                            <td class="text-right">{{ estimacion.suma_porEstimar }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row error-content">
                                        <label for="motivo" class="col-sm-2 col-form-label">Motivo:</label>
                                        <div class="col-sm-10">
                                            <textarea
                                                    name="motivo"
                                                    id="motivo"
                                                    class="form-control"
                                                    v-model="motivo"
                                                    v-validate="{required: true}"
                                                    data-vv-as="Motivo"
                                                    :class="{'is-invalid': errors.has('motivo')}"
                                            ></textarea>
                                            <div class="invalid-feedback" v-show="errors.has('motivo')">{{ errors.first('motivo') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="salir">Cerrar</button>
                        <button type="submit" class="btn btn-danger" :disabled="errors.count() > 0 || motivo == ''">Eliminar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </span>
</template>

<script>
import Resumen from './resumen/Show';
    export default {
        name: "estimacion-delete",
         components: {Resumen},
        props: ['id'],
        data() {
            return {
                cargando: false,
                logo: '',
                obra: this.$session.get('obra'),
                guiones:'\xa0\xa0',
                identacion:'',
                itemIdentacion:'',
                motivo: '',
                id_estimacion: this.$route.params.id,
            }
        },
        mounted() {
            this.$Progress.start();
            this.find(this.id_estimacion)
                .finally(() => {
                    this.$Progress.finish();
                });
        },
        methods: {
            find(id) {
                this.cargando = true;
                this.motivo = '';
                this.$store.commit('contratos/estimacion/SET_ESTIMACION', null);
                return this.$store.dispatch('contratos/estimacion/showEstimacionTable', {
                    id: id,
                }).then(data => {
                    this.$store.commit('contratos/estimacion/SET_ESTIMACION', data);
                    $(this.$refs.modal).modal('show');
                }).finally(() => {
                    this.cargando = false;
                })
            },
            isObject(item){
                return typeof item === 'object'
            },

            identacionTabla(val){
                var cant=val.length/4;

                return this.guiones.repeat(cant-1);

            },
            identacionItem(val){

                return this.guiones.repeat(val-1);
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        if(this.motivo == '') {
                            swal('¡Error!', 'Debe colocar un motivo para realizar la operación.', 'error')
                        }
                        else {
                            this.eliminar()
                        }
                    }
                });
            },
            eliminar() {
                this.cargando = true;
                return this.$store.dispatch('contratos/estimacion/eliminar', {
                    id: this.$data.id_estimacion,
                    params: {data: this.$data.motivo}
                })
                    .then(data => {
                        this.$store.commit('contratos/estimacion/DELETE_ESTIMACION', {id: this.$data.id_estimacion})
                        this.$router.push({name: 'estimacion'});
                    })
                    .finally( ()=>{
                        this.cargando = false;
                    });
            },
            salir(){
                this.$router.push({name: 'estimacion'});
            },
        },
        computed: {
            estimacion() {
                return this.$store.getters['contratos/estimacion/currentEstimacion']
            },


        }
    }
</script>

<style scoped>

</style>
