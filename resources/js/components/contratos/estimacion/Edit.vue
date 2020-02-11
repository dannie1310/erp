<template>
    <span>
        <div class="row">
			<div class="col-md-6">
				<div class="card" v-if="estimacion">
					<div class="card-header">
						<h6 class="card-title">Subcontrato</h6>
					</div>
					<div class="card-body">
						<form>
							<div class="form-group row">
								<label for="fecha" class="col-sm-2 col-form-label">Fecha</label>
								<div class="col-sm-10">
									<input
                                        name="fecha"
                                        v-validate="{required: true}"
                                        data-vv-as="Fecha"
                                        :class="{'is-invalid': errors.has('fecha')}"
                                        v-model="fecha"
                                        type="date"
                                        class="form-control"
                                        id="fecha"
                                        placeholder="Fecha">
									<div class="invalid-feedback" v-show="errors.has('fecha')">{{ errors.first('fecha') }}</div>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-2 col-form-label">Objeto</label>
								<div class="col-sm-10">
									{{ subcontrato.referencia }}
								</div>
							</div>
							<div class="form-group row" v-if="subcontrato.empresa">
								<label class="col-sm-2 col-form-label">Contratista</label>
								<div class="col-sm-10">
									{{ subcontrato.empresa.razon_social }}
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-2 col-form-label">Observaciones</label>
								<div class="col-sm-10">
									<textarea
                                        name="observaciones"
                                        id="observaciones"
                                        class="form-control"
                                        v-model="observaciones"
                                    ></textarea>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>

			<div class="col-md-6">
				<div class="card" v-if="subcontrato">
					<div class="card-header">
						<h6 class="card-title">Periodo de Estimación</h6>
					</div>
					<div class="card-body">
						<form>
							<div class="form-row">
								<div class="form-group col-md-6">
									<label for="inicio">Inicio</label>
									<input
                                        name="fecha_inicio"
                                        v-validate="{required: true}"
                                        data-vv-as="Inicio"
                                        :class="{'is-invalid': errors.has('fecha_inicio')}"
                                        v-model="fecha_inicio"
                                        type="date"
                                        class="form-control"
                                        id="fecha_inicio"
                                        placeholder="Inicio">
									<div class="invalid-feedback" v-show="errors.has('fecha_inicio')">{{ errors.first('fecha_inicio') }}</div>

								</div>
								<div class="form-group col-md-6">
									<label for="inicio">Término</label>
									<input
                                        name="fecha_fin"
                                        v-validate="{required: true}"
                                        data-vv-as="Término"
                                        :class="{'is-invalid': errors.has('fecha_fin')}"
                                        v-model="fecha_fin"
                                        type="date"
                                        class="form-control"
                                        id="fecha_fin"
                                        placeholder="Término">
									<div class="invalid-feedback" v-show="errors.has('fecha_fin')">{{ errors.first('fecha_fin') }}</div>

								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>

    </span>
</template>


<script>


import DeductivaEdit from './deductivas/Edit'
    export default {
        name: "estimacion-edit",
        components: {DeductivaEdit},
        data() {
            return {
                cargando: true,
                interval: '',
                logo: '',
                obra: [],
                id: '',
                guiones:'\xa0\xa0',
                identacion:'',
                itemIdentacion:'',

            }
        },
        mounted() {
            this.cargando = true;
            this.obra = this.$session.get('obra');
            this.id = this.$route.params.id;
            this.find();
        },
        methods: {
            find() {
                this.$store.commit('contratos/estimacion/SET_ESTIMACION', null);
                return this.$store.dispatch('contratos/estimacion/find', {
                    id: this.id,
                    params: { include: ['subcontrato.partidas_ordenadas.contrato_conceptos'] }
                }).then(data => {
                    this.$store.commit('contratos/estimacion/SET_ESTIMACION', data);
                    this.cargando = false;
                })
            },
            editar(){
                alert('Boton editar');
            },
            isObject(item){
                    return typeof item === 'object'
            },

            identacionTabla(val){
                var cant=val.length/4;

                return this.guiones.repeat(cant-1);

            },
            identacionItem(val){

                return this.guiones.repeat(val-1);
            }


        },
        computed: {
            estimacion() {
                return this.$store.getters['contratos/estimacion/currentEstimacion']
            },


        }
    }
</script>

<style scoped>

</style>
