<template>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <!-- /.card-header -->
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
                { title: 'Motivo', tdClass:'center', field: 'motivo', sortable: true, thClass:'th_c100', thComp: require('../globals/th-Filter').default},
                { title: 'Observaciones', field: 'observaciones',  sortable: true},
            ],
            data: [],
            total: 0,
            query: {scope:'', sort: '', order: ''},
            cargando: false
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

</style>
