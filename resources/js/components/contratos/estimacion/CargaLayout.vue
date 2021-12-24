<template>
    <span>
        <button @click="load()" type="button" class="btn btn-success" :disabled="cargando" title="Cargar Layout">
            <i class="fa fa-upload" v-if="!cargando"></i>
            <i class="fa fa-spinner fa-spin" v-else></i>
            Cargar Layout
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-upload"></i> CARGAR LAYOUT DE ESTIMACIÓN</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate" v-if="datos_archivo != []">

                        <div class="modal-body">
                            <div class="row justify-content-between">
                                <div class="col-md-12">
                                     <label for="carga_layout" class="col-lg-12 col-form-label">Cargar Layout</label>
                                    <div class="col-lg-12">
                                        <input type="file" class="form-control" id="carga_layout"
                                               @change="onFileChange"
                                               row="3"
                                               v-validate="{ ext: ['xlsx']}"
                                               name="carga_layout"
                                               data-vv-as="Layout"
                                               ref="carga_layout"
                                               :class="{'is-invalid': errors.has('carga_layout')}">
                                        <div class="invalid-feedback" v-show="errors.has('carga_layout')">{{ errors.first('carga_layout') }} (xlsx)</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="cerrarModal">
                                <i class="fa fa-times-circle"></i>
                                Cerrar
                            </button>
                            <button type="button" class="btn btn-primary" @click="validate" :disabled="!file">
                                <i class="fa fa-upload"></i>
                                Cargar</button>
                        </div>
                    </form>
                    <form role="form" v-else>
                         <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <h1>{{datos_archivo.contratista}}</h1>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-3">
                                        <label class="col-form-label">Fecha de Estimación</label>
                                        {{datos_archivo.fecha_estimacion}}
                                    </div>
                                    <div class="col-md-3">
                                        <label class="col-form-label">Fecha Inicio de Estimación</label>
                                        {{datos_archivo.fecha_inicio_estimacion}}
                                    </div>
                                    <div class="col-md-3">
                                        <label class="col-form-label">Fecha Término de Estimación</label>
                                        {{datos_archivo.fecha_fin_estimacion}}
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="card" v-if="!cargando">
                                        <div class="card-body">
                                            <div class="form-check form-check-inline">
                                                <input v-model="columnas" class="form-check-input" type="checkbox" value="contratado" id="contratado">
                                                <label class="form-check-label" for="contratado">Contratado</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input v-model="columnas" class="form-check-input" type="checkbox" id="avance-volumen" value="avance-volumen">
                                                <label class="form-check-label" for="avance-volumen">Avance Volumen</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input v-model="columnas" class="form-check-input" type="checkbox" id="avance-importe" value="avance-importe">
                                                <label class="form-check-label" for="avance-importe">Avance Importe</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input v-model="columnas" class="form-check-input" type="checkbox" id="saldo" value="saldo">
                                                <label class="form-check-label" for="saldo">Saldo</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input v-model="columnas" class="form-check-input" type="checkbox" id="destino" value="destino">
                                                <label class="form-check-label" for="destino">Destino</label>
                                            </div>
                                        </div>
		                            </div>
                                    <div class="card">
                                        <div class="card-body table-responsive">
                                            <table id="tabla-conceptos">
                                                <thead>
                                                    <tr>
                                                        <th rowspan="2">Clave</th>
                                                        <th rowspan="2">Concepto</th>
                                                        <th rowspan="2">UM</th>
                                                        <th style="display: none" colspan="2" class="contratado">Contratado</th>
                                                        <th style="display: none" colspan="3" class="avance-volumen">Avance Volumen</th>
                                                        <th style="display: none" colspan="2" class="avance-importe">Avance Importe</th>
                                                        <th style="display: none" colspan="2" class="saldo">Saldo</th>
                                                        <th colspan="4">Esta Estimación</th>
                                                        <th style="display: none" class="destino">Distribución</th>
                                                    </tr>
                                                    <tr>
                                                        <th style="display: none" class="contratado">Volumen</th>
                                                        <th style="display: none" class="contratado">P.U.</th>
                                                        <th style="display: none" class="avance-volumen">Anterior</th>
                                                        <th style="display: none" class="avance-volumen">Acumulado</th>
                                                        <th style="display: none" class="avance-volumen">%</th>
                                                        <th style="display: none" class="avance-importe">Anterior</th>
                                                        <th style="display: none" class="avance-importe">Acumulado</th>
                                                        <th style="display: none" class="saldo">Volumen</th>
                                                        <th style="display: none" class="saldo">Importe</th>
                                                        <th>Volumen</th>
                                                        <th>%</th>
                                                        <th>P.U.</th>
                                                        <th>Importe</th>
                                                        <th style="display: none" class="destino">Destino</th>
                                                    </tr>
                                                </thead>
                                                <tbody v-for="(concepto, i) in datos_archivo.partidas">
                                                    <tr>
                                                        <td :title="concepto.clave">{{ concepto.clave }}</td>
                                                        <td :title="concepto.descripcion_concepto">
                                                            <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                                                            {{concepto.descripcion_concepto}}
                                                        </td>
                                                        <td class="centrado">{{concepto.unidad}}</td>
                                                        <td style="display: none" class="numerico contratado">{{ parseFloat(concepto.cantidad_subcontrato).formatMoney(2) }}</td>
                                                        <td style="display: none" class="numerico contratado">{{ parseFloat(concepto.precio_unitario_subcontrato).formatMoney(2) }}</td>
                                                        <td style="display: none" class="numerico avance-volumen"></td>
                                                        <td style="display: none" class="numerico avance-volumen">{{ parseFloat(concepto.cantidad_estimada_anterior).formatMoney(2) }}</td>
                                                        <td style="display: none" class="numerico avance-volumen">{{ parseFloat(concepto.porcentaje_avance).formatMoney(2) }}</td>
                                                        <td style="display: none" class="numerico avance-importe"></td>
                                                        <td style="display: none" class="numerico avance-importe">{{ parseFloat(concepto.importe_estimado_anterior).formatMoney(2) }}</td>
                                                        <td style="display: none" class="numerico saldo">{{  parseFloat(concepto.cantidad_por_estimar).formatMoney(2) }}</td>
                                                        <td style="display: none" class="numerico saldo">{{ parseFloat(concepto.importe_por_estimar).formatMoney(2) }}</td>
                                                        <td class="numerico">{{parseFloat(concepto.cantidad_estimacion).formatMoney(2)}}</td>
                                                        <td class="numerico">{{parseFloat(concepto.porcentaje_estimado).formatMoney(2)}}</td>
                                                        <td class="numerico">{{ concepto.precio_unitario_subcontrato_format}}</td>
                                                        <td class="numerico">{{parseFloat(concepto.importe_estimacion).formatMoney(2)}}</td>
                                                        <td style="display: none" class="destino" :title="concepto.destino_path">{{ concepto.destino_path }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="cerrarModal">
                                <i class="fa fa-times-circle"></i>
                                Cerrar
                            </button>
                            <button type="button" class="btn btn-primary" @click="validate" :disabled="!file" v-if="datos_archivo != []">
                                <i class="fa fa-upload"></i>
                                Cargar
                            </button>
                            <button class="btn btn-info float-right" type="submit" @click="guardar" v-else>
                                Guardar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "cargar-layout",
        props: ['id'],
        data() {
            return {
                cargando: false,
                file: null,
                nombre: '',
                datos_archivo: []
            }
        },
        mounted(){
        },
        methods:{
            validate() {
                this.$validator.validate().then(result => {
                    if (result){
                        this.cargarLayout()
                    }else{
                        if(this.$refs.carga_layout.value !== ''){
                            this.$refs.carga_layout.value = '';
                            this.file = null;
                        }
                        this.$validator.errors.clear();
                        swal('¡Error!', 'Error archivos de entrada invalidos.', 'error')
                    }
                });
            },
            load() {
                this.$refs.carga_layout.value = '';
                this.file = null;
                this.$validator.errors.clear();

                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
            },
            cerrarModal(event) {
                this.$refs.carga_layout.value = '';
                this.file = null;
                this.$validator.errors.clear();
                $(this.$refs.modal).modal('hide')
            },
            cargarLayout(){

                var formData = new FormData();
                formData.append('file',  this.file);
                formData.append('id',  this.id);
                formData.append('name', this.nombre);

                return this.$store.dispatch('contratos/estimacion/cargaLayout',
                    {
                        data: formData,
                        config: {
                            params: { _method: 'POST'}
                        }
                    })
                    .then(data => {
                        this.datos_archivo = data;

                    }).finally(() => {
                        this.$refs.carga_layout.value = '';
                        this.file = null;
                        this.file_name = '';
                        this.$validator.errors.clear();
                        /*
                        setTimeout(() => {
                            $(this.$refs.modal).modal('hide');
                            this.$emit('back', this.data);
                        }, 100);
                        */
                    });
            },
            createImage(file, tipo) {
                var reader = new FileReader();
                var vm = this;

                reader.onload = (e) => {
                    vm.file = e.target.result;
                };
                reader.readAsDataURL(file);

            },
            onFileChange(e){
                this.file = null;
                var files = e.target.files || e.dataTransfer.files;
                if (!files.length)
                    return;
                    this.nombre = files[0].name;
                if(e.target.id == 'carga_layout') {
                    this.createImage(files[0]);
                }
            },
            guardar() {
                return this.$store.dispatch('contratos/estimacion/store', {
                    id_antecedente: this.datos_archivo.id,
                    fecha: moment(this.datos_archivo.fecha_estimacion).format('YYYY-MM-DD'),
                    cumplimiento: moment(this.datos_archivo.fecha_inicio_estimacion).format('YYYY-MM-DD'),
                    vencimiento:  moment(this.datos_archivo.fecha_fin_estimacion).format('YYYY-MM-DD'),
                    observaciones: this.datos_archivo.observaciones,
                    conceptos: this.datos_archivo.partidas
                })
                .then(data=> {
                    this.$router.push({name: 'estimacion-index'});
                    this.$router.push({name: 'estimacion'});
                })
            },
        }
    }
</script>

<style scoped>

</style>
