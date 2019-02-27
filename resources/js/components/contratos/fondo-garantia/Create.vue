<template>
    <span>
        <button @click="init" v-if="$root.can('registrar_movimiento_bancario')" class="btn btn-app pull-right" >
            <i class="fa fa-plus"></i> Generar Fondo de Garantía
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
                                        <div class="col-md-4">
                                            <div class="form-group error-content">
                                                <subcontrato-sin-fondo-select
                                                        scope="sinFondo"
                                                        name="id_subcontrato"
                                                        id="id_subcontrato"
                                                        data-vv-as="Subcontrato"
                                                        v-validate="{required: true}"
                                                        v-model="id_subcontrato"
                                                        :error="errors.has('id_subcontrato')">
                                                ></subcontrato-sin-fondo-select>
                                                 <div class="error-label" v-show="errors.has('id_subcontrato')">{{ errors.first('id_subcontrato') }}</div>
                                            </div>
                                        </div>

                                    </div>
                                 </div>
                             </div>
                            <div  v-if="subcontrato.id" class="card">
                                <div class="card-header">
                                     <div class="row" >
                                         <div class="col-md-12">
                                              <label ><i class="fa fa-info-circle" style="padding-right:3px"></i>Detalle de Subcontrato {{subcontrato.numero_folio_format}}</label>
                                         </div>
                                     </div>
                                 </div>
                                 <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label for="id_subcontrato">Referencia:</label>
                                        </div>
                                        <div class="col-md-5">
                                            {{subcontrato.referencia}}
                                        </div>
                                        <!-- Fecha -->
                                        <div class="col-md-3 money" >
                                             <label>Fecha:</label>
                                        </div>
                                        <div class="col-md-2 money">
                                            {{subcontrato.fecha_format}}
                                        </div>
                                    </div>
                                     <div class="row">
                                        <!-- Observaciones -->
                                        <div class="col-md-2">
                                            <label for="id_subcontrato">Empresa:</label>
                                        </div>
                                        <div class="col-md-10">
                                            {{subcontrato.empresa.razon_social}}
                                        </div>
                                    </div>
                                    <div class="row">
                                        <!-- Total -->
                                        <div class="col-md-2">
                                             <label>Total:</label>
                                        </div>
                                        <div class="col-md-3">
                                            {{subcontrato.monto_format}} ({{subcontrato.moneda.nombre}})
                                        </div>
                                        <!-- % Anticipo -->
                                        <div class="col-md-1">
                                             <label>Anticipo:</label>
                                        </div>
                                        <div class="col-md-1 money">
                                            {{subcontrato.anticipo}} %
                                        </div>
                                        <!-- % Fondo e Garantía -->
                                         <div class="col-md-3 money">
                                             <label>Fondo de Garantía: </label>
                                        </div>
                                        <div class="col-md-2 money">
                                            {{subcontrato.retencion}} %
                                        </div>
                                    </div>
                                    <div class="row">
                                        <!-- Observaciones -->
                                        <div class="col-md-2">
                                            <label for="id_subcontrato">Observaciones:</label>
                                        </div>
                                        <div class="col-md-10">
                                            {{subcontrato.observaciones}}
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
    import SubcontratoSinFondoSelect from  "../subcontratos/SelectSubcontratosSinFondo";
    export default {
        name: "fondo-garantia-create",
        components: {SubcontratoSinFondoSelect},
        data() {
            return {
                id_subcontrato : '',
                subcontrato : []
            }
        },

        mounted() {
            this.getSubcontratosSinFondo()
        },

        methods: {
            find(id) {
                return this.$store.dispatch('contratos/subcontrato/find', {
                    id: id,
                    params: { include: 'moneda,empresa' }
                })
            },
            init() {
                $(this.$refs.modal).modal('show');
                this.id_subcontrato = '';
                this.retencion = '';
                this.subcontrato = [];
                this.$validator.reset()
            },

            getSubcontratosSinFondo() {
                return [];
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
                this.find(id)
                    .then(data=>{
                        this.subcontrato = data
                        this.retencion = data.retencion
                     })
            }
        }
    }
</script>

<style scoped>
    .modal-body {
        background-color: #ddd;
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