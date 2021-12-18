<template>
    <loader></loader>
</template>

<script>
    import api from "../api"

    import Loader from "../components/Loader";

    export default {
        name: "EmailConfirm",
        components: {Loader},
        mounted() {
            api.post('/auth/email/confirm', {token: this.$route.params.token})
            .then(response => {
                this.$router.push({name: 'news'});
            });
        }
    }
</script>