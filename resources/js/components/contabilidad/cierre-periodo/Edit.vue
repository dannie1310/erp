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
                        <h5 class="modal-title" id="exampleModalLongTitle">EDICIÓN DE CIERRE DE PERIODO</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="estatus">Estado</label>
                                        <input readonly type="text" class="form-control" id="estatus" :value="1">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="anio">Año</label>
                                        <input readonly type="text" class="form-control" id="anio" :value="cierre.anio">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="mes">Mes</label>
                                        <input readonly type="text" class="form-control" id="mes" :value="cierre.mes">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                        </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "cierre-periodo-edit",
        props: ['id'],
        computed: {
            datosContables() {
                return this.$store.getters['auth/datosContables']
            },
            cierre() {
                return this.$store.getters['contabilidad/cierre-periodo/currentCierre']
            }
        },

        methods: {
            find(id) {
                return this.$store.dispatch('contabilidad/cierre-periodo/find', id)
                    .then(() => {
                        $(this.$refs.modal).modal('show');
                    })
            },

            update() {
                return this.$store.dispatch('contabilidad/cierre-periodo/update', this.cierre)
                    .then(() => {
                        $(this.$refs.modal).modal('hide');
                    })
            },

            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.update()
                    }
                });
            },

            updateAttribute(e) {
                return this.$store.commit('contabilidad/cierre-periodo/UPDATE_ATTRIBUTE', {attribute: $(e.target).attr('name'), value: e.target.value})
            }
        }
    }
</script>