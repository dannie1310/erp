<template>
    <span>
        <button @click="init" v-if="$root.can('registrar_cuenta_banco')" class="btn btn-app btn-info pull-right">
            <i class="fa fa-plus"></i> Registrar Cuenta
        </button>

        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">REGISTRAR CUENTA DE BANCO</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">

                    </form>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    import EmpresaAutocomplete from "../../cadeco/empresa/Autocomplete";
    export default {
        name: "cuenta-banco-create",
        components: {EmpresaAutocomplete},
        data() {
            return {
                id_empresa: '',
                cuenta: '',
                id_tipo_cuenta_contable: ''
            }
        },
        computed: {
            tipos() {
                return this.$store.getters['contabilidad/tipo-cuenta-contable/tipos'];
            },
            datosContables() {
                return this.$store.getters['auth/datosContables']
            }
        },
        methods: {
            init() {
                $(this.$refs.modal).modal('show');

                this.id_empresa = '';
                this.cuenta = '';
                this.id_tipo_cuenta_contable = '';

                this.$validator.reset()
            },

            store() {
                return this.$store.dispatch('contabilidad/cuenta-banco/store', this.$data)
                    .then((data) => {
                        $(this.$refs.modal).modal('hide');
                        this.$emit('created', data);
                    });
            },

            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.store()
                    }
                });
            }
        }
    }
</script>