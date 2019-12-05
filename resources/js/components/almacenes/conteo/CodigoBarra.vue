<template>
    <span>
        <button @click="init" v-if="$root.can('agregar_conteos_codigo_barra')" class="btn btn-app btn-info float-right" :disabled="cargando">
            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
            <i class="fa fa-barcode" v-else></i>
            Registro con Código
        </button>
        <div class="modal fade" ref="modal" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-barcode"></i> Registrar conteo con código de barras</h5>
                        <button type="button" class="close" @click="cerrar" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row error-content">
                                        <label for="barcodeValue" class="col-sm-4 col-form-label">Código de barras: </label>
                                        <div class="col-sm-8">
                                              <input
                                                      ref="barcodeValue"
                                                      name="barcodeValue"
                                                      data-vv-as="Codigo de Barra"
                                                      v-validate="{required: true}"
                                                      class="form-control"
                                                      id="barcodeValue"
                                                      placeholder="Escanear código de barras"
                                                      v-model="barcodeValue"
                                                      :class="{'is-invalid': errors.has('barcodeValue')}"
                                                      v-on:keyup.enter="transformer"
                                              />
                                              <barcode v-bind:value="barcodeValue">
                                              </barcode>
                                        </div>
                                    </div>
                                </div>
                           </div>
                            <form role="form" @submit.prevent="validate">

                            <div class="row" v-if="marbete && barcodeValue && tipoConteo">
                                <div class="col-md-12">
                                    <div class="form-group row error-content">
                                        <label for="id_marbete" class="col-sm-4 col-form-label">Marbete: </label>
                                        <div class="col-sm-8">
                                            <input
                                                    type="text"
                                                    name="id_marbete"
                                                    data-vv-as="Folio Marbete"
                                                    v-validate="{required: false}"
                                                    class="form-control"
                                                    id="id_marbete"
                                                    placeholder="marbete.folio_marbete"
                                                    v-model="marbete.folio_marbete"
                                                    :class="{'is-invalid': errors.has('id_marbete')}"
                                                    :disabled="true">
                                            <div class="invalid-feedback" v-show="errors.has('id_marbete')">{{ errors.first('id_marbete') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row" v-if="marbete && barcodeValue && tipoConteo">
                                <div class="col-md-12">
                                    <div class="form-group row error-content">
                                        <label for="tipo_conteo" class="col-sm-4 col-form-label">Número de Conteo: </label>
                                        <div class="col-sm-8">
                                            <input
                                                    type="text"
                                                    name="tipo_conteo"
                                                    data-vv-as="Tipo Conteo"
                                                    v-validate="{required: false}"
                                                    class="form-control"
                                                    id="tipo_conteo"
                                                    placeholder="Tipo Conteo"
                                                    v-model="tipoConteo.descripcion"
                                                    :class="{'is-invalid': errors.has('tipo_conteo')}"
                                                    :disabled="true">
                                            <div class="invalid-feedback" v-show="errors.has('tipo_conteo')">{{ errors.first('tipo_conteo') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                             <div class="row" v-if="marbete && barcodeValue && tipoConteo">
                                <div class="col-md-12">
                                    <div class="form-group row error-content">
                                        <label for="cantidad_nuevo" class="col-sm-4 col-form-label">Nuevos: </label>
                                        <div class="col-sm-8">
                                            <input
                                                    ref="input"
                                                    step="any"
                                                    type="number"
                                                    name="cantidad_nuevo"
                                                    data-vv-as="Cantidad Nuevos"
                                                    v-validate="{required: true}"
                                                    class="form-control"
                                                    id="cantidad_nuevo"
                                                    placeholder="Cantidad Nuevos"
                                                    v-model="dato.cantidad_nuevo"
                                                    :class="{'is-invalid': errors.has('cantidad_nuevo')}"
                                                    v-on:keyup.enter.prevent="validate">
                                            <div class="invalid-feedback" v-show="errors.has('cantidad_nuevo')">{{ errors.first('cantidad_nuevo') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row" v-if="marbete && barcodeValue && tipoConteo">
                                <div class="col-md-12" v-if="checkedMostrar">
                                    <div class="form-group row error-content">
                                        <label for="cantidad_usados" class="col-sm-4 col-form-label">Usados: </label>
                                        <div class="col-sm-8">
                                            <input
                                                    step="any"
                                                    type="number"
                                                    name="cantidad_usados"
                                                    data-vv-as="Cantidad Usados"
                                                    v-validate="{required: false}"
                                                    class="form-control"
                                                    id="cantidad_usados"
                                                    placeholder="Cantidad Usados"
                                                    v-model="dato.cantidad_usados"
                                                    :class="{'is-invalid': errors.has('cantidad_usados')}"
                                                    v-on:keyup.enter.prevent="validate">
                                            <div class="invalid-feedback" v-show="errors.has('cantidad_usados')">{{ errors.first('cantidad_usados') }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12" v-if="checkedMostrar">
                                    <div class="form-group row error-content">
                                        <label for="cantidad_inservible" class="col-sm-4 col-form-label">Inservibles: </label>
                                        <div class="col-sm-8">
                                            <input
                                                    step="any"
                                                    type="number"
                                                    name="cantidad_inservible"
                                                    data-vv-as="Cantidad Inservible"
                                                    v-validate="{required: false}"
                                                    class="form-control"
                                                    id="cantidad_inservible"
                                                    placeholder="Cantidad Inservible"
                                                    v-model="dato.cantidad_inservible"
                                                    :class="{'is-invalid': errors.has('cantidad_inservible')}"
                                                    v-on:keyup.enter.prevent="validate">
                                            <div class="invalid-feedback" v-show="errors.has('cantidad_inservible')">{{ errors.first('cantidad_inservible') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                             <label for="seguir" @click="foco">Captura Continua</label>
                            <input type="checkbox" id="seguir" value="seguir" v-model="seguir"  @click="foco" >
                             <label for="mostrar" v-if="marbete && barcodeValue && tipoConteo"  @click="foco">Más información</label>
                            <input type="checkbox" id="mostrar" value="mostrar" v-model="checkedMostrar" v-if="marbete && barcodeValue && tipoConteo"  @click="foco">
                            </form>

                        </div>
                         <div class="modal-footer">
                             <button type="button" class="btn btn-secondary" @click="cerrar">Cerrar</button>
                             <button class="btn btn-primary" :disabled="errors.count() > 0 || dato.cantidad_nuevo == ''" @click="validate">Registrar</button>

                        </div>
                </div>
            </div>
          </div>
    </span>
</template>

<script>
    import MarbeteSelect from "../marbete/Select";
    import VueBarcode from 'vue-barcode';
    export default {
        name: "codigo-barra",
        components: {MarbeteSelect,'barcode': VueBarcode},
        data() {
            return {
                cargando: false,
                marbetes:[],
                conteos:[],
                barcodeValue: '',
                seguir: true,
                checkedMostrar: false,
                dato:{
                    id_marbete:'',
                    tipo_conteo:'',
                    cantidad_usados:0,
                    cantidad_nuevo:'',
                    cantidad_inservible:0,
                    total:''
                }
            }
        },
        methods:{
            transformer(){
                if (this.barcodeValue[(this.barcodeValue.length)-2] == "c"){
                    var marbete = this.barcodeValue.split("c");
                    this.barcodeValue = marbete[0]+"C"+marbete[1];
                    this.findCodigo();
                }
                else if(this.barcodeValue[(this.barcodeValue.length)-2] == "C"){
                    this.findCodigo();
                }else {
                    swal('¡Error!', 'Número de Marbete Invalido.', 'error');
                    this.barcodeValue='';
                }
            },
            findCodigo() {
                var marbete = this.barcodeValue.split("C");
                this.dato.id_marbete = marbete[0];
                if (!marbete[1] || marbete[1] == 3 || marbete[2]){
                    swal('¡Error!', 'Número de Marbete Invalido.', 'error');
                    this.barcodeValue='';
                }else{
                    this.dato.tipo_conteo = marbete[1];
                    if(marbete[1]<0 || marbete[1]>=3){
                        swal('¡Error!', 'Número de Marbete Invalido.', 'error');
                        this.barcodeValue='';
                    }else{
                        this.$store.commit('almacenes/marbete/SET_MARBETE', null);
                        return this.$store.dispatch('almacenes/marbete/findCodigo', {
                            id: marbete[0],
                            params: {}
                        }).then(data => {
                            this.$store.commit('almacenes/marbete/SET_MARBETE', data);
                            return this.$store.dispatch('almacenes/ctg-tipo-conteo/find', {
                                id: marbete[1],
                                params: {}
                            }).then(data => {
                                this.$store.commit('almacenes/ctg-tipo-conteo/SET_CONTEO', data);
                                this.$nextTick(() => this.$refs.input.focus());
                            });
                        });
                    }
                }
            },
            cerrar(){
                this.$emit('created');
                $(this.$refs.modal).modal('hide');
            },
            foco(){
                if(this.checkedMostrar == false){
                    this.dato.cantidad_usados=0;
                    this.dato.cantidad_inservible=0;
                }
                if(this.$refs.input){
                    this.$nextTick(() => this.$refs.input.focus());
                }else{
                    this.$nextTick(() => this.$refs.barcodeValue.focus());
                }
            },
            init() {
                this.cargando = true;
                this.barcodeValue='';
                this.checkedMostrar=false;
                this.dato.cantidad_usados=0;
                this.dato.id_marbete = '';
                this.dato.tipo_conteo = '';
                this.dato.cantidad_nuevo='';
                this.dato.cantidad_inservible=0;
                this.dato.total='';
                this.$validator.reset();
                this.cargando = false;
                this.$refs.barcodeValue.focus();
                $(this.$refs.modal).modal('show');
            },
            getConteo(){
                this.conteos = [];
                return this.$store.dispatch('almacenes/ctg-tipo-conteo/index', {
                    params: {
                        sort: 'id', order: 'asc'
                    }
                }).then(data => {
                        this.conteos = data.data;
                  })
            },
            validate() {
                this.$validator.validate().then(result => {
                    var marbete = this.barcodeValue.split("C");
                    if (result) {
                        if(this.dato.cantidad_usados < 0 || this.dato.cantidad_nuevo < 0 || this.dato.cantidad_inservible < 0){
                            swal('¡Error!', 'Error al registrar cantidad, favor de revisar la información y registrar la cantidad nuevamente.', 'error');
                            this.dato.cantidad_usados=0;
                            this.dato.cantidad_nuevo='';
                            this.dato.cantidad_inservible=0;
                            this.$nextTick(() => this.$refs.input.focus());
                        }
                        else {
                            this.dato.id_marbete = marbete[0];
                            this.dato.tipo_conteo = marbete[1];
                            this.store()
                        }
                    }
                });
            },

            store() {
                return this.$store.dispatch('almacenes/conteo/storeCodigoBarra', this.$data.dato)
                    .then(data => {
                        if(this.seguir){
                            this.$emit('created');
                            this.init();
                        }else{
                            this.$emit('created', data);
                            $(this.$refs.modal).modal('hide');
                        }
                    })
                    .catch(error => {
                        this.$emit('created');
                        this.init();
                    })
                    .finally( ()=>{
                        this.cargando = false;
                    });
            },
        },
        computed: {
            marbete() {
                return this.$store.getters['almacenes/marbete/currentMarbete'];
            },
            tipoConteo() {
                return this.$store.getters['almacenes/ctg-tipo-conteo/currentConteo'];
            }
        }
    }
</script>

<style>
    .error-label {
        width: 100%;
        margin-top: 0.25rem;
        font-size: 80%;
        color: #dc3545;
    }
</style>