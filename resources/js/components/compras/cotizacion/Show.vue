<template>
    <span>
        <div class="row">
            <div class="col-12">
                 <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <cotizacion-partial-show v-bind:id="this.id"></cotizacion-partial-show>
                            </div>
                        </div>
                    </div>
                 </div>
            </div>
        </div>
    </span>
</template>

<script>
    import CotizacionPartialShow from "./partials/PartialShow";
    export default {
        name: "cotizacion-show",
        components: {CotizacionPartialShow},
        props: ['id', 'show'],
        data(){
            return{
                cargando: false,
                no_cotizados: [],
                items: [],
                cuenta: [],
                x: 0,
                t: 0,
            }
        },
        mounted() {
            //this.find();
        },
        methods: {
            find() {

                this.cargando = true;
                this.$store.commit('compras/cotizacion/SET_COTIZACION', null);
                return this.$store.dispatch('compras/cotizacion/find', {
                    id: this.id,
                    params:{include: ['empresa', 'sucursal', 'complemento', 'partidas']}
                }).then(data => {
                    this.$store.commit('compras/cotizacion/SET_COTIZACION', data);
                    this.items = data.partidas.data;
                })
                .finally(() => {
                    this.cargando = false;
                })
            }
        },
        computed: {
            cotizacion() {
                return this.$store.getters['compras/cotizacion/currentCotizacion']
            }
        },
        watch: {
            items()
            {
                this.x = 0;
                this.t = 0;
                while(this.x < this.items.length)
                {
                    this.no_cotizados[this.x] = this.items[this.x].no_cotizado;
                    this.cuenta[this.x] = (this.no_cotizados[this.x]) ? this.t ++ : 0;
                    this.x ++;
                }
            }
        }
    }
</script>

<style scoped>

</style>
