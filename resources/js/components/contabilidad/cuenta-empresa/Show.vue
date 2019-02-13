<template>
    <span>
        <button @click="find(id)" type="button" class="btn btn-sm btn-outline-secondary"><i class="fa fa-eye"></i></button>

        <!-- Modal -->
        <div v-if="empresa" class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
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
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(cuenta, i) in empresa.cuentasEmpresa.data">
                                    <td>{{ i + 1 }}</td>
                                    <td>{{ cuenta.cuenta }}</td>
                                    <td>{{ cuenta.tipo.descripcion }}</td>
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
        name: "cuenta-empresa-show",
        props: ['id'],
        methods: {
            find(id) {
                return this.$store.dispatch('cadeco/empresa/find', {
                    id: id,
                    params: { include: 'cuentasEmpresa' }
                })
                    .then(() => {
                        $(this.$refs.modal).modal('show');
                    })
            }
        },

        computed: {
            empresa() {
                return this.$store.getters['cadeco/empresa/currentEmpresa']
            }
        }
    }
</script>