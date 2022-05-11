<template>
  <div class="seo-media-field">
    <div class="seo-media-droparea" v-if="!previewUrl">
      <img :src="value" v-if="value" />
      <div class="seo-media-droparea__label" v-else>
        {{ __('Click or drop file here' )}}
        <br />
        <i>{{ __('Best size for image (1200 x 630)') }}</i>
      </div>
      <div
        class="seo-media-droparea__above seo-media-droparea__label"
        v-if="value"
      >{{ __('Click or drop file to replace') }}</div>
      <input type="file" class="seo-media-input" max="1" @change="whenUploadFile($event)" />
    </div>
    <div
      class="seo-media-preview"
      v-if="previewUrl"
      :style="{height: previewHeight, width: previewWidth}"
    >
      <div class="seo-media-preview__wrapper">
        <img
          ref="previewFile"
          :src="previewUrl"
          class="seo-media-preview__input"
          :style="{transform: previewTransform}"
          @load="onPreviewImageLoaded"
        />
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    value: String
  },
  data() {
    return {
      previewUrl: null,
      previewHeight: null,
      previewWidth: null,
      previewTransformX: -50,
      previewTransformY: -50,
      startTransformX: null,
      startTransformY: null,
      previewDragClone: null
    };
  },
  computed: {
    previewTransform() {
      return (
        "translate(" +
        this.previewTransformX +
        "%, " +
        this.previewTransformY +
        "%)"
      );
    }
  },
  methods: {
    handleDragStart(event) {
      const clone = document.createElement("div");
      clone.style.opacity = 0; // use opacity or
      clone.className = "seo-media-image__ghost";
      document.body.appendChild(clone);
      event.dataTransfer.setDragImage(clone, 0, 0);
      this.previewDragClone = clone;

      this.startTransformX = this.previewTransformX;
      this.startTransformY = this.previewTransformY;
    },
    handleDragEnd(event) {
      if (this.previewDragClone) {
        this.previewDragClone.parentNode.removeChild(this.previewDragClone);
      }
    },
    handleDrag(event) {
      event.dataTransfer.setDragImage(this.previewDragClone, 0, 0);
      if (event.clientX && event.clientY) {
        const h = this.previewHeight;
        const w = this.previewWidth;
        const x = this.startTransformX + (event.layerX / h) * 100;
        const y = this.startTransformY + (event.layerY / w) * 100;

        this.previewTransformX = x;
        this.previewTransformY = y;
      }
    },
    onPreviewImageLoaded() {
      const img = this.$refs.previewFile;

      this.previewHeight = img.height || img.clientHeight || img.offsetHeight;
      this.previewWidth = img.width || img.clientWidth || img.offsetWidth;
    },
    whenUploadFile(event) {
      const [file] = event.target.files;

      const reader = new FileReader();
      reader.onload = () => {
        const dataURL = reader.result;
        this.previewUrl = dataURL;
      };
      reader.readAsDataURL(file);

      console.log('ss', file);
      return this.$emit("imageSelect", file);
    }
  }
};
</script>

<style lang="scss" scoped>
.seo-media-droparea {
  position: relative;
  overflow: hidden;
  background: #eeeeee;
  border-radius: 2px;
  border: 1px solid #dddddd;
  height: 0;
  padding-top: 52.5%;
  cursor: pointer;
}

.dark .seo-media-droparea {
    background: rgba(var(--colors-gray-900),var(--tw-bg-opacity));
    border: rgba(var(--colors-gray-700),var(--tw-border-opacity));
}

.seo-media-droparea img {
  display: block;
  width: 100%;
  height: auto;
  position: absolute;
  left: 0;
  top: 0;
  z-index: 5;
}
.seo-media-droparea__label {
  font-weight: bold;
  position: absolute;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}
.seo-media-droparea__above {
  z-index: 4;
  color: #ffffff;
}
.seo-media-droparea:hover::after {
  content: "";
  background: #000000;
  opacity: 0.4;
  position: absolute;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  z-index: 6;
}
.seo-media-droparea:hover .seo-media-droparea__above {
  z-index: 7;
}
.seo-media-input {
  opacity: 0;
  cursor: pointer;
  position: absolute;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  z-index: 10;
}
.seo-media-preview {
  overflow: hidden;
  display: block;
}
.seo-media-preview__wrapper {
  width: 100%;
  height: 0px;
  position: relative;
  padding-top: 52.5%;
  background: #eeeeee;
  border: 1px solid #dddddd;
}
.seo-media-preview__wrapper .seo-media-preview__input {
  position: absolute;
  left: 50%;
  top: 50%;
  min-width: 100%;
  transform: translate(-50%, -50%);
  z-index: 1;
  cursor: move;
}
.seo-media-image__ghost {
  cursor: move;
}
</style>
