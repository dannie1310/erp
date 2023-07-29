<template>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive">
                        <datatable v-bind="$data" />
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
    name: "fechas-inhabiles-sat-index",
    data() {
        return {
            HeaderSettings: false,
            columns: [
                { title: '#', field: 'index', thClass: 'th_index', tdClass: 'td_index', sortable: false },
                { title: 'Fecha Inhábil SAT', thClass: 'fecha_hora', field: 'fecha'},
                { title: 'Tipo Fecha', field: 'tipo'},
                { title: 'Usuario Registró', field: 'usuario'},
                { title: 'Fecha Hora Registro', thClass: 'fecha_hora', field: 'registro'},
                { title: '', field: 'buttons', thClass: 'th_index',  tdComp: require('./partials/ActionButtons').default},
            ],
            data: [],
            total: 0,
            query: {scope: ['vigente'], sort: 'fecha', order: 'desc'},
            estado: "",
            cargando: false,
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
            return this.$store.dispatch('fiscal/fecha-inhabil-sat/paginate', { params: this.query})
                .then(data => {
                    this.$store.commit('fiscal/fecha-inhabil-sat/SET_FECHAS_INHABILES', data.data);
                    this.$store.commit('fiscal/fecha-inhabil-sat/SET_META', data.meta);
                })
                .finally(() => {
                    this.cargando = false;
                })
        },
    },
    computed: {
        fechas_inhabiles(){
            return this.$store.getters['fiscal/fecha-inhabil-sat/fechas_inhabiles'];
        },
        meta(){
            return this.$store.getters['fiscal/fecha-inhabil-sat/meta'];
        },
        tbodyStyle() {
            return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
        }
    },
    watch: {
        fechas_inhabiles: {
            handler(fechas_inhabiles) {
                let self = this
                self.$data.data = []
                self.$data.data = fechas_inhabiles.map((fecha, i) => ({
                    index: (i + 1) + self.query.offset,
                    fecha: fecha.fecha_format,
                    tipo: fecha.tipo_fecha.descripcion,
                    usuario: fecha.usuario_registro_format,
                    registro: fecha.fecha_registro_format,
                    buttons: $.extend({}, {
                        id: fecha.id,
                        eliminar: (self.$root.can('eliminar_fechas_inhabiles_sat',true)) ? true : false,
                    })
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

<style>

</style>
