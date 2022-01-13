<template>
    <span>
        <button @click="load()" type="button" class="btn btn-outline-success" :disabled="cargando" title="Cargar Layout">
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
                    <form role="form" @submit.prevent="validate">

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
                </div>
            </div>
        </div>

        <div class="modal fade" ref="modal_datos" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-xl modal" role="document" style="height:auto">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle2"> <i class="fa fa-upload"></i> CARGAR LAYOUT DE ESTIMACIÓN</h5>
                        <button type="button" class="close" @click="cerrarModalResumen" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form">
                         <div class="modal-body">
                                <div class="col-md-12">
                                    <h4>{{datos_archivo.contratista}}</h4>
                                </div>
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="col-form-label">Fecha de Estimación</label>
                                            {{datos_archivo.fecha_estimacion}}
                                        </div>
                                        <div class="col-md-4">
                                            <label class="col-form-label">Fecha Inicio de Estimación</label>
                                            {{datos_archivo.fecha_inicio_estimacion}}
                                        </div>
                                        <div class="col-md-4">
                                            <label class="col-form-label">Fecha Término de Estimación</label>
                                            {{datos_archivo.fecha_fin_estimacion}}
                                        </div>
                                    </div>
                                    
                                </div>
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label class="col-form-label">Observaciones: </label>
                                            {{datos_archivo.observaciones}}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-body" >
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
                                            <div class="pull-right" v-if="datos_archivo.partidas_invalidas" style="color:red;">Partidas con volumen no válido o mayor al saldo</div>
                                            <div class=" table-responsive" style="overflow-y: auto; height:250px">
                                                <table id="tabla-conceptos">
                                                    <thead>
                                                        <tr>
                                                            <th rowspan="2">#</th>
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
                                                            <td>{{concepto.no_partida}}</td>
                                                            <td :title="concepto.clave">{{ concepto.clave }}</td>
                                                            <td :title="concepto.descripcion_concepto">
                                                                <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                                                                {{concepto.descripcion_concepto}}
                                                            </td>
                                                            <td class="centrado">{{concepto.unidad}}</td>
                                                            <td style="display: none" class="numerico contratado">{{ parseFloat(concepto.cantidad_subcontrato).formatMoney(2) }}</td>
                                                            <td style="display: none" class="numerico contratado">${{ parseFloat(concepto.precio_unitario_subcontrato).formatMoney(2) }}</td>
                                                            <td style="display: none" class="numerico avance-volumen"></td>
                                                            <td style="display: none" class="numerico avance-volumen">{{ parseFloat(concepto.cantidad_estimada_anterior).formatMoney(2) }}</td>
                                                            <td style="display: none" class="numerico avance-volumen">{{ parseFloat(concepto.porcentaje_avance).formatMoney(2) }}</td>
                                                            <td style="display: none" class="numerico avance-importe"></td>
                                                            <td style="display: none" class="numerico avance-importe">${{ parseFloat(concepto.importe_estimado_anterior).formatMoney(2) }}</td>
                                                            <td style="display: none" class="numerico saldo">{{  parseFloat(concepto.cantidad_por_estimar).formatMoney(2) }}</td>
                                                            <td style="display: none" class="numerico saldo">${{ parseFloat(concepto.importe_por_estimar).formatMoney(2) }}</td>
                                                            <template v-if="concepto.cantidad_valida"><td class="numerico">{{parseFloat(concepto.cantidad).formatMoney(2)}}</td></template>
                                                            <template v-else><td class="numerico" style="color:red">{{concepto.cantidad}}</td></template>

                                                            <template v-if="concepto.cantidad_valida"><td class="numerico">{{parseFloat(concepto.porcentaje_estimado).formatMoney(2)}}</td></template>
                                                            <template v-else-if="concepto.porcentaje_estimado == 'N/V'"><td class="numerico" style="color:red">N/V</td></template>
                                                            <template v-else><td class="numerico" style="color:red">{{parseFloat(concepto.porcentaje_estimado).formatMoney(2)}}</td></template>

                                                            <td class="numerico">{{ concepto.precio_unitario_subcontrato_format}}</td>

                                                            <template v-if="concepto.cantidad_valida"><td class="numerico">${{parseFloat(concepto.importe).formatMoney(2)}}</td></template>
                                                            <template v-else-if="concepto.importe == 'N/V'"><td class="numerico" style="color:red">N/V</td></template>
                                                            <template v-else><td class="numerico" style="color:red">${{parseFloat(concepto.importe).formatMoney(2)}}</td></template>

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
                            <button type="button" class="btn btn-secondary" @click="cerrarModalResumen">
                                <i class="fa fa-times"></i>
                                Cerrar
                            </button>
                            <button class="btn btn-primary float-right" type="button" @click="guardar" :disabled="datos_archivo.partidas_invalidas">
                                <i class="fa fa-save"></i>
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
                columnas: [],
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
                this.file = null;
                this.$validator.errors.clear();

                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
            },
            cerrarModal() {
                this.file = null;
                this.$validator.errors.clear();
                $(this.$refs.modal).modal('hide')
            },
            cerrarModalResumen(){
                $(this.$refs.modal_datos).modal('hide');
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
                        this.columnas= [];
                        this.datos_archivo = data;
                        $(this.$refs.modal_datos).appendTo('body')
                        $(this.$refs.modal_datos).modal('show');

                    }).finally(() => {
                        this.$refs.carga_layout.value = '';
                        this.file = null;
                        this.file_name = '';
                        this.$validator.errors.clear();
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
                var conceptos = this.getConceptos();
                let data ={
                    id_antecedente: this.datos_archivo.id,
                    fecha: moment(this.datos_archivo.fecha_estimacion, 'DD/MM/YYYY').format('YYYY-MM-DD'),
                    cumplimiento: moment(this.datos_archivo.fecha_inicio_estimacion, 'DD/MM/YYYY').format('YYYY-MM-DD'),
                    vencimiento:  moment(this.datos_archivo.fecha_fin_estimacion, 'DD/MM/YYYY').format('YYYY-MM-DD'),
                    referencia: this.datos_archivo.referencia,            
                    conceptos: conceptos
                };
                this.datos_archivo.observaciones!=''?data.observaciones = this.datos_archivo.observaciones:'';
                return this.$store.dispatch('contratos/estimacion/store', data)
                .then(data=> {
                    this.cerrarModal();
                    this.cerrarModalResumen();
                    this.$router.push({name: 'estimacion'});
                }).finally(()=>{
                });
            },
            getConceptos() {
                var partidas = this.datos_archivo.partidas;
        		var conceptos = [];
                Object.values(partidas).forEach(partida =>{
                    if (parseFloat(partida.cantidad) !== 0) {
                        conceptos.push({
                            item_antecedente: partida.id_concepto,
                            id_concepto: partida.id_destino,
                            importe: partida.importe,
                            cantidad: partida.cantidad
                        })
                    }
                });
                // for (var key in partidas) {
                //     var obj = partidas[key];
                //     if (parseFloat(obj.cantidad) !== 0) {
                //         conceptos.push({
                //             item_antecedente: obj.id_concepto,
                //             id_concepto: obj.id_destino,
                //             importe: obj.importe,
                //             cantidad: obj.cantidad
                //         })
                //     }
                // }
				return conceptos;
			},
        },
        watch: {
            columnas(val) {
                $('.contratado').css('display', 'none');
                $('.avance-volumen').css('display', 'none');
                $('.avance-importe').css('display', 'none');
                $('.saldo').css('display', 'none');
                $('.destino').css('display', 'none');

                val.forEach(v => {
                    $('.' + v).removeAttr('style')
                })
            },
        }
    }
</script>

<style scoped>
    .scrolling {
        overflow-y: auto;
    }

    table#tabla-conceptos {
        word-wrap: unset;
        width: 100%;
        background-color: white;
        border-color: transparent;
        border-collapse: collapse;
        clear: both;
    }

    table thead th
    {
        padding: 0.2em;
        border: 1px solid #666;
        background-color: #333;
        color: white;
        font-weight: normal;
        overflow: hidden;
        text-align: center;
    }

    table thead th {
        text-align: center;
    }
    table tbody tr
    {
        border-width: 0 1px 1px 1px;
        border-style: none solid solid solid;
        border-color: white #CCCCCC #CCCCCC #CCCCCC;
    }
    table tbody td,
    table tbody th
    {
        border-right: 1px solid #ccc;
        color: #242424;
        line-height: 20px;
        overflow: hidden;
        padding: 1px 5px;
        text-align: left;
        text-overflow: ellipsis;
        -o-text-overflow: ellipsis;
        -ms-text-overflow: ellipsis;
        white-space: nowrap;
    }

    table col.clave { width: 120px; }
    table col.icon { width: 25px; }
    table col.monto { width: 115px; }
    table col.pct { width: 60px; }
    table col.unidad { width: 80px; }
    table col.clave  {width: 100px; }

    table tbody td input.text
    {
        border: none;
        padding: 0;
        margin: 0;
        width: 100%;
        background-color: transparent;
        font-family: inherit;
        font-size: inherit;
        font-weight: bold;
    }

    table tbody .numerico
    {
        text-align: right;
        padding-left: 0;
        white-space: normal;
    }

    .text.is-invalid {
        color: #dc3545;
    }

    table tbody td input.text {
        text-align: right;
    }
</style>
