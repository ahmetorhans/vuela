<template>
  <q-layout view="hhr lpr lFf">
    <q-layout-header>
      <q-toolbar color="secondary">
        <q-btn flat dense round @click="leftDrawerOpen = !leftDrawerOpen">
          <q-icon name="menu" />
        </q-btn>

        <q-toolbar-title>
          My app
          <div slot="subtitle">v 1.0</div>
        </q-toolbar-title>
       
      </q-toolbar>
    </q-layout-header>

    <q-layout-drawer v-model="leftDrawerOpen" content-class="bg-grey-2" :content-style="{width: '240px'}">
      <q-list no-border link inset-delimiter v-if="yuklendi">
        <q-list-header>{{$store.state.auth.guard.name}}
          <small style="cursor:pointer" @click="$router.push('/logout')">[Çıkış]</small>
        </q-list-header>

        <q-item to="/servisler" class="qitemH">
          <q-item-side icon="store" />
          <q-item-main label="Servis" />
        </q-item>
       
        <q-item to="/kullanicilar" class="qitemH">
          <q-item-side icon="supervisor_account" />
          <q-item-main label="Kullanıcılar" />
        </q-item>

        <q-item to="/ayarlar" class="qitemH">
          <q-item-side icon="settings" />
          <q-item-main label="Ayarlar" />
        </q-item>
      </q-list>
    </q-layout-drawer>
    <q-layout-drawer v-if="guard=='kendi'" side="right" v-model="rightDrawerOpen" content-class="bg-grey-2">
      <rapor-item />
    </q-layout-drawer>

    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script>
import { openURL } from 'quasar'
import store from '../store'


export default {
  name: 'LayoutDefault',

  data () {

    return {
      leftDrawerOpen: true,
      yuklendi: false,
      rightDrawerOpen: false,
    }
  },

  computed: {
    guard () {


      if (this.$store.state.auth.authLoaded)
        this.yuklendi = true;

      let role = this.$store.state.auth.guard.userGroup;

      console.log(this.$store.state.auth.guard);

      if (role === 'musteri')
        return 'musteri'
      else
        return 'kendi'
    },

  }
}
</script>

<style>
.qitemH {
  height: 55px;
}
</style>
