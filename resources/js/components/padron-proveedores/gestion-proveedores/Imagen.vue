<template>
    <span>
        <div id="myCarousel" class="carousel slide carousel-fade" data-ride="carousel" v-if="imagenes">
            <div class="carousel-inner">
                <div v-for="(imagen,i) in imagenes" class="carousel-item" :class="i==0 ? 'active' : ''">
                    <img :src="imagen['imagen']" :alt="imagen['descripcion']" width="1200" height="800">
                    <div class="carousel-caption">
                        <h3 style="color:#7BC143">{{imagen['descripcion']}}</h3>
                    </div>
                </div>
            </div>
            <a class="carousel-control-prev" href="#myCarousel" data-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </a>
            <a class="carousel-control-next" href="#myCarousel" data-slide="next">
                <span class="carousel-control-next-icon"></span>
            </a>
        </div>
    </span>
</template>

<script>
    export default {
        name: "Imagen",
        props: ['imagenes'],
    }
</script>
<style scoped>
    .carousel-inner img {
        width: 100%;
        height: 100%;
    }
</style>
