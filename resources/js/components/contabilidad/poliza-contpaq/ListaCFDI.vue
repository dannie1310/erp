<template>
    <span>
        <div class="row" v-if="!cargando">
            <hr>
            <div class="col-md-12 table-responsive">
                <strong>
                    Lista de posibles CFDI
                </strong>
                <table class="table table-sm table-fs-sm">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th style="width: 70px">Fecha</th>
                            <th style="width: 180px">Razón Social</th>
                            <th style="width: 200px">UUID</th>
                            <th>Serie</th>
                            <th>Folio</th>
                            <th>Conceptos</th>
                            <th>IVA</th>
                            <th>Monto</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(cfdi, i) in cfdis" :style="cfdi.grado_coincidencia ==3?`background-color : green`:cfdi.grado_coincidencia ==2?`background-color : lightgreen`:``">
                            <td bgcolor="">{{i +1}}</td>
                            <td>{{cfdi.fecha_cfdi}}</td>
                            <td>{{cfdi.razon_social}}</td>
                            <td>{{cfdi.uuid}}</td>
                            <td>{{cfdi.serie}}</td>
                            <td>{{cfdi.folio}}</td>
                            <td>{{cfdi.conceptos_txt}}</td>
                            <td style="text-align: right">{{parseFloat(cfdi.importe_iva).formatMoney(2)}}</td>
                            <td style="text-align: right">{{parseFloat(cfdi.total).formatMoney(2)}}</td>
                            <td style="text-align: center">
                                <div class="form-check form-check-inline">
                                    <label class="form-check-label" style="cursor:pointer" >
                                        <input class="form-check-input" type="checkbox" name="enviar" v-model="cfdi.seleccionado" value="1" >
                                    </label>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>

            </div>
        </div>
        <div v-else>
            <div class="row" v-if="cfdis.length == 0">
                <div class="col-md-12">
                    Sin CFDI detectados.
                </div>
            </div>
        </div>
    </span>
</template>

<script>


export default {
    name: "lista-cfdi-asociar",
    props : ['id', 'id_empresa'],
    data() {
        return {
            cfdis : {},
            cargando :false,
            monto : 0
        }
    },
    components: {},
    mounted() {
        this.find();
    },
    methods :{
        find()
        {
            this.cargando = true;
            return this.$store.dispatch('contabilidadGeneral/poliza/findCfdi', {
                id: this.id,
                params: {
                    id_poliza: this.id,
                    id_empresa: this.id_empresa
                }
            }).then(data => {
                this.cfdis = data;

            }).finally(() => {
                this.cargando = false;
            });
        }

    },
}
</script>
<style scoped>
table.table-fs-sm{
    font-size: 10px;
}
</style>
