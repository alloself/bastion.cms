<template>
  <section class="main-block">
    <div class="block-bg"></div>
    <div class="block-bg__elem block-bg__elem--left" :key="'false'"></div>
    <div class="block-bg__elem block-bg__elem--right" :key="'false'"></div>
    <div class="block-bg__elem block-bg__elem--center" :key="'false'"></div>
    <div class="block-bg__elem block-bg__elem--line" :key="'false'"></div>
    <v-container class="fill-height" fluid>
      <v-row :align="'center'" justify="center" no-gutters class="fill-height">
        <v-col cols="12" sm="8" md="4">
          <v-card class="elevation-12" @keyup.enter="handler">
            <v-toolbar dark flat>
              <Logo></Logo>
              <v-toolbar-title>Вход</v-toolbar-title>
              <v-spacer></v-spacer>
            </v-toolbar>
            <v-card-text>
              <smart-form :loading="loading" :fields="fields" v-model:form="form"></smart-form>
            </v-card-text>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn depressed @click="handler" :loading="loading">Вход</v-btn>
            </v-card-actions>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </section>
</template>
<script lang="ts" setup>
import { useUserStore } from '@admin/entities/user';
import type { FormContext } from 'vee-validate';
import { useRouter } from 'vue-router';
import { createFields } from '@admin/entities/user';
import { ref } from 'vue';
import { loading } from "@admin/shared/api/axios";
import { Logo, SmartForm } from '@admin/shared/components';
import { useFormSubmit } from '@admin/shared/composables';

const userStore = useUserStore();
const router = useRouter();
const fields = createFields();

const form = ref<FormContext>();

const { handler } = useFormSubmit(async () => {
  await userStore.login(form.value?.values);
  router.push("/");
}, form);
</script>
<style lang="scss">
$imgpath: "@admin/shared/assets/svg/";
$blue: #096ed1;
$sm: 576px;
$md: 768px;
$lg: 992px;
$xl: 1200px;
$xxl: 1920px;

.main-block {
  position: fixed;
  z-index: 0;
  width: 100%;
  height: 100vh;
  background: url("@admin/shared/assets/img/login-bg.png") center top no-repeat;
  background-color: #000;
  background-size: 700% auto;
  transition: all 0.7s;
  overflow: hidden;

  @media (min-width: $md) {
    background-size: 500% auto;
  }

  @media (min-width: $xl) {
    background-size: auto auto;
  }
}

.block-bg {
  position: absolute;
  z-index: -1;
  left: 0;
  right: 0;
  top: 0;
  bottom: 0;
  overflow: hidden;
  pointer-events: none;
  transform: translate3d(0, 0, 0);

  &__elem {
    z-index: -1;
    position: absolute;
    width: 674px;
    height: 674px;
    background-repeat: no-repeat;
    background-position: center center;
    background-size: 100% 100%;

    @media (min-width: $xl) {
      width: 874px;
      height: 874px;
    }

    svg {
      width: 100%;
      height: 100%;
    }

    &--line {
      width: 2px;
      left: 50%;
      top: 167px;
      height: auto;
      bottom: 0;
      transform: translateX(-50%);
      animation: opacity 3s linear;

      @media (min-width: $xl) {
        top: 219px;
      }

      &:before {
        content: "";
        position: absolute;
        left: 0;
        top: 100%;
        width: 2px;
        height: 30px;
        background: url($imgpath + "line.svg") center top no-repeat;
        background-size: 100% 100%;
        animation: to_top_line 3s linear infinite;
      }
    }

    &--left {
      background-image: url($imgpath + "romb-left.svg");
      left: 0;
      bottom: 0;
      transform: translate(-80%, 60%);
      animation: opacity 2s 1s linear;

      @media (min-width: $md) {
        transform: translate(-70%, 50%);
      }

      @media (min-width: $xl) {
        transform: translate(-60%, 40%);
      }
    }

    &--right {
      background-image: url($imgpath + "romb-right.svg");
      right: 0;
      bottom: 0;
      transform: translate(80%, 60%);
      animation: opacity 2s 1s linear;

      @media (min-width: $md) {
        transform: translate(70%, 50%);
      }

      @media (min-width: $xl) {
        transform: translate(60%, 40%);
      }
    }

    &--center {
      top: 0;
      left: 50%;
      background-image: url($imgpath + "romb-center.svg");
      transform: translate(-50%, -75%);
      animation: opacity 1s linear;
    }
  }
}

.lines {
  transform: rotate(45deg);
  transform-origin: center center;
}

.logo {
  height: 100%;
  display: flex;
  align-items: center;
  height: 32px;

  svg {
    height: 100%;
  }
}

.color-1 {
  fill: var(--v-primary-base);
}

.color-2 {
  fill: #fff;
  opacity: 0.3;
  isolation: isolate;
}

@keyframes opacity {
  from {
    opacity: 0;
  }

  to {
    opacity: 1;
  }
}

@keyframes to_top_line {
  0% {
    top: 100%;
  }

  99% {
    opacity: 0;
  }

  100% {
    opacity: 0;
    top: 0;
  }
}
</style>
