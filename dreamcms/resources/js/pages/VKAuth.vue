<template>
    <loader></loader>
</template>

<script>
    import api, {AUTH_SUCCESS, LOAD_USER} from "../api"

    import Loader from "../components/Loader";

    export default {
        name: "VKAuth",
        components: {Loader},
        mounted() {
            api.get('auth/vk/' + this.$route.params.type + '?code=' + this.$route.query.code)
            .then(response => {
                switch (this.$route.params.type) {
                    case 'login':
                        if (response.data.success){
                            this.$store.dispatch(AUTH_SUCCESS, response.data.token);
                            this.$store.dispatch(LOAD_USER);

                            this.$socket.emit('authenticate', {token: response.data.token});

                            this.$router.push({name: 'news'});
                        }
                        break;

                    case 'link':
                        if (response.data.success){
                            this.$router.push({name: 'security'});
                        }
                        break;

                    case 'recovery':
                        if (response.data.success){
                            this.$router.push({name: 'reset', params: {token: response.data.recovery_token}});
                        }
                        break;

                    default:
                        this.$router.push({name: 'news'});
                }
            });
        }
    }
</script>