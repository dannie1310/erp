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
                            name="id_obra"
                            v-model="id_obra"
                            option-value="id"
                            :custom-text="obraDescripcion"
                            :list="obras"
                            :placeholder="!cargando?'Seleccionar o buscar por nombre de obra o base de datos':'Cargando...'"
                            :isError="errors.has(`id_obra`)">
						</model-list-select>
					</div>
				</div>
			</div>
		</div>
    </span>
</template>

<script>
    import {ModelListSelect} from 'vue-search-select';
    export default {
        name: "configuracion-obra",
        components: {ModelListSelect},
        data() {
            return {
                id_obra: '',
                obra: null,
                cargando: false,
                obras: [],
            }
        },
        mounted() {
            this.getObras();
        },
        init() {
            this.cargando = true;
            this.obras = [];
            this.obra = null;
            this.id_obra = '';
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
        },
        watch: {
            id_obra(value){
                if(value != '')
                {
                    console.log(value)
                    return this.$store.dispatch('seguridad/configuracion-obra/establecerContexto', {
                        id: value,
                        params: {

                        }
                    })
                    .then(data => {
                       console.log(data)
                    })
                }
            }
        }
    }
</script>

<style scoped>

</style>
