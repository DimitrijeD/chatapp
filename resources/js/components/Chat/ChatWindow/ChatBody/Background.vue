<template>
    <div class="body-bg outer bg-gradient-to-white from-blue-500">
        <!-- <div class="inner"></div> -->
    </div>
</template>

<script>

export default {

}

</script>

<style scoped>
.body-bg {
  position: absolute;
  left: 0px;
  z-index: -1;
  height: 100%;
}

.inner{
    height: 100%;
    width: 95%;
    margin: auto;
    background-color: #f7faff;
}

.outer{
    width: 28rem;
}
</style>