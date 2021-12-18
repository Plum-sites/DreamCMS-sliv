<template>
    <app-card
            :heading="server.name"
            :fullBlock="true"
            :footer="true"
            colClasses="xs12 sm6"
    >
        <vue-perfect-scrollbar style="height: 410px" :settings="settings">
            <v-list class="notification-wrap">
                <template v-for="message in messages" style="height: 100%">
                    <v-list-tile avatar ripple style="height: 100%">
                        <v-list-tile-avatar style="height: 100%">
                            <v-icon :color="message.side === 'Server' ? 'success' : 'primary'">notifications_active</v-icon>
                        </v-list-tile-avatar>
                        <v-list-tile-content style="height: 100%">
                            <v-list-tile-sub-title class="grey--text fs-12 fw-normal">{{ message.side }}</v-list-tile-sub-title>
                            <v-list-tile-title style="height: 100%"><h6 class="mb-0"><span v-html="message.msg"></span></h6></v-list-tile-title>
                        </v-list-tile-content>
                    </v-list-tile>
                </template>
            </v-list>
        </vue-perfect-scrollbar>

        <div class="d-custom-flex justify-space-between" slot="footer">
            <v-btn small color="primary">Отправить</v-btn>
        </div>
    </app-card>
</template>

<script>
    export default {
        props: ['server'],
        name: "ServerConsole",
        data: function(){
            return {
                loading: false,
                messages: [],
                settings: {
                    maxScrollbarLength: 150
                }
            }
        },
        methods:{
            setLoading(loading){
                this.loading = loading;
            },
            addMessage(message){
                this.messages.push(message);
            }
        }
    }
</script>

<style scoped>

</style>