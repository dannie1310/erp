<template>
    <span>
        <button @click="init" v-if="$root.can('generar_fondo_garantia')" class="btn btn-app float-right" :disabled="cargando" >
            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
            <i class="fa fa-plus" v-else></i>
            Generar Fondo de Garantía
        </button>

        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"><i class="fa fa-edit" style="padding-right:3px"></i>Generar Fondo de Garantía</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                             <div   class="card">
                                 <div class="card-header">
                                     <div class="row" >
                                         <div class="col-md-12">
                                              <label ><i  class="fa fa-info-circle" style="padding-right:3px"></i>Seleccione el subcontrato al que se generará el fondo de garantía</label>
                                         </div>
                                     </div>
                                 </div>
                                 <div class="card-body">
                                    <div class="row">
                                        <!-- SubcontratoService -->
                                        <div class="col-md-2">
                                            <label for="id_subcontrato">Subcontrato:</label>
                                        </div>
                                        <div class="col-md-10">
                                            <div class="form-group error-content">
                                                <model-list-select
                                                        name="id_subcontrato"
                                                        :placeholder="cargando?'Cargando...':'Seleccionar o buscar por número de folio o por referencia'"
                                                        data-vv-as="Subcontrato"
                                                        v-model="id_subcontrato"
                                                        option-value="id"
                                                        :custom-text="numeroFolioAndReferencia"
                                                        :list="subcontratos"
                                                        v-validate="{required: true}"
                                                        :isError="errors.has(`id_subcontrato`)"
                                                >
                                                </model-list-select>
                                                 <div class="error-label" v-show="errors.has('id_subcontrato')">{{ errors.first('id_subcontrato') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                 </div>
                             </div>
                            <div class="row" v-if="subcontrato.id && buscando == 0">
                                <div class="col-md-12">
                                    <detalle-subcontrato :subcontrato="subcontrato" ></detalle-subcontrato>
                                </div>
                            </div>
                            <div class="card" v-if="buscando == 1">
                                <div class="card-body">
                                    <div class="row" >
                                        <div class="col-md-12" align = "center">
                                            <i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>
                                        </div>
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
    import SubcontratoSelect from "../../cadeco/subcontrato/Select";
    import DetalleSubcontrato from "../subcontrato/partials/DetalleSubcontrato";
    import {ModelListSelect} from 'vue-search-select';
    export default {
        name: "fondo-garantia-create",
        components: {SubcontratoSelect, DetalleSubcontrato, ModelListSelect},

        data() {
            return {
                id_subcontrato : '',
                subcontrato : [],
                buscando: 0,
                cargando: false,
                subcontratos:[],
            }
        },

        mounted() {
            this.getSubcontratosSinFondo();
        },

        methods: {
            find(id) {
                this.buscando = 1;
                return this.$store.dispatch('contratos/subcontrato/find', {
                    id: id,
                    params: { include: 'moneda,empresa' }
                })
            },
            init() {
                cargando: true;
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
                this.id_subcontrato = '';
                this.retencion = '';
                this.subcontrato = [];
                this.$validator.reset();
                cargando: false;
            },

            getSubcontratosSinFondo() {
                this.cargando = true;
                 return this.$store.dispatch('contratos/subcontrato/index', {
                    config: {
                        params: {
                            scope: ['sinFondo'],
                        }
                    }
                })
                    .then(data => {
                        this.subcontratos = data;
                        this.cargando = false;
                    })
            },
            numeroFolioAndReferencia (item) {
                return `[${item.numero_folio_format}] - Referencia: [${item.referencia}]`
            },

            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.store()
                    }
                });
            },

            store() {
                return this.$store.dispatch('contratos/fondo-garantia/store', {id_subcontrato:this.id_subcontrato})
                    .then((data) => {
                        $(this.$refs.modal).modal('hide');
                        this.$emit('created', data);
                    })
            }
        },

        computed: {
            subcontratosSinFondo() {
                return this.$store.getters['contratos/fondo-garantia/fondosGarantia']
            },
        },

        watch : {
            id_subcontrato (id)
            {
                if(id != ''){
                    this.find(id)
                    .then(data=>{
                        this.subcontrato = data
                        this.retencion = data.retencion
                        this.buscando = 0
                     })
                }
                
            }
        }
    }
</script>

<style scoped>
    .modal-body {
        background-color: #dedede;
    }
    .btn-primary {
        background-color: #00c0ef;
        border-color: #00acd6;
        color: #FFF;
    }
    button:checked{
        background-color: #5bc0de;
    }
    .btn-primary:hover {
        background-color: #5bc0de;
        border-color: #46b8da;
    }
    .error-label {
        width: 100%;
        margin-top: 0.25rem;
        font-size: 80%;
        color: #dc3545;
    }
    .money
    {
        text-align: right;
    }
</style>
