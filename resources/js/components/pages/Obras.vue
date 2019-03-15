<template>
    <div class="row">
        <div class="col-md-12">
            <ul class="list-group">
                <input class="form-control" placeholder="Buscar obra..." v-model="search">
            <span v-for="(grupo, i) in obrasAgrupadas">
                <li class="list-group-item disabled"><i class="fa fa-fw fa-database"></i>{{ i }}</li>
                    <a v-for="obra in grupo" href="#" class="list-group-item" @click="setContext(i, obra.id_obra)" v-bind:class="{disabled: loading}">
                    {{ obra.nombre }}
                </a>
            </span>
            </ul>




            <nav aria-label="Page navigation example">
                <ul class="pagination justify-content-center">
                    <li class="page-item ">
                        <a class="page-link" href="#" v-if="currentPage>1"tabindex="-1" @click="changePage(currentPage-1)" >Anterior</a>
                    </li>
                    <li class="page-item" v-for="page in totalPages" @click="changePage(page)"  ><a class="page-link" href="#">{{page}}</a></li>

                    <li class="page-item">
                        <a class="page-link" href="#" v-if="currentPage<totalPages" @click="changePage(currentPage+1)" >Siguiente</a>
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
            totalPages(){
                return this.$store.getters['cadeco/obras/totalPages']
            },
            currentPage(){
                return this.$store.getters['cadeco/obras/currentPage']
            }
        },

        mounted() {
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
                     this.$store.getters['cadeco/obras/obrasAgrupadas'];


                }, 650);

            }
        },
        methods: {

            fetch(){
                return this.$store.dispatch('cadeco/obras/fetch', {
                    params: {
                        search:this.search
                    }
                })
            },
            changePage(newPage){
                console.log("Siguiente: "+newPage);
                return this.$store.dispatch('cadeco/obras/fetch', {
                    params: {
                        page:newPage
                    }
                })
                this.$store.getters['cadeco/obras/obrasAgrupadas'];
            },
            setContext(database, id_obra) {
                this.loading = true;
                return new Promise((res, rej) => {
                    axios.post('/api/auth/setContext', {database: database, id_obra: id_obra})
                        .then(response => {
                            res(response.data);
                        })
                        .catch(err => {
                            rej(err)
                        })
                })
                    .then(res => {
                        this.$session.set('jwt', res.access_token)
                        this.$session.set('obra', res.obra)

                        this.$store.commit("auth/setObra", res)
                        this.$router.push({name: 'home'})
                    })
                    .then(() => {
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