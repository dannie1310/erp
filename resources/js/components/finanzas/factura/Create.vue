<template>
    <div class="row">
        <div class="col-md-12" >
            <div class="invoice p-3 mb-3">
                <form role="form" @submit.prevent="validate">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group error-content">
                                    <label for="fecha">Fecha:</label>
                                    <datepicker v-model = "dato.fecha"
                                                name = "fecha"
                                                :format = "formatoFecha"
                                                :language = "es"
                                                :bootstrap-styling = "true"
                                                class = "form-control"
                                                v-validate="{required: true}"
                                                :disabled-dates="fechasDeshabilitadas"
                                                :class="{'is-invalid': errors.has('fecha')}"
                                    ></datepicker>
                                    <div class="invalid-feedback" v-show="errors.has('fecha')">{{ errors.first('fecha') }}</div>
                                </div>
                            </div>
                        </div>
                    <div class="row">
                        <div class="col-md-10">
                            <div class="form-group error-content">
                                <label for="fecha">Empresa:</label>
                                <model-list-select
                                        name="id_empresa"
                                        placeholder="Seleccionar o buscar por RFC y razón social"
                                        data-vv-as="Empresa"
                                        v-validate="{required: true}"
                                        v-model="dato.id_empresa"
                                        option-value="id"
                                        :custom-text="rfcAndRazonSocial"
                                        :list="empresas"
                                        :isError="errors.has(`id_empresa`)">
                                </model-list-select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group error-content">
                                <label for="fecha">Moneda:</label>

                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!--Referencia-->
                        <div class="col-md-6">
                            <div class="form-group error-content">
                                <label for="referencia">Referencia:</label>
                                <div class="col-sm-10">
                                    <input class="form-control"
                                           style="width: 100%"
                                           placeholder="Referencia"
                                           name="referencia"
                                           id="referencia"
                                           data-vv-as="Referencia"
                                           v-validate="{required: true}"
                                           v-model="dato.referencia"
                                           :class="{'is-invalid': errors.has('referencia')}"
                                    >
                                    <div class="invalid-feedback" v-show="errors.has('referencia')">{{ errors.first('referencia') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>




                </form>
            </div>
        </div>
    </div>
</template>

<script>
    import datepicker from 'vuejs-datepicker';
    import {es} from 'vuejs-datepicker/dist/locale';
    import {ModelListSelect} from 'vue-search-select';
    export default {
        name: "Create",
        components: {datepicker,ModelListSelect},
        data() {
            return {
                es:es,
                fechasDeshabilitadas:{},
                empresas:[],
                dato:{
                    con_prestamo: 0,
                    folio_vale: '',
                    opcion_cargo: 1,
                    id_concepto:'',
                    fecha:'',
                    id_almacen:'',
                    id_empresa:'',
                    opciones:1,
                    referencia:'',
                    observaciones:'',
                    partidas:[]
                },
            }
        },
        init() {
            this.cargando = true;
        },
        mounted() {
            this.getEmpresas();
            this.dato.fecha = new Date();
            this.fechasDeshabilitadas.from= new Date();
        },
        methods:{
            rfcAndRazonSocial (item){
                return `[${item.rfc}] - ${item.razon_social}`
            },
            formatoFecha(date){
                return moment(date).format('DD/MM/YYYY');
            },
            getEmpresas() {
                this.cargando =true;
                return this.$store.dispatch('cadeco/empresa/index', {
                    params: {sort: 'razon_social', order: 'asc', scope:'Contratista' }
                })
                    .then(data => {
                        this.empresas = data.data;
                        this.cargando = false;
                    })
            },
        }
    }
</script>

<style scoped>

</style>