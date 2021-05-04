<template>
    <span>
        <button @click="init" class="btn btn-app btn-info float-right">
            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
            <i class="fa fa-plus" v-else></i>
            Registrar
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">REGISTRAR FECHA INHÁBILES</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group error-content">
                                        <label for="fecha" class="col-form-label">Fecha:</label>
                                        <datepicker v-model = "fecha"
                                                    name = "fecha"
                                                    :format = "formatoFecha"
                                                    :language = "es"
                                                    :bootstrap-styling = "true"
                                                    class = "form-control"
                                                    v-validate="{required: true}"
                                                    :class="{'is-invalid': errors.has('fecha')}"
                                        ></datepicker>
                                        <div class="invalid-feedback" v-show="errors.has('fecha')">{{ errors.first('fecha') }}</div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <label for="id_tipo_fecha" class="col-md-2 col-form-label">Tipo Fecha</label>
                                    <div class="col-md-10">
                                        <select
                                            name="id_tipo_fecha"
                                            id="id_tipo_fecha"
                                            data-vv-as="Tipo Fecha"
                                            v-validate="{required: true}"
                                            class="form-control"
                                            v-model="id_tipo_fecha"
                                            :class="{'is-invalid': errors.has('id_tipo_fecha')}"
                                        >
                                            <option value>-- Tipo Fecha --</option>
                                            <option v-for="tipo in tipos" :value="tipo.id">{{ tipo.descripcion }}</option>
                                        </select>
                                        <div class="invalid-feedback" v-show="errors.has('id_tipo_fecha')">{{ errors.first('id_tipo_fecha') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary" :disabled="errors.count() > 0" @click="validate"><i class="fa fa-save"></i> Guardar</button>
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
        name: "fecha-inhabiles-create",
        components: {Datepicker, es},
        data() {
            return {
                es : es,
                cargando : false,
                fecha : '',
                fecha_hoy : '',
                id_tipo_fecha : ''
            }
        },
        mounted() {
            this.fecha_hoy = new Date();
            this.fecha = new Date();
        },
        methods: {
            init() {
                this.fecha = new Date();
                this.cargando = false;
                this.getTipos();
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
            },
            formatoFecha(date) {
                return moment(date).format('DD/MM/YYYY');
            },
            getTipos() {
                this.cargando = true;
                return this.$store.dispatch('fiscal/tipo-fecha-sat/index', {
                    params: {sort: 'descripcion', order: 'asc'}
                })
                    .then(data => {
                        this.$store.commit('fiscal/tipo-fecha-sat/SET_TIPOS', data.data);
                        this.cargando = false;
                    })
            },
            store() {
                return this.$store.dispatch('fiscal/fecha-inhabil-sat/store',  {
                    'fecha' : this.fecha,
                    'id_tipo_fecha' : this.id_tipo_fecha
                })
                    .then((data) => {
                        this.$emit('created', data)
                        this.salir()
                    });
            },

            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.store()
                    }
                });
            },
            salir(){
                $(this.$refs.modal).modal('hide');
            }
        },
        computed: {
            tipos() {
                return this.$store.getters['fiscal/tipo-fecha-sat/tipos'];
            },
        }
    }
</script>

<style scoped>

</style>
