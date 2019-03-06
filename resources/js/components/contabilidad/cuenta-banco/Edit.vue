<template>
    <span>
        <button @click="find" class="btn btn-sm btn-outline-info" :disabled="cargando">
            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
            <i class="fa fa-pencil" v-else></i>
        </button>

        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content" v-if="cuenta">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">{{ `${cuenta.numero} (${cuenta.abreviatura ? cuenta.abreviatura : ''} ${cuenta.empresa.razon_social})` }}</h5>
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
                                    <th>Tipo de Cuenta</th>
                                    <th>Cuenta Contable</th>
                                    <th>Guardar</th>
                                </tr>
                                </thead>
                                <tbody>
                                <cuenta-banco-edit-form v-for="(c, i) in cuenta.cuentasBanco.data" :cuenta="c" :key="i"/>
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
    import CuentaBancoEditForm from "./EditForm";
    export default {
        name: "cuenta-banco-edit",
        components: {CuentaBancoEditForm},
        props: ['id'],
        data() {
            return {
                cuenta: null,
                cargando: false
            }
        },

        computed: {
            datosContables() {
                return this.$store.getters['auth/datosContables']
            }
        },

        methods: {
            find() {
                this.cargando = true;
                return this.$store.dispatch('cadeco/cuenta/find', {
                    id: this.id,
                    params: { include: ['cuentasBanco', 'empresa'] }
                })
                    .then(data => {
                        this.cuenta = data
                        $(this.$refs.modal).modal('show')
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            }
        }
    }
</script>