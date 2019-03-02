<template>
    <span>
        <button @click="find(id)" type="button" class="btn btn-sm btn-outline-info">
            <i class="fa fa-pencil"></i>
        </button>

        <!-- Modal -->
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">EDICIÓN DE CIERRE DE PERIODO </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="anio">Año</label>
                                        <input readonly type="text" class="form-control" id="anio" :value="datos.anio">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="mes">Mes</label>
                                        <input readonly type="text" class="form-control" id="mes" :value="datos.mes">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div v-if="datos.apertura != undefined"  class="form-group error-content">
                                        <label for="estado">Estatus</label>
                                        <select
                                                type="text"
                                                name="estado"
                                                v-validate="{required: true}"
                                                class="form-control"
                                                id="estado"
                                                v-model="data.estatus"
                                        >
                                            <option value>-- Estado --</option>
                                            <option v-if="datos.apertura.estatus_actual == 0" :value="1">ABRIR</option>
                                            <option v-else="datos.apertura.estatus_actual == 1" :value="0">CERRAR</option>

                                        </select>
                                    </div>
                                    <div v-else class="form-group error-content">
                                        <label for="estado">Estatus</label>
                                        <select
                                                type="text"
                                                name="estado"
                                                v-validate="{required: true}"
                                                class="form-control"
                                                id="estado"
                                                v-model="data.estatus"
                                        >
                                            <option value>-- Estado --</option>
                                            <option :value="1">ABRIR</option>
                                        </select>
                                    </div>
                                </div>
                                <div v-if="data.estatus == 1" class="col-md-6">
                                    <div class="form-group error-content">
                                        <label for="motivo">Motivo</label>
                                        <input
                                                type="text"
                                                name="motivo"
                                                v-validate="{required: true}"
                                                class="form-control"
                                                id="motivo"
                                                placeholder="Motivo"
                                                v-model="data.motivo"
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary"  @click="validate">Guardar Cambios</button>


                        </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "cierre-periodo-edit",
        props: ['id','datos'],
        data() {
            return {
                data: {
                    estatus: this.estado,
                    id_cierre: this.id,
                    motivo: this.motivo
                }
            }
        },
        computed: {
            datosContables() {
                return this.$store.getters['auth/datosContables']
            }
        },

        methods: {
            find(id) {
                return this.$store.dispatch('contabilidad/cierre-periodo/find', id)
                    .then(() => {
                        $(this.$refs.modal).modal('show');
                    })
            },

            update(id) {
                return this.$store.dispatch('contabilidad/cierre-periodo/update', {
                        id: id,
                        data: this.$data.data
                    })
                    .then(() => {
                        $(this.$refs.modal).modal('hide');
                    })
            },

            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.update(this.id)
                    }
                });
            },

            updateAttribute(e) {
                return this.$store.commit('contabilidad/cierre-periodo/UPDATE_ATTRIBUTE', {attribute: $(e.target).attr('name'), value: e.target.value})
            }
        }
    }
</script>