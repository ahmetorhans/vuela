<template>

    <q-layout view="hhr lpr lFf" style="background-color:#efefef">

        <div id="container">

            <div class="box shadow-8" style="background-color:#fff;padding:20px;">
                <q-field>
                    <div class="q-headline" style="padding:15px 0px">Parolanızı Sıfırlayın</div>

                </q-field>
                <q-field label="Eposta Adresi" :label-width="4" class="fip">
                    <q-input v-model="user.email" required />
                </q-field>

                <q-field class="fip">
                    <q-btn color="primary" @click="login">Gönder</q-btn>
                </q-field>
            </div>

        </div>

    </q-layout>
</template>
<script>
import axios from "axios";
import notify from './notify';
export default {
    data () {
        return {
         
            user: {}
        };
    },
    methods: {


        login: function () {
            
            
            axios
                .post(this.apiUrl + "auth/resetPassword", this.user)
                .then(res => {
                    if (res.data.status === false) {
                        this.errors = res.data.msg;
                        notify(res.data.msg, true);
                    } else {
                        notify(res.data.msg);
                          this.$router.push('/login');
                    }
                })
        },
    }
};
</script>