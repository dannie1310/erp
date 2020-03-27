<template>
    <span>
        <div class="card">
            <div class="card-body">
				<div class="row">
					<div class="col-md-12">
						<label>
							Seleccione la obra:
						</label>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<model-list-select
                            :disabled="cargando"
                            name="id_configuracion"
                            v-model="id_configuracion"
                            option-value="id"
                            :custom-text="obraDescripcion"
                            :list="obras"
                            :placeholder="!cargando?'Seleccionar o buscar por nombre de obra o base de datos':'Cargando...'"
                            :isError="errors.has(`id_configuracion`)">
						</model-list-select>
					</div>
				</div>

			</div>
		</div>
        <configuracion-obra v-if="obra" :obra="obra" v-bind:monedas="monedas" v-bind:tipo="1"/>
        <estado-obra  v-if="obra" :obra="obra" v-bind:tipo="1"/>
    </span>
</template>

<script>
    import {ModelListSelect} from 'vue-search-select';
    import ConfiguracionObra from '../../configuracion/partials/Obra.vue'
    import EstadoObra from '../../configuracion/partials/EstadoObra.vue'
    export default {
        name: "configuracion-general-obra",
        components: {ModelListSelect, ConfiguracionObra, EstadoObra},
    data() {
            return {
                id_configuracion: '',
                configuracion: null,
                cargando: false,
                obras: [],
                obra: null,
                monedas : []
            }
        },
        mounted() {
            this.getObras();
        },
        init() {
            this.cargando = true;
            this.obras = [];
            this.configuracion = null;
            this.id_configuracion = '';
            this.obra = null;
            this.monedas = []
        },
        methods: {
            obraDescripcion(item) {
                return `${item.nombre} - (${item.base_datos})`
            },
            getObras() {
                this.obras = [];
                this.cargando = true;
                return this.$store.dispatch('seguridad/configuracion-obra/index', {
                    params: {
                        sort: 'nombre',
                        order: 'asc'
                    }
                })
                    .then(data => {
                        this.obras = data.data;
                        this.cargando = false;
                    })
            },
            getObra() {
                return this.$store.dispatch('cadeco/obras/global', {
                    id: this.configuracion.id_obra,
                    data: {id_configuracion : this.id_configuracion },
                    params: { include: [], 'logo' : true }
                }).then(data_obra => {
                    return this.$store.dispatch('cadeco/moneda/monedasGlobales')
                        .then(data => {
                            this.monedas = data.data;
                            this.obra = data_obra;
                            this.obra.configuracion = this.configuracion;
                        })

                })
            },
            getMonedas() {
                return this.$store.dispatch('cadeco/moneda/monedasGlobales')
                    .then(data => {
                        this.monedas = data.data;
                    })
            },
        },
        watch: {
            id_configuracion(value){
                if(value != '')
                {
                    this.configuracion = null;
                    this.obra = null;
                    this.obras.forEach(obra => {
                        if (obra.id == value) {
                            return this.configuracion = obra
                        }
                    })
                    this.getObra();
                }
            }
        }
    }
</script>

<style scoped>

</style>
