<!--<template>-->
<!--  <div>-->
<!--    <input type="file" @change="onFileChange" accept="image/*" />-->
<!--    <div v-if="imageSrc">-->
<!--      <img ref="image" :src="imageSrc" alt="Image to crop" />-->
<!--    </div>-->
<!--    <button type="button" class="btn btn-primary btn-sm" v-if="imageSrc" @click="cropImage">Crop Image</button>-->
<!--&lt;!&ndash;    <div v-if="croppedImage">&ndash;&gt;-->
<!--&lt;!&ndash;      <h3>Cropped Image:</h3>&ndash;&gt;-->
<!--&lt;!&ndash;      <img :src="croppedImage" alt="Cropped Image" />&ndash;&gt;-->
<!--&lt;!&ndash;    </div>&ndash;&gt;-->
<!--  </div>-->
<!--</template>-->

<!--<script>-->
<!--//import Cropper from 'cropperjs';-->
<!--//import 'cropperjs/dist/cropper.css';-->
<!--import {bus} from "../../app";-->

<!--export default {-->
<!--  data() {-->
<!--    return {-->
<!--      imageSrc: '',-->
<!--      cropper: null,-->
<!--      croppedImage: ''-->
<!--    };-->
<!--  },-->
<!--  methods: {-->
<!--    onFileChange(event) {-->
<!--      const file = event.target.files[0];-->
<!--      if (file) {-->
<!--        const reader = new FileReader();-->
<!--        reader.onload = (e) => {-->
<!--          this.imageSrc = e.target.result;-->
<!--          this.$nextTick(() => {-->
<!--            this.initCropper();-->
<!--          });-->
<!--        };-->
<!--        reader.readAsDataURL(file);-->
<!--      }-->
<!--    },-->
<!--    initCropper() {-->
<!--      const image = this.$refs.image;-->
<!--      this.cropper = new Cropper(image, {-->
<!--        aspectRatio: 2, // Set aspect ratio (e.g., 1 for square)-->
<!--        viewMode: 1, // Restrict the crop box to not exceed the size of the canvas-->
<!--        autoCropArea: 0.8, // Define the initial crop area size-->
<!--        movable: true,-->
<!--        zoomable: true,-->
<!--        rotatable: true,-->
<!--        scalable: true-->
<!--      });-->
<!--    },-->
<!--    cropImage() {-->
<!--      // const croppedCanvas = this.cropper.getCroppedCanvas();-->
<!--      // this.croppedImage = croppedCanvas.toDataURL('image/jpeg');-->
<!--      // bus.$emit('backToModal',this.croppedImage)-->
<!--    }-->
<!--  },-->
<!--  beforeDestroy() {-->
<!--    if (this.cropper) {-->
<!--      this.cropper.destroy();-->
<!--    }-->
<!--  }-->
<!--};-->
<!--</script>-->

<!--<style scoped>-->
<!--img {-->
<!--  max-width: 100%;-->
<!--  height: auto;-->
<!--}-->
<!--</style>-->