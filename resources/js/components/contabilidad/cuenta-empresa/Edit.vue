<template>
    <span>
        <button @click="find" class="btn btn-sm btn-outline-info" :disabled="cargando">
            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
            <i class="fa fa-pencil" v-else></i>
        </button>

        <div class="modal fade" ref="editModal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content" v-if="empresa">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ empresa.razon_social }}</h5>
                        <button type="button" class="close" @click="closeModal()" aria-label="Close">
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
                                <cuenta-empresa-edit-form v-for="(cuenta, i) in empresa.cuentasEmpresa.data" :cuenta="cuenta" :key="i" @deleted="destroy(cuenta)"/>
                                </tbody>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <cuenta-empresa-create :id="id" :btnclass="'btn btn-primary pull-right'" @created="created()"></cuenta-empresa-create>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="closeModal()">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    import CuentaEmpresaEditForm from "./EditForm";
    import CuentaEmpresaCreate from "./Create";
    export default {
        name: "cuenta-empresa-edit",
        components: {CuentaEmpresaCreate, CuentaEmpresaEditForm},
        props: ['id'],
        data() {
            return {
                empresa: null,
                cargando: false
            }
        },

        computed: {
            datosContables() {
                return this.$store.getters['auth/datosContables']
            }
        },

        methods: {
            closeModal() {
                $(this.$refs.editModal).modal('hide')
            },

            created() {

            },

            find() {
                this.cargando = true;
                return this.$store.dispatch('cadeco/empresa/find', {
                    id: this.id,
                    params: { include: 'cuentasEmpresa' }
                })
                    .then(data => {
                        this.empresa = data
                        $(this.$refs.editModal).modal('show')
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },

            destroy(cuenta) {
                $(this.$refs.editModal).modal('hide')
                this.empresa.cuentasEmpresa.data = this.empresa.cuentasEmpresa.data.filter((c, i) => {
                    return cuenta.id !== c.id;
                })
                this.$store.dispatch('cadeco/empresa/paginate', {
                    params: {
                        include: 'cuentasEmpresa',
                        scope: 'conCuentas',
                        offset: 0
                    }
                })
                    .then(data => {
                        this.$store.commit('cadeco/empresa/SET_EMPRESAS', data.data)
                        this.$store.commit('cadeco/empresa/SET_META', data.meta)
                    })
            }
        }
    }
</script>