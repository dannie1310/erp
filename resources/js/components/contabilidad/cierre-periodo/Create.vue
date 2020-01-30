<template>
    <span>
        <button @click="init" v-if="$root.can('generar_cierre_periodo')" class="btn btn-app btn-info float-right" :disabled="cargando">
            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
            <i class="fa fa-plus" v-else></i>
            Registrar
        </button>

        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">REGISTRAR CIERRE DE PERIODO</h5>
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
                                            <label><b>Fecha</b></label>
                                                <datepicker v-model = "date"
                                                            name = "fecha"
                                                            :language = "es"
                                                            :format = "formatoFecha"
                                                            :bootstrap-styling = "true"
                                                            class = "form-control"
                                                ></datepicker>
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
   import Datepicker from 'vuejs-datepicker';
    import {es} from 'vuejs-datepicker/dist/locale';
    export default {
        name: "cierre-periodo-create",
        components: {Datepicker},
        data() {
            return {
                es: es,
                anio: '',
                mes: '',
                cargando: false,
                date: ''
            }
        },
        methods: {
            init() {
                $(this.$refs.modal).modal('show');
                this.date = new Date();
                this.anio = new Date (this.date).getFullYear();
                this.mes = new Date (this.date).getMonth() + 1;
                this.cargando = false;


                this.$validator.reset()
            },
            formatoFecha(date){
                return moment(date).format('MM/YYYY');
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
        watch: {
            date(value){
                this.anio = '';
                this.mes = '';
                if(value){
                        this.anio = new Date (value).getFullYear();
                        this.mes = new Date (value).getMonth() + 1;
                }
            },
        }
    }
</script>
