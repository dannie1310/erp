<template>
    <span>
        <button @click="find" class="btn btn-sm btn-outline-info">
            <i class="fa fa-pencil"></i>
        </button>

        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content" v-if="empresa">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">{{ empresa.razon_social }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Cuenta Contable</th>
                                    <th>Tipo de Cuenta</th>
                                    <th>Guardar</th>
                                </tr>
                                </thead>
                                <tbody>
                                <cuenta-empresa-edit-form v-for="(cuenta, i) in empresa.cuentasEmpresa.data" :cuenta="cuenta" :key="i"/>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    import CuentaEmpresaEditForm from "./EditForm";
    export default {
        name: "cuenta-empresa-edit",
        components: {CuentaEmpresaEditForm},
        props: ['id'],
        data() {
            return {
                empresa: null
            }
        },

        computed: {
            datosContables() {
                return this.$store.getters['auth/datosContables']
            }
        },

        methods: {
            find() {
                return this.$store.dispatch('cadeco/empresa/find', {
                    id: this.id,
                    params: { include: 'cuentasEmpresa' }
                })
                    .then((data) => {
                        this.empresa = data
                        $(this.$refs.modal).modal('show')
                    })
            }
        }
    }
</script>