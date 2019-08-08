<template>
    <span>
        <button @click="init" v-if="$root.can('registrar_banco')" class="btn btn-app btn-info pull-right">
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
                                        <label for="id_banco">Banco</label>
                                        <select
                                            class="form-control"
                                            name="id_ctg_banco"
                                            data-vv-as="Banco"
                                            id="id_ctg_banco"
                                            v-model="id_ctg_banco"
                                            v-validate="{required: true}"
                                            :class="{'is-invalid': errors.has('id_tipo_fondo')}">
                                            <option value>-- Seleccione un banco --</option>
                                            <option  v-for="(item, index) in bancos" :value="item.id">
                                                {{ item.razon_social }}
                                            </option>

                                        </select>
                                        <div class="invalid-feedback" v-show="errors.has('id_ctg_banco')">{{ errors.first('id_ctg_banco') }}</div>
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

                id_ctg_banco: '',
                bancos:[],

            }
        },

        mounted() {
            this.getBancos()


        },

        methods: {
            init() {
                $(this.$refs.modal).modal('show');
                  this.$validator.reset()
            },
            getBancos() {
                return this.$store.dispatch('seguridad/finanzas/ctg-banco/index', {
                params: {sort: 'razon_social',  order: 'asc', scope:'NoRegistrado'}
                })
                    .then(data => {
                        this.bancos= data.data;
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

                return this.$store.dispatch('cadeco/banco/store', this.$data)
                    .then((data) => {
                        $(this.$refs.modal).modal('hide');
                        this.$emit('created',data)
                        this.bancos=[];
                        this.getBancos();

                    })
            }
        },

        computed: {


        }
    }
</script>
