<template>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <datatable v-bind="$data" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "Kardex-Material-Index",
    data() {
        return {
            HeaderSettings: false,
            columns: [
                { title: '#', field:'index',sortable: false},
                { title: 'Almacén', field:'descripcion', sortable: true,  thComp: require('../../globals/th-Filter').default},
                { title: 'Adquirido', field:'adquirido'},
                { title: 'Pagado', field:'pagado'},
                { title: 'Por Pagar', field:'por_pagar'},
                { title: 'Acciones', field: 'buttons', tdComp: require('./partials/ActionButtons').default},
            ],
            data: [],
            total: 0,
            query: {include:[], scope: 'tipoMaterial', sort:'descripcion', order:'asc'},
            estado: "",
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
            return this.$store.dispatch('cadeco/almacen/paginate', { params: this.query})
                .then(data => {
                    this.$store.commit('cadeco/almacen/SET_ALMACENES', data.data);
                    this.$store.commit('cadeco/almacen/SET_META', data.meta);
                })
                .finally(() => {
                    this.cargando = false;
                })
        }
    },
    computed: {
        almacenes(){
            return this.$store.getters['cadeco/almacen/almacenes'];
        },
        meta(){
            return this.$store.getters['cadeco/almacen/meta'];
        },
        tbodyStyle() {
            return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
        }
    },
    watch: {
        almacenes: {
            handler(almacenes) {
                let self = this
                self.$data.data = []
                almacenes.forEach(function (almacen, i) {
                    self.$data.data.push({
                        index: (i + 1) + self.query.offset,
                        descripcion: almacen.descripcion,
                        adquirido: almacen.totales.adquirido,
                        pagado: almacen.totales.pagado,
                        por_pagar: almacen.totales.por_pagar,
                        buttons: $.extend({}, {
                            id: almacen.id
                        })
                    })
                });
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
