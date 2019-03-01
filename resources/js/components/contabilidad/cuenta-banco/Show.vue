<template>
    <span>
        <button @click="find(id)" type="button" class="btn btn-sm btn-outline-secondary"><i class="fa fa-eye"></i></button>

        <!-- Modal -->
        <div v-if="cuenta" class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">{{ titulo }}</h5>
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
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(cuenta_bancaria, i) in cuenta.cuentasBanco.data">
                                    <td>{{ i + 1 }}</td>
                                    <td>{{ cuenta_bancaria.cuenta }}</td>
                                    <td>{{ cuenta_bancaria.tipo.descripcion }}</td>
                                </tr>


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
    export default {
        name: "cuenta-banco-show",
        props: ['id','titulo'],
        methods: {
            find(id) {
                return this.$store.dispatch('cadeco/cuenta/find', {
                    id: id,
                    params: { include: 'cuentasBanco' }
                })
                    .then(() => {
                        $(this.$refs.modal).modal('show');
                    })
            }
        },

        computed: {
            cuenta() {
                return this.$store.getters['cadeco/cuenta/currentCuenta']
            }
        }
    }
</script>