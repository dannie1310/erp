<template>
    <span>
        <div class="row">
            <div class="col-md-12">
                 <label><i class="fa fa-file-excel"></i> Archivo xlsx:</label>
            </div>
        </div>
        <form role="form" @submit.prevent="validate">
            <input type="file" class="form-control" id="carga_layout"
                   @change="onFileChange"
                   row="3"
                   v-validate="{ ext: ['xlsx']}"
                   name="carga_layout"
                   data-vv-as="Layout"
                   ref="carga_layout"
                   :class="{'is-invalid': errors.has('carga_layout')}"
            >
            <div class="invalid-feedback" v-show="errors.has('carga_layout')">{{ errors.first('carga_layout') }} (xlsx)</div>
        </form>
        <span v-if="solicitud_partidas.length > 0">
            <br />
            <div class="row" >
                <div class="col-md-12">
                    <h6>-Cantidad de Partidas: {{resumen.cantidad_partidas}} -Cantidad de Pólizas: {{resumen.cantidad_polizas_involucradas}} -Cantidad de Movimientos: {{resumen.cantidad_movimientos}} -Cantidad de Bases de Datos: {{resumen.cantidad_bases}} </h6>
                </div>
            </div>
            <div class="row" >
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th class="index_corto">#</th>
                                    <th class="fecha">Fecha</th>
                                    <th class="fecha">Tipo</th>
                                    <th class="fecha">Folio</th>
                                    <th class="money"></th>
                                    <th>Concepto</th>
                                    <th class="referencia_input">Referencia</th>
                                    <th class="index_corto">#BD</th>
                                    <th class="index_corto">#P</th>
                                    <th class="index_corto">#M</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template v-for="(partida, i) in solicitud_partidas">
                                    <tr style="background-color:rgba(0, 0, 0, 0.1)">
                                        <td>{{i+1}}</td>
                                        <td>{{partida.fecha_format}}</td>
                                        <td>{{partida.tipo_txt}}</td>
                                        <td>{{partida.folio}}</td>
                                        <td></td>
                                        <td>{{partida.concepto}}</td>
                                        <td>{{partida.referencia}}</td>
                                        <td>{{partida.cantidad_bases}}</td>
                                        <td>{{partida.polizas.length}}</td>
                                        <td>{{partida.cantidad_movimientos}}</td>
                                    </tr>
                                    <tr v-for="(poliza, j) in partida.polizas">
                                        <td></td>
                                        <td style="text-align: right">{{j+1}}</td>
                                        <td colspan="2">{{poliza.bd_contpaq}}</td>
                                        <td >{{poliza.monto_format}}</td>
                                        <td colspan="4">{{poliza.concepto}}</td>
                                        <td>{{poliza.movimientos.length}}</td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-secondary pull-right"  @click="regresar"><i class="fa fa-angle-left"></i>Regresar</button>
                        <button type="button" class="btn btn-success pull-right"  @click="store" v-if="solicitud_partidas.length > 0"><i class="fa fa-save"></i>Registrar</button>
                    </div>

                </div>
            </div>


        </span>


    </span>
</template>

<script>
    export default {
        name: "Create",
        data() {
            return {
                cargando: false,
                solicitud_partidas:[],
                resumen:[],
                file_solicitudes : null,
                file_solicitudes_name : '',
            }
        },
        methods:{
            store() {
                return this.$store.dispatch('contabilidadGeneral/solicitud-edicion-poliza/store', this.$data)
                    .then((data) => {
                        this.$router.push({name: 'solicitud-edicion-poliza'});
                    });
            },
            createImage(file, tipo) {
                var reader = new FileReader();
                var vm = this;

                reader.onload = (e) => {
                    vm.file_solicitudes = e.target.result;
                };
                reader.readAsDataURL(file);

            },

            onFileChange(e){
                this.file_solicitudes = null;
                var files = e.target.files || e.dataTransfer.files;
                if (!files.length)
                    return;
                this.file_solicitudes_name = files[0].name;
                this.createImage(files[0]);
                setTimeout(() => {
                    this.validate()
                }, 500);
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result){
                        if(this.$refs.carga_layout.value === ''){
                            swal('¡Error!', 'Seleccione un archivo.', 'warning')
                        }else{
                            this.cargarLayout()
                        }
                    }else{
                        if(this.$refs.carga_layout.value !== ''){
                            this.$refs.carga_layout.value = '';
                            this.file_solicitudes = null;
                        }
                        this.$validator.errors.clear();
                    }
                });
            },
            regresar() {
                this.$router.push({name: 'solicitud-edicion-poliza'});
            },
            cargarLayout(){
                this.cargando = true;
                var formData = new FormData();
                formData.append('solicitud',  this.file_solicitudes);
                formData.append('nombre_archivo',  this.file_solicitudes_name);
                return this.$store.dispatch('contabilidadGeneral/solicitud-edicion-poliza/cargarLayout',
                    {
                        data: formData,
                        config: {
                            params: { _method: 'POST'}
                        }
                    })
                    .then(data => {
                        console.log(data);
                        if(data.partidas.length > 0){
                            this.solicitud_partidas = data.partidas;
                            this.resumen = data.resumen;

                        }else{
                            if(this.$refs.carga_layout.value !== ''){
                                this.$refs.carga_layout.value = '';
                                this.file_solicitudes = null;
                            }
                            this.solicitud_partidas = [];
                            swal('Carga Masiva', 'Archivo de layout sin cambios válidos.', 'warning')
                        }
                    }).finally(() => {
                        this.cargando = false;
                    });
            },
        }
    }
</script>

<style scoped>

</style>