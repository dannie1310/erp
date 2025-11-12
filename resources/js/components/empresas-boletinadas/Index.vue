<template>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <!-- /.card-header -->
                <div class="card-header">
                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                        <label class="btn btn-primary active">
                            <input type="checkbox" autocomplete="off"
                                   v-model="en_juicio"> En Juicio
                        </label>
                        <label class="btn btn-primary active">
                            <input type="checkbox" autocomplete="off"
                                   v-model="mala_experiencia"> Mala Experiencia Operativa
                        </label>
                        <label class="btn btn-primary active">
                            <input type="checkbox" autocomplete="off"
                                   v-model="efos_presuntos_hi"> EFOS Presuntos HI
                        </label>
                        <label class="btn btn-primary active">
                            <input type="checkbox" name="options" autocomplete="off"
                                   v-model="efos_definitivos_hi"> EFOS Definitivos HI
                        </label>
                        <label class="btn btn-primary active">
                            <input type="checkbox" autocomplete="off"
                                   v-model="no_localizados_hi"> No Localizado HI
                        </label>
                        <label class="btn btn-primary">
                            <input type="checkbox" autocomplete="off"
                                   v-model="efos_presuntos"> EFOS Presuntos
                        </label>
                        <label class="btn btn-primary">
                            <input type="checkbox" name="options" autocomplete="off"
                                   v-model="efos_definitivos"> EFOS Definitivos
                        </label>
                        <label class="btn btn-primary">
                            <input type="checkbox" autocomplete="off"
                                   v-model="no_localizados"> No Localizado
                        </label>
                        <label class="btn btn-primary">
                            <input type="checkbox" autocomplete="off"
                                   v-model="boletinado"> Boletinados
                        </label>
                    </div>

                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <datatable v-bind="$data" v-bind:class="'table-sm table-bordered'" v-bind:style="'font-size: 11px'" />
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
    </div>
</template>

<script>
export default {
    name: "Index.vue",
    data() {
        return {
            HeaderSettings: false,
            columns: [
                { title: '#', field: 'index', thClass: 'index_corto', sortable: false },
                { title: 'RFC', field: 'rfc',sortable: true,  thClass:'th_c100', thComp: require('../globals/th-Filter').default},
                { title: 'Razón Social', field: 'razon_social', thClass:'th_c400', sortable: true, thComp: require('../globals/th-Filter').default},
                { title: 'Motivo', tdClass:'center', field: 'motivo', sortable: true, thClass:'th_c150', thComp: require('../globals/th-Filter').default},
                { title: 'Observaciones', field: 'observaciones',  sortable: true},
            ],
            data: [],
            total: 0,
            query: {scope:'', sort: 'rfc', order: 'asc', limit:'20'},
            cargando: false,
            no_localizados : false,
            efos_definitivos : false,
            efos_presuntos: false,
            no_localizados_hi : true,
            efos_definitivos_hi : true,
            efos_presuntos_hi: true,
            en_juicio : true,
            mala_experiencia : true,
            boletinado: false
        }
    },
    mounted() {
        this.$Progress.start();
        this.paginate()
            .finally(() => {
                this.$Progress.finish();
            })
    },
    methods: {
        paginate() {
            this.cargando = true;
            this.$data.query.mala_experiencia = this.mala_experiencia;
            this.$data.query.en_juicio = this.en_juicio;
            this.$data.query.efos_presuntos = this.efos_presuntos;
            this.$data.query.efos_definitivos = this.efos_definitivos;
            this.$data.query.no_localizados = this.no_localizados;
            this.$data.query.efos_presuntos_hi = this.efos_presuntos_hi;
            this.$data.query.efos_definitivos_hi = this.efos_definitivos_hi;
            this.$data.query.no_localizados_hi = this.no_localizados_hi;
            this.$data.query.boletinado = this.boletinado;
            return this.$store.dispatch('padronProveedores/empresa-boletinada/paginate', { params: this.query})
                .then(data => {
                    this.$store.commit('padronProveedores/empresa-boletinada/SET_EMPRESAS', data.data);
                    this.$store.commit('padronProveedores/empresa-boletinada/SET_META', data.meta);
                })
                .finally(() => {
                    this.cargando = false;
                })
        },
    },
    computed: {
        empresas(){
            return this.$store.getters['padronProveedores/empresa-boletinada/empresas'];
        },
        meta(){
            return this.$store.getters['padronProveedores/empresa-boletinada/meta'];
        },
        tbodyStyle() {
            return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
        }
    },
    watch: {
        empresas: {
            handler(empresas) {
                let self = this
                self.$data.data = []
                self.$data.data = empresas.map((empresa, i) => ({
                    index: (i + 1) + self.query.offset,
                    rfc: empresa.rfc,
                    razon_social: empresa.razon_social,
                    motivo: empresa.motivo,
                    observaciones: empresa.observaciones,
                }));
            },
            deep: true
        },

        no_localizados(no_localizados) {
            this.$data.query.mala_experiencia = this.mala_experiencia;
            this.$data.query.en_juicio = this.en_juicio;
            this.$data.query.efos_presuntos = this.efos_presuntos;
            this.$data.query.efos_definitivos = this.efos_definitivos;
            this.$data.query.no_localizados = no_localizados;
            this.$data.query.efos_presuntos_hi = this.efos_presuntos_hi;
            this.$data.query.efos_definitivos_hi = this.efos_definitivos_hi;
            this.$data.query.no_localizados_hi = this.no_localizados_hi;
            this.$data.query.boletinado = this.boletinado;
            this.query.offset = 0;
            this.paginate()
        },

        efos_definitivos(efos_definitivos) {
            this.$data.query.mala_experiencia = this.mala_experiencia;
            this.$data.query.en_juicio = this.en_juicio;
            this.$data.query.efos_presuntos = this.efos_presuntos;
            this.$data.query.efos_definitivos = efos_definitivos;
            this.$data.query.no_localizados = this.no_localizados;
            this.$data.query.efos_presuntos_hi = this.efos_presuntos_hi;
            this.$data.query.efos_definitivos_hi = this.efos_definitivos_hi;
            this.$data.query.no_localizados_hi = this.no_localizados_hi;
            this.$data.query.boletinado = this.boletinado;
            this.query.offset = 0;
            this.paginate()
        },

        efos_presuntos(efos_presuntos) {
            this.$data.query.mala_experiencia = this.mala_experiencia;
            this.$data.query.en_juicio = this.en_juicio;
            this.$data.query.efos_presuntos = efos_presuntos;
            this.$data.query.efos_definitivos = this.efos_definitivos;
            this.$data.query.no_localizados = this.no_localizados;
            this.$data.query.efos_presuntos_hi = this.efos_presuntos_hi;
            this.$data.query.efos_definitivos_hi = this.efos_definitivos_hi;
            this.$data.query.no_localizados_hi = this.no_localizados_hi;
            this.$data.query.boletinado = this.boletinado;
            this.query.offset = 0;
            this.paginate()
        },

        en_juicio(en_juicio) {
            this.$data.query.mala_experiencia = this.mala_experiencia;
            this.$data.query.en_juicio = en_juicio;
            this.$data.query.efos_presuntos = this.efos_presuntos;
            this.$data.query.efos_definitivos = this.efos_definitivos;
            this.$data.query.no_localizados = this.no_localizados;
            this.$data.query.efos_presuntos_hi = this.efos_presuntos_hi;
            this.$data.query.efos_definitivos_hi = this.efos_definitivos_hi;
            this.$data.query.no_localizados_hi = this.no_localizados_hi;
            this.$data.query.boletinado = this.boletinado;
            this.query.offset = 0;
            this.paginate()
        },

        mala_experiencia(mala_experiencia) {
            this.$data.query.mala_experiencia = mala_experiencia;
            this.$data.query.en_juicio = this.en_juicio;
            this.$data.query.efos_presuntos = this.efos_presuntos;
            this.$data.query.efos_definitivos = this.efos_definitivos;
            this.$data.query.no_localizados = this.no_localizados;
            this.$data.query.efos_presuntos_hi = this.efos_presuntos_hi;
            this.$data.query.efos_definitivos_hi = this.efos_definitivos_hi;
            this.$data.query.no_localizados_hi = this.no_localizados_hi;
            this.$data.query.boletinado = this.boletinado;
            this.query.offset = 0;
            this.paginate()
        },

        no_localizados_hi(no_localizados_hi) {
            this.$data.query.mala_experiencia = this.mala_experiencia;
            this.$data.query.en_juicio = this.en_juicio;
            this.$data.query.efos_presuntos = this.efos_presuntos;
            this.$data.query.efos_definitivos = this.efos_definitivos;
            this.$data.query.no_localizados = this.no_localizados;
            this.$data.query.efos_presuntos_hi = this.efos_presuntos_hi;
            this.$data.query.efos_definitivos_hi = this.efos_definitivos_hi;
            this.$data.query.no_localizados_hi = no_localizados_hi;
            this.$data.query.boletinado = this.boletinado;
            this.query.offset = 0;
            this.paginate()
        },

        efos_definitivos_hi(efos_definitivos_hi) {
            this.$data.query.mala_experiencia = this.mala_experiencia;
            this.$data.query.en_juicio = this.en_juicio;
            this.$data.query.efos_presuntos = this.efos_presuntos;
            this.$data.query.efos_definitivos = this.efos_definitivos;
            this.$data.query.no_localizados = this.no_localizados;
            this.$data.query.efos_presuntos_hi = this.efos_presuntos_hi;
            this.$data.query.efos_definitivos_hi = efos_definitivos_hi;
            this.$data.query.no_localizados_hi = this.no_localizados_hi;
            this.$data.query.boletinado = this.boletinado;
            this.query.offset = 0;
            this.paginate()
        },

        efos_presuntos_hi(efos_presuntos_hi) {
            this.$data.query.mala_experiencia = this.mala_experiencia;
            this.$data.query.en_juicio = this.en_juicio;
            this.$data.query.efos_presuntos = this.efos_presuntos;
            this.$data.query.efos_definitivos = this.efos_definitivos;
            this.$data.query.no_localizados = this.no_localizados;
            this.$data.query.efos_presuntos_hi = efos_presuntos_hi;
            this.$data.query.efos_definitivos_hi = this.efos_definitivos_hi;
            this.$data.query.no_localizados_hi = this.no_localizados_hi;
            this.$data.query.boletinado = this.boletinado;
            this.query.offset = 0;
            this.paginate()
        },

        boletinado(boletinado) {
            this.$data.query.mala_experiencia = this.mala_experiencia;
            this.$data.query.en_juicio = this.en_juicio;
            this.$data.query.efos_presuntos = this.efos_presuntos;
            this.$data.query.efos_definitivos = this.efos_definitivos;
            this.$data.query.no_localizados = no_localizados;
            this.$data.query.efos_presuntos_hi = this.efos_presuntos_hi;
            this.$data.query.efos_definitivos_hi = this.efos_definitivos_hi;
            this.$data.query.no_localizados_hi = this.no_localizados_hi;
            this.$data.query.boletinado = boletinado;
            this.query.offset = 0;
            this.paginate()
        },

        meta: {
            handler(meta) {
                let total = meta.pagination.total
                this.$data.total = total
            },
            deep: true
        },
        query: {
            handler(query) {
                this.paginate(query)
            },
            deep: true
        },
        search(val) {
            if (this.timer) {
                clearTimeout(this.timer);
                this.timer = null;
            }
            this.timer = setTimeout(() => {
                this.query.search = val;
                this.query.offset = 0;
                this.paginate();

            }, 500);
        },
        cargando(val) {
            $('tbody').css({
                '-webkit-filter': val ? 'blur(2px)' : '',
                'pointer-events': val ? 'none' : ''
            });
        }
    }
}
</script>

<style scoped>
label:not(.form-check-label):not(.custom-file-label) {
    font-weight: 500;
}

.btn-primary:not(:disabled):not(.disabled):active, .btn-primary:not(:disabled):not(.disabled).active, .show > .btn-primary.dropdown-toggle {
    color: #ffffff;
    background-color: #007bff;
    border-color: #005cbf;
}

.btn-primary {
    color: #007bff;
    background-color: #ffffff;
    border-color: #dee2e6;
    box-shadow: none;
}
</style>
