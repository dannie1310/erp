<template>
    <span>
        <div >
            <div class="row">
                <div class="col-12">
                    <div class="card">
			            <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <partial-show v-bind:id="id" v-bind:base_b64="this.base_b64" @cargaFinalizada="iniciar" />
                                </div>
                            </div>
                            <hr />
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row error-content">
                                        <label for="motivo" class="col-md-2 col-form-label">Motivo de eliminación:</label>
                                        <div class="col-md-10">
                                            <textarea
                                                name="motivo"
                                                id="motivo"
                                                class="form-control"
                                                v-model="motivo"
                                                v-validate="{required: true}"
                                                data-vv-as="Motivo"
                                                :class="{'is-invalid': errors.has('motivo')}"
                                            ></textarea>
                                            <div class="invalid-feedback" v-show="errors.has('motivo')">{{ errors.first('motivo') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="pull-right">
                                <button type="button" class="btn btn-secondary" v-on:click="salir">
                                    <i class="fa fa-angle-left"></i>Regresar
                                </button>
                                <button type="submit" class="btn btn-danger" @click="validate" :disabled="errors.count() > 0 || fin_carga == 0 || motivo == ''">
                                    <i class="fa fa-trash"></i>Eliminar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    import Encabezado from "./partials/Encabezado";
    import TablaDatos from "./partials/TablaDatos"
    export default {
        name: "avance-subcontrato-delete",
        components: {Encabezado, TablaDatos},
        props: ['id'],
        data(){
            return{
                cargando: false,
                motivo: ''
            }
        },
        mounted() {
            this.find();
        },
        methods: {
            find() {
                this.cargando = true;
                return this.$store.dispatch('contratos/avance-subcontrato/obtenerAvance', {
                    id: this.id,
                    params:{include: []}
                }).then(data => {
                    this.avance = data
                })
                    .finally(()=> {
                        this.cargando = false;
                    })
            },
            salir() {
                this.$router.push({name: 'avance-subcontrato'});
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        if(this.motivo == '') {
                            swal('¡Error!', 'Debe colocar un motivo para realizar la operación.', 'error')
                        }
                        else {
                            this.eliminar()
                        }
                    }
                });
            },
            eliminar() {
                var data = {};
                data['motivo'] = this.motivo;
                return this.$store.dispatch('contratos/avance-subcontrato/eliminar', {
                    id: this.id,
                    data: data
                }).then(data => {
                    this.salir();
                })
            },
        }
    }
</script>
