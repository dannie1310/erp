<template>
    <span>
        <button @click="init" v-if="" class="btn btn-app btn-info pull-right">
            <i class="fa fa-plus"></i> Registrar Banco
        </button>

        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Registrar Banco</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row">
                                <!-- Banco -->
                                     <div class="col-md-12" v-if="bancos">
                                    <div class="form-group error-content">
                                        <label for="id_tipo_fondo">Banco</label>
                                        <select
                                            class="form-control"
                                            name="id_tipo_fondo"
                                            data-vv-as="Tipo de Fondo"
                                            id="id_tipo_fondo"
                                            v-model="id_tipo_fondo"
                                            v-validate="{required: true}"
                                            :class="{'is-invalid': errors.has('id_tipo_fondo')}">
                                            <option value>-- Seleccione --</option>
                                            <option v-for="(item, index) in bancos" :value="item.id">
                                                {{ item.razon_social }}
                                            </option>
                                        </select>
                                        <div class="invalid-feedback" v-show="errors.has('id_tipo_fondo')">{{ errors.first('id_tipo_fondo') }}</div>
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

    import BancoIndex from '../Index';
    export default {
        name: "banco-create",
       components: {BancoIndex},
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
                bancos:[],
                tiposFondo:[],
                isHidden:false
            }
        },

        mounted() {
            this.getBancos()
           // this.getTiposFondo()

        },

        methods: {
            init() {
                $(this.$refs.modal).modal('show');
                // this.id_empresa = '';
                // this.responsable_text = '';
                // this.id_tipo_fondo = '';
                // this.id_costo = '';
                // this.nombre = '';
                // this.descripcion_corta= '';
                // this.descripcion = '',
                //     this.fondo_obra = '',
                //     this.costos = [],
                //     this.checkFondo = false;

                this.$validator.reset()
            },
            getBancos() {
                return this.$store.dispatch('seguridad/finanzas/ctg-banco/index', { params: { } })
                    .then(data => {
                        this.bancos= data.data;
                    })
            },
            getTiposFondo() {
                // return this.$store.dispatch('finanzas/ctg-tipo-fondo/ctgTipoFondo',{
                //     params: {scope:'TipoFondoActivo'}
                // })
                //     .then(data => {
                //         this.tiposFondo = data.data;
                //
                //     })
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.store()
                    }
                });
            },
            store() {
                // return this.$store.dispatch('cadeco/fondo/store', this.$data)
                //     .then((data) => {
                //         $(this.$refs.modal).modal('hide');
                //         this.$emit('created',data)
                //         this.getEmpresa();
                //
                //     })
            }
        },

        computed: {


        }
    }
</script>
