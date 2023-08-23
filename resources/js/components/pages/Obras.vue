<template>
    <div class="row">
        <div class="col-md-12">
            <ul class="list-group">
                <input class="form-control" placeholder="Buscar obra..." v-model="search">
                <span v-for="(grupo, i) in obrasAgrupadas" style="margin-bottom: 10px">
                    <li class="list-group-item disabled"><i class="fa fa-fw fa-database"></i>{{ i }}</li>
                    <a v-for="obra in grupo" href="#" class="list-group-item" @click="setContext(i, obra.id_obra)" v-bind:class="{disabled: loading}">{{ obra.nombre }}</a>
                </span>
            </ul>

            <nav aria-label="Page navigation example" v-if="!(Object.keys(meta).length === 0 && meta.constructor === Object)" totalPages="meta.pagination.total_pages">
                <ul class="pagination justify-content-center">
                    <li class="page-item ">
                        <button class="page-link"  v-if="meta.pagination.current_page>1" tabindex="-1" @click="changePage(meta.pagination.current_page-1)" >Anterior</button>
                    </li>

                    <li class="page-item" v-if="meta.pagination.total_pages>1"v-for="page in meta.pagination.total_pages" @click="changePage(page)" v-bind:class="{active : page == meta.pagination.current_page}">
                        <button class="page-link">{{page}}</button>
                    </li>

                    <li class="page-item">
                        <button class="page-link" v-if="meta.pagination.current_page<meta.pagination.total_pages"@click="changePage(meta.pagination.current_page+1)" >Siguiente</button>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</template>

<script>
    import { mapActions } from 'vuex'

    export default {
        name: "Obras",
        data() {
            return {
                loading: false,
                search:''
            }
        },

        computed: {
            obrasAgrupadas() {
                return this.$store.getters['cadeco/obras/obrasAgrupadas']
            },
            meta() {
                return this.$store.getters['cadeco/obras/meta']
            }
        },

        mounted() {
            this.$store.commit('auth/setObra', { obra: null });
            this.$store.commit('auth/setPermisos', { permisos: [] });
            this.$store.commit('auth/setEmpresa', null);

            this.$session.remove('permisos');
            this.$session.remove('db');
            this.$session.remove('id_obra');
            this.$session.remove('sistemas');
            this.$session.remove('id_empresa');
            this.$session.remove('empresa');
            this.fetch();
        },

        watch:{
            search(val) {
                if (this.timer) {
                    clearTimeout(this.timer);
                    this.timer = null;
                }
                this.timer = setTimeout(() => {
                     this.fetch();
                }, 550);
            },
        },
        methods: {
            fetch(){
                this.$store.commit('cadeco/obras/SET_OBRAS', []);
                this.$store.commit('cadeco/obras/SET_META', {});

                return this.$store.dispatch('cadeco/obras/paginate', {
                    params: {
                        search:this.search
                    }
                })
            },
            changePage(newPage){
                return this.$store.dispatch('cadeco/obras/paginate', {
                    params: {
                        page:newPage
                    }
                });
            },
            setContext(database, id_obra) {
                this.loading = true;
                this.$session.set('permisos', [])
                this.$store.commit("auth/setPermisos", [])

                delete window.axios.defaults.headers.common['db'];
                delete window.axios.defaults.headers.common['idobra'];

                return new Promise((res, rej) => {
                    axios.post('/auth/setContext', {db: database, id_obra: id_obra})
                        .then(r => r.data)
                        .then(response => {
                            res(response);
                        })
                        .catch(err => {
                            rej(err)
                        })
                })
                    .then(res => {
                        this.$session.set('permisos', res.permisos)
                        this.$store.commit("auth/setPermisos", res)
                        this.$session.set('db', database)
                        this.$session.set('id_obra', id_obra)
                        this.$store.commit("auth/setObra", res)
                        this.$session.set('id_empresa', res.obra.datos_contables.id);
                        this.$store.commit("auth/setEmpresa", res.obra.datos_contables.empresa);
                        this.$router.push({name: 'home'})
                    })
                    .finally(() => {
                        this.loading = false;
                    })
            }
        }
    }
</script>
<style scoped>
    a.disabled {
        pointer-events: none;
        cursor: default;
    }
    input {
        margin-bottom: 20px;
    }
    nav{
        margin-top: 25px;
    }
</style>
