<template>
    <span>
        <button @click="init" v-if="$root.can('registrar_fondos')" class="btn btn-app btn-info pull-right">
            <i class="fa fa-plus"></i> Registrar Fondo
        </button>

        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Registrar Fondo</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row">

                    <div  class="row" v-if="!isHidden" style="width:100%; margin-left:1px;">
                                <!-- Responsable Select-->
                                 <div class="col-md-10" >
                                    <div class="form-group error-content">
                                        <label for="id_empresa">Responsable</label>
                                        <select
                                                class="form-control"
                                                name="id_empresa"
                                                data-vv-as="Tipo"
                                                id="id_empresa"
                                                v-model="id_empresa"
                                                v-validate="{required: true}"
                                                :class="{'is-invalid': errors.has('id_empresa')}">
                                            <option value>-- Responsable --</option>
                                            <option v-for="(item, index) in empresas" :value="item.id">
                                                {{ item.razon_social }}
                                            </option>
                                        </select>

                                        <div class="invalid-feedback" v-show="errors.has('id_empresa')">{{ errors.first('id_empresa') }}</div>
                                    </div>
                                </div>
                                <div class="col-md-2 mt-4"  >
                                    <button v-on:click="isHidden = true" type="button" class="btn btn-md  btn-secondary"  style="margin-top:6px;" >
                                        <i class="fa fa-plus"></i>
                                      </button>
                                </div>
                            </div>
                                        <!--Responsable Manual-->
                                        <div  class="row" id="selResMan" v-if="isHidden" style="width:100%; margin-left:1px;">
                                         <div class="col-md-10" >
                                            <div class="form-group error-content">
                                                <label for="responsable_text">Responsable</label>
                                                 <input type="text" class="form-control"
                                                        name="responsable_text"
                                                        data-vv-as="Responsable"
                                                        v-model="responsable_text"
                                                        v-validate="{required: true}"
                                                        :class="{'is-invalid': errors.has('responsable_text')}"
                                                        id="responsable_text"
                                                        placeholder="Nombre del Responsable">

                                                <div class="invalid-feedback" v-show="errors.has('responsable_text')">{{ errors.first('responsable_text') }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-2 mt-4"  >
                                            <button v-on:click="isHidden = false"  type= "button" class="btn btn-md  btn-secondary"  style="margin-top:6px;" >

                                              <i class="fa fa-caret-square-o-down"></i>
                                              </button>
                                        </div>
                        </div>
                                <!-- Tipo de Fondo-->
                                     <div class="col-md-10" v-if="tiposFondo">
                                    <div class="form-group error-content">
                                        <label for="id_tipo_fondo">Tipo de Fondo</label>
                                        <select
                                                class="form-control"
                                                name="id_tipo_fondo"
                                                data-vv-as="Tipo de Fondo"
                                                id="id_tipo_fondo"
                                                v-model="id_tipo_fondo"
                                                v-validate="{required: true}"
                                                :class="{'is-invalid': errors.has('id_tipo_fondo')}">
                                            <option value>-- Tipo de Fondo--</option>
                                            <option v-for="(item, index) in tiposFondo" :value="item.id">
                                                {{ item.descripcion }}
                                            </option>
                                        </select>
                                        <div class="invalid-feedback" v-show="errors.has('id_tipo_fondo')">{{ errors.first('id_tipo_fondo') }}</div>
                                    </div>
                                </div>
                                <!-- Tipo de Gasto-->
                                     <div class="col-md-10">
                                    <div class="form-group error-content">
                                        <label for="id_costo">Tipo de Gasto</label>
                                       <costo-select
                                               name="id_costo"
                                               data-vv-as="Costo"
                                               v-validate="{required: true}"
                                               id="id_costo"
                                               v-model="id_costo"
                                               :error="errors.has('id_costo')"
                                               ref="costoSelect"
                                               :disableBranchNodes="false"
                                       ></costo-select>
                                        <div class="invalid-feedback" v-show="errors.has('id_costo')">{{ errors.first('id_costo') }}</div>
                                    </div>
                                </div>
                                <!-- Check Fondo de Obra-->
                                <div class="col-md-10">
                                    <div class="form-check">
                                        <input type="checkbox"
                                               name="checkFondo"
                                               class="form-check-input"
                                               data-vv-as="FondoObra"
                                               v-model="checkFondo"
                                               id="checkFondo"
                                               v-on:click=" ! checkFondo">
                                        <label class="form-check-label" for="checkFondo">Fondo de Obra</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                           <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </div>
                    </form>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div>
    </span>
</template>

<script>

    import FondoIndex from '../Index';
    import CostoSelect from "../../cadeco/costo/Select";
    export default {
        name: "fondo-create",
        components: {CostoSelect, FondoIndex},
        data() {
            return {
                id_empresa: '',
                responsable_text: '',
                id_tipo_fondo: '',
                id_costo: '',
                nombre: '',
                descripcion_corta: '',
                checkFondo: false,
                fondo_obra: '',
                descripcion: '',
                costos: [],
                empresas:[],
                tiposFondo:[],
                isHidden:false
            }
        },

        mounted() {
            this.getEmpresa()
            this.getTiposFondo()

        },

        methods: {
            init() {
                $(this.$refs.modal).modal('show');
                this.id_empresa = '';
                this.responsable_text = '';
                this.id_tipo_fondo = '';
                this.id_costo = '';
                this.nombre = '';
                this.descripcion_corta= '';
                this.descripcion = '',
                this.fondo_obra = '',
                this.costos = [],
                this.checkFondo = false;

                this.$validator.reset()
            },
            getEmpresa() {
                return this.$store.dispatch('cadeco/empresa/index', { params: { scope:'TipoEmpresa'} })
                    .then(data => {
                        this.empresas= data.data;
                    })
            },
            getTiposFondo() {
                return this.$store.dispatch('finanzas/ctg-tipo-fondo/ctgTipoFondo',{
                    params: {scope:'TipoFondoActivo'}
                })
                    .then(data => {
                        this.tiposFondo = data.data;

                    })
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.store()
                    }
                });
            },
            store() {
                 return this.$store.dispatch('cadeco/fondo/store', this.$data)
                     .then((data) => {
                        $(this.$refs.modal).modal('hide');
                        this.$emit('created',data)
                         this.getEmpresa();

                     })
            }
        },

        computed: {


        }
    }
</script>