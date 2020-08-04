<template>
    <span>
        <div class="row">
            <div class="col-12">
                <div class="invoice p-3 mb-3">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <label for="id_solicitud">Seleccionar Contrato Proyectado:</label>
                                    <model-list-select
                                        :disabled="cargando"
                                        name="id_contrato"
                                        option-value="id_transaccion"                                                               
                                        v-model="id_contrato"
                                        :custom-text="numeroFolioFormatAndObservaciones"
                                        :list="contratos"
                                        :placeholder="!cargando?'Seleccionar o buscar Contrato Proyectado':'Cargando...'"
                                        :isError="errors.has(`id_contrato`)">
                                    </model-list-select>
                                <div style="display:block" class="invalid-feedback" v-show="errors.has('id_contrato')">{{ errors.first('id_contrato') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12" v-if="data">
                <div class="invoice p-3 mb-3">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-12 table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
import {ModelListSelect} from 'vue-search-select';
export default {
    name: "asignacion-proveedores-create",
    components: {ModelListSelect},
    data() {
        return {
            cargando: false,
            contratos:[],
            id_contrato:'',
            data:null,
        }
    },
    mounted() {
        this.getContratos();
    },
    computed: {
        
    },
    methods: {
        numeroFolioFormatAndObservaciones(item){
            return `[${item.folio}] - [${item.referencia}]`
        },
        getContratos(){
            this.cargando = true;
            return this.$store.dispatch('contratos/contrato-proyectado/getContratos', {
                params: this.query
            })
            .then(data => {
                this.contratos =  data;
            })
            .finally(() => {
                this.cargando = false;
            })
        },
        getCotizaciones(id){
            this.cargando = true;
            this.data = null;
            return this.$store.dispatch('contratos/contrato-proyectado/getCotizaciones', {
                id: id,
                params: {}
            })
            .then(data => {
                // this.id_empresa = Object.keys(data.cotizaciones)[0];
                this.data = data;
            })
            .finally(() => {
                this.cargando = false;
            })
        },
        validate() {
            this.$validator.validate().then(result => {
                if (result){
                    this.store();
                }
            });
        }
    },
    watch:{
        id_contrato(value){
            if(value != ''){
                this.getCotizaciones(value);
            }
        }
    }


}
</script>

<style>

</style>