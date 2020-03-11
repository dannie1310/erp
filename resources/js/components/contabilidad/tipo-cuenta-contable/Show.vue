<template>
    <span>
        <button @click="find()" type="button" class="btn btn-sm btn-outline-secondary" :disabled="cargando">
            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
            <i class="fa fa-eye" v-else></i>
        </button>

        <!-- Modal -->
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content" v-if="tipo">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Tipo Cuenta Contable</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>Descripcion</th>
                                    <th>Registró</th>
                                    <th>Naturaleza de la Cuenta</th>
                                    <th>Fecha y Hora de Registro</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                   <td>{{ tipo.descripcion }}</td>
                                   <td v-if="tipo.usuario">{{ tipo.usuario.nombre }}</td>
                                   <td v-else> </td>
                                   <td v-if="tipo.naturaleza">{{ tipo.naturaleza.descripcion}}</td>
                                   <td v-else> </td>
                                   <td>{{ tipo.fecha}}</td>
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
        name: "tipo-cuenta-contable-show",
        props: ['id'],
        data() {
            return {
                cargando: false,
            }
        },
        methods: {
            find() {
                this.cargando = true;
                return this.$store.dispatch('contabilidad/tipo-cuenta-contable/find', {
                    id: this.id,
                    params: { include: ['naturaleza', 'usuario'] }
                })
                    .then(data => {
                        this.$store.commit('contabilidad/tipo-cuenta-contable/SET_TIPO', data);
                        $(this.$refs.modal).appendTo('body')
                        $(this.$refs.modal).modal('show');
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            }
        },

        computed: {
            tipo() {
                return this.$store.getters['contabilidad/tipo-cuenta-contable/currentTipo']
            }
        }
    }
</script>
