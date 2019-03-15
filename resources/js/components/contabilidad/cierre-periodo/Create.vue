<template>
    <span>
        <button @click="init" v-if="$root.can('generar_cierre_periodo')" class="btn btn-app btn-info pull-right">
            <i class="fa fa-plus"></i> Cerrar Periodo
        </button>

        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">CERRAR PERIODO</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group error-content">
                                        <div class="form-group">
                                            <label for="fecha"><b>Fecha</b></label>
                                            <input name="Fecha"
                                                   v-validate="{required: true}"
                                                   type="text"
                                                   id="fecha"
                                                   ref="fecha"
                                                   class="form-control input-sm"
                                                   placeholder="Seleccione año y mes del Cierre" >
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary"  @click="validate">Registrar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </span>

</template>

<script>
    export default {
        name: "cierre-periodo-create",
        data() {
            return {
                    anio: '',
                    mes: ''
            }
        },

        mounted: function() {
            let self = this
            $(self.$refs.fecha).datepicker({
                autoclose: true,
                clearBtn: true,
                minViewMode: 1,
                format: 'mm/yyyy',
                language: 'es',
            }).on('changeDate', function(selected){
                if (selected.date) {
                    self.$set(self, 'anio', new Date(selected.date.valueOf()).getFullYear());
                    self.$set(self, 'mes', new Date(selected.date.valueOf()).getMonth() + 1);
                }
            });
        },

        methods: {
            init() {
                $(this.$refs.modal).modal('show');
                this.mes = '';
                this.anio = '';

                this.$validator.reset()
            },

            store() {
                return this.$store.dispatch('contabilidad/cierre-periodo/store', this.$data)
                    .then(data => {
                       $(this.$refs.modal).modal('hide');
                       this.$emit('created', data)
                    });
            },

            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.store()
                    }
                });
            },
        },

        computed: {

        },
    }
</script>