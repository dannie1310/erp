<template>
    <span>
        <div class="row">
            <div class="col-md-12">
                <div class="invoice p-3 mb-3">
                    <div class="modal-body">
                        <div class="row">
                            <label for="id_empresa"  class="col-md-3">Seleccionar :</label>
                            <div class="col-md-9">
                                <model-list-select
                                    name="id_empresa"
                                    placeholder="Seleccionar o buscar por RFC o por razón social"
                                    data-vv-as="Orden de Compra"
                                    v-model="id_empresa"
                                    option-value="id"
                                    :custom-text="rfcAndRazonSocial"
                                    :list="empresas"
                                >
                                </model-list-select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="invoice p-3 mb-3">
                    <div class="modal-body">
                        <div class="row">
                            <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-th"></i> CONSULTA DE CUENTA BANCARIA</h5>
                            <label class="col-md-12">Empresa Unificadora :</label>
                            <label class="col-md-2">R.F.C. :</label>
                            <label class="col-md-12">Empresa Unificadora :</label>
                            <label class="col-md-12">Empresa Unificadora :</label>
                            <label class="col-md-12">Empresa Unificadora :</label>


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
    name: "unificacion-proveedores-create",
    components: {ModelListSelect},
    data() {
        return {
            cargando:false,
            empresas:[],
            id_empresa:'',
            detalle_unificacion:[],
        }
    },
    mounted() {
        this.getEmpresas();
    },
    methods: {
        getDetalleUnificacion(){
            this.cargando = true;
            this.detalle_unificacion = [];
            return this.$store.dispatch('cadeco/empresa/detalleUnificacion', {
                id:this.id_empresa,
                params: {
                    // scope: '',
                    sort: 'rfc',
                    order: 'desc'
                }  
            }).then(data => {
                this.detalle_unificacion = data.data;
            }).finally(()=>{
                this.cargando = false;
            })

        },
        getEmpresas(){
            this.cargando = true;
            return this.$store.dispatch('cadeco/empresa/index', {
                    params: {
                        // scope: '',
                        sort: 'rfc',
                        order: 'desc'
                    }  
                }).then(data => {
                    this.empresas = data.data;
                }).finally(()=>{
                    this.cargando = false;
                })
        },
        rfcAndRazonSocial (item){
            return `[${item.rfc}] - ${item.razon_social}`
        },

    },
    watch:{
        id_empresa(value){
            if(value != ''){
                this.getDetalleUnificacion();
            }
        }
    },

}
</script>

<style>

</style>