<template>
    <span>
        <div class="row">
             <div class="col-md-4">
                 <div class="form-group row error-content">
                     <label for="id_empresa" class="col-md-4 col-form-label">Tipo de búsqueda:</label>
                     <div class="col-md-8">
                         <select class="form-control"
                                 name="tipo_busqueda"
                                 data-vv-as="Tipo de Búsqueda"
                                 v-model="tipo_busqueda"
                                 v-validate="{required: true}"
                                 :error="errors.has('tipo_busqueda')"
                                 id="tipo_busqueda">
                             <option value selected>-- Seleccione --</option>
                             <option value="1">Individual vs Consolidada</option>
                             <option value="2">Individual vs Individual Histórica</option>
                             <option value="3">Consolidada vs Consolidada Histórica</option>
                         </select>
                     </div>
                 </div>
             </div>
             <div class="col-md-1">
                 <button @click="buscar" class="btn btn-primary float-right">
                        <i class="fa fa-search"></i> Buscar
                 </button>
             </div>
         </div>
        <span >
            <h6><i class="fa fa-arrow-circle-right"></i><b>Resultado de la búsqueda</b></h6>
            <div class="table-responsive">
                <table style="width: 100%" class="table table-stripped">
                    <tbody>
                        <tr>
                            <th style="text-align: left" >Duración de búsqueda (segundos):</th>
                            <td style="text-align: right">{{resultado.duracion}}</td>
                        </tr>
                        <tr>
                            <th style="text-align: left" >Núm. pólizas leidas:</th>
                            <td style="text-align: right">{{resultado.polizas_leidas}}</td>
                        </tr>
                        <tr>
                            <th style="text-align: left" >Núm. pólizas no encontradas:</th>
                            <td style="text-align: right">{{resultado.polizas_no_encontradas}}</td>
                        </tr>
                        <tr>
                            <th style="text-align: left" >Núm. pólizas con diferencias:</th>
                            <td style="text-align: right">{{resultado.polizas_con_diferencias}}</td>
                        </tr>
                    </tbody>
                </table>
           </div>
        </span>
    </span>
</template>

<script>
    export default {
        name: "DetectarIncidente",
        data() {
            return {
                procesado : false,
                procesando : false,
                resultado : [],
                tipo_busqueda : '',
            }
        },
        methods:{
            buscar(){
                this.procesando = true;
                return this.$store.dispatch('contabilidadGeneral/incidente-poliza/buscar',
                    this.$data
                        /*data: [this.$data],
                        config: {
                            params: { _method: 'POST'}
                        }*/
                    )
                    .then(data => {
                        this.resultado = data;
                        this.procesado = true;
                    }).finally(() => {
                        this.procesando = false;
                    });
            },
        }
    }
</script>

<style scoped>

</style>