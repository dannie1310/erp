<template>
    <span>
        <button @click="find(dataCuenta)" type="button" class="btn btn-sm btn-outline-secondary"><i class="fa fa-eye"></i></button>

        <!-- Modal -->
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                       <h5 class="modal-title" id="exampleModalLongTitle">{{ dataCuenta }}</h5> -->
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
                                    <th>Banco</th>
                                    <th>dataCuenta</th>
                                </tr>
                                </thead>
                                <tbody>

                                    <td>{{ dataCuenta }}</td>

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
        props: ['dataCuenta'],
        methods: {
            find(dataCuenta) {
                return
                        $(this.$refs.modal).modal('show');
            }
        },

        computed: {

        }
    }
</script>