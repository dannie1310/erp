<template>
    <span>
        <br>
        <div class="row" v-if="!cargando">
            <hr>
            <div class="col-md-12 table-responsive" v-if="poliza && poliza.cfdi.data.length>0">
                <strong>
                    Lista de CFDI´s Asociados
                </strong>
                <table class="table table-sm table-fs-sm table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 25px">#</th>
                            <th style="width: 15px">T</th>
                            <th style="width: 65px">Fecha</th>
                            <th style="width: 250px">Razón Social</th>
                            <th style="width: 200px">UUID</th>
                            <th style="width: 20px">Serie</th>
                            <th style="width: 80px">Folio</th>
                            <th>Conceptos</th>
                            <th style="width: 50px">IVA</th>
                            <th style="width: 90px">Monto</th>
                            <th style="width: 15px"></th>
                        </tr>
                    </thead>
                    <tbody v-if="poliza.cfdi.data.length > 0">
                        <tr v-for="(cfdi, i) in poliza.cfdi.data" >
                            <td>{{parseInt(i)+1}}</td>
                            <td>{{cfdi.tipo_comprobante}}</td>
                            <td>{{cfdi.fecha_corta_format}}</td>
                            <td>{{cfdi.proveedor.razon_social}}</td>
                            <td>
                                <CFDI v-if="cfdi" v-bind:txt="cfdi.uuid" v-bind:id="cfdi.id" @click="cfdi.id" ></CFDI>
                            <td>{{cfdi.serie}}</td>
                            <td>
                                {{cfdi.folio}}
                            </td>
                            <td>{{cfdi.conceptos_txt}}</td>
                            <td style="text-align: right">{{parseFloat(cfdi.iva).formatMoney(2)}}</td>
                            <td style="text-align: right;">
                                {{parseFloat(cfdi.total).formatMoney(2)}}
                            </td>
                            <td style="text-align: center">
                                <button type="button" class="btn btn-sm btn-danger" title="Desasociar"
                                        @click="desasociar(cfdi.id)"
                                        v-if="para_eliminar && ($root.can('asociar-cfdi-a-poliza-contpaq',true) || $root.can('asociar-cfdi-a-poliza-contpaq-desde-sao'))">
                                    <i class="fa fa-unlink"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                    <tbody v-else>
                        <tr style="background-color: lightgrey">
                            <td colspan="11" style="text-align: center; font-size: 12px; font-style: italic">
                                <strong>Sin CFDI Asociados</strong>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </span>
</template>

<script>


import CFDI from "../../../fiscal/cfd/cfd-sat/CFDI.vue";

export default {
    name: "poliza-contpaq-lista-cfdi-asociados",
    props : ['id','para_eliminar'],
    components: {CFDI},
    data() {
        return {
            cargando :false,
            cfdi_store : [],
        }
    },
    mounted() {
    },
    methods :{
        desasociar(id) {
            this.cargando = true;
            return this.$store.dispatch('contabilidadGeneral/poliza/desasociarCFDI', {
                id_empresa: this.idEmpresaContabilidad,
                id_cfdi: id,
                id_poliza: this.poliza.id
            })
            .then(data => {
                this.$store.commit('contabilidadGeneral/poliza/SET_POLIZA', data);
            })
            .finally( ()=>{
                this.cargando = false;
            });
        },
    },
    watch: {
    },
    computed: {
        poliza(){
            return this.$store.getters['contabilidadGeneral/poliza/currentPoliza'];
        },
        idEmpresaContabilidad() {
            return this.$store.getters['auth/idEmpresaContabilidad']
        }
    },
}
</script>
<style scoped>

table.table-fs-sm{
    font-size: 10px;
}

</style>
