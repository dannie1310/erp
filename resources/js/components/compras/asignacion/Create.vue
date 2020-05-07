<template>
    <span>
        <div class="row">
            <div class="col-12">
                <div class="invoice p-3 mb-3">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <label for="id_solicitud">Seleccionar Solicitud de Compra:</label>
                                    <model-list-select
                                        :disabled="cargando"
                                        name="id_solicitud"
                                        option-value="id"                                                               
                                        v-model="id_solicitud"
                                        option-text="observaciones"
                                        :list="solicitudes"
                                        :placeholder="!cargando?'Seleccionar o buscar material por descripcion':'Cargando...'"
                                        :isError="errors.has(`id_solicitud`)">
                                    </model-list-select>
                                <div style="display:block" class="invalid-feedback" v-show="errors.has('id_solicitud')">{{ errors.first('id_solicitud') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Registrar</button>
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
            solicitudes:[],
            cotizaciones:null,
            id_solicitud:'',
        }
    },
    mounted() {
        this.getSolicitudes();
    },
    computed: {
        
    },
    methods: {
        getSolicitudes(){
            this.cargando = true;
            this.solicitudes = [];
            this.cotizaciones = null;
            return this.$store.dispatch('compras/solicitud-compra/index', {
                params: {
                    scope: ['cotizacion'],
                    limit: 50,
                    order: 'DESC',
                    sort: 'numero_folio'
                }
            })
            .then(data => {
                this.solicitudes = data.data;
                this.cargando = false;
            })

        },
        getCotizaciones(id){
            this.cargando = true;
            this.cotizaciones = null;
            return this.$store.dispatch('compras/solicitud-compra/getCotizaciones', {
                id: id,
                params: {}
            })
            .then(data => {
                this.cotizaciones = data;
            })
            .finally(() => {
                this.cargando = false;
            })
        },
    },
    watch:{
        id_solicitud(value){
            if(value != ''){
                this.getCotizaciones(value);
            }
        }
    }
}
</script>
<style>

</style>