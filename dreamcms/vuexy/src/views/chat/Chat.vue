<template>
    <!-- Need to add height inherit because Vue 2 don't support multiple root ele -->
    <div style="height: inherit">
        <div
            class="body-content-overlay"
            :class="{'show': shallShowUserProfileSidebar || shallShowActiveChatContactSidebar || mqShallShowLeftSidebar}"
            @click="mqShallShowLeftSidebar=shallShowActiveChatContactSidebar=shallShowUserProfileSidebar=false"
        />

        <!-- Main Area -->
        <section class="chat-app-window">

            <!-- Start Chat Logo -->
            <div
                v-if="!activeChat.contact"
                class="start-chat-area"
            >
                <div class="mb-1 start-chat-icon">
                    <feather-icon
                        icon="MessageSquareIcon"
                        size="56"
                    />
                </div>
                <h4
                    class="sidebar-toggle start-chat-text"
                    @click="startConversation"
                >
                    Выберите сервер
                </h4>
            </div>

            <!-- Chat Content -->
            <div
                v-else
                class="active-chat"
            >
                <!-- Chat Navbar -->
                <div class="chat-navbar">
                    <header class="chat-header">

                        <!-- Avatar & Name -->
                        <div class="d-flex align-items-center">

                            <!-- Toggle Icon -->
                            <div class="sidebar-toggle d-block d-lg-none mr-1">
                                <feather-icon
                                    icon="MenuIcon"
                                    class="cursor-pointer"
                                    size="21"
                                    @click="mqShallShowLeftSidebar = true"
                                />
                            </div>

<!--                            <b-avatar-->
<!--                                size="36"-->
<!--                                :src="activeChat.contact.avatar"-->
<!--                                class="mr-1 cursor-pointer badge-minimal"-->
<!--                                badge-->
<!--                                badge-variant="success"-->
<!--                                @click.native="shallShowActiveChatContactSidebar=true"-->
<!--                            />-->
                            <h6 class="mb-0">
                                {{ activeChat.contact.fullName }}
                            </h6>
                        </div>

                        <!-- Contact Actions -->
                        <div class="d-flex align-items-center">
<!--                            <feather-icon-->
<!--                                icon="PhoneCallIcon"-->
<!--                                size="17"-->
<!--                                class="cursor-pointer d-sm-block d-none mr-1"-->
<!--                            />-->
<!--                            <div class="dropdown">-->
<!--                                <b-dropdown-->
<!--                                    variant="link"-->
<!--                                    no-caret-->
<!--                                    toggle-class="p-0"-->
<!--                                    right-->
<!--                                >-->
<!--                                    <template #button-content>-->
<!--                                        <feather-icon-->
<!--                                            icon="MoreVerticalIcon"-->
<!--                                            size="17"-->
<!--                                            class="align-middle text-body"-->
<!--                                        />-->
<!--                                    </template>-->
<!--                                    <b-dropdown-item>-->
<!--                                        View Contact-->
<!--                                    </b-dropdown-item>-->
<!--                                    <b-dropdown-item>-->
<!--                                        Mute Notifications-->
<!--                                    </b-dropdown-item>-->
<!--                                    <b-dropdown-item>-->
<!--                                        Block Contact-->
<!--                                    </b-dropdown-item>-->
<!--                                    <b-dropdown-item>-->
<!--                                        Clear Chat-->
<!--                                    </b-dropdown-item>-->
<!--                                    <b-dropdown-item>-->
<!--                                        Report-->
<!--                                    </b-dropdown-item>-->
<!--                                </b-dropdown>-->
<!--                            </div>-->
                        </div>
                    </header>
                </div>

                <!-- User Chat Area -->
                <vue-perfect-scrollbar
                    ref="refChatLogPS"
                    :settings="perfectScrollbarSettings"
                    class="user-chats scroll-area"
                >
                    <chat-log
                        :chat-data="activeChat"
                        :profile-user-avatar="profileUserDataMinimal.avatar"
                    />
                </vue-perfect-scrollbar>

                <!-- Message Input -->
                <b-form
                    class="chat-app-form"
                    @submit.prevent="sendMessage"
                >
                    <b-input-group class="input-group-merge form-send-message mr-1">
                        <b-form-input
                            v-model="chatInputMessage"
                            placeholder="Введите команду (без /)"
                        />
                    </b-input-group>
                    <b-button v-if="chatsContacts.find(chat => chat.checked)"
                        variant="primary"
                        class="mr-1"
                        @click="sendToSelected"
                    >
                        Выбранные
                    </b-button>
                    <b-button
                        variant="primary"
                        type="submit"
                    >
                        Отправить
                    </b-button>
                </b-form>
            </div>
        </section>

        <!-- Active Chat Contact Details Sidebar -->
        <chat-active-chat-content-details-sidedbar
            :shall-show-active-chat-contact-sidebar.sync="shallShowActiveChatContactSidebar"
            :contact="activeChat.contact || {}"
        />

        <!-- Sidebar -->
        <portal to="content-renderer-sidebar-left">
            <chat-left-sidebar
                :chats-contacts="chatsContacts"
                :contacts="contacts"
                :active-chat-contact-id="activeChat.contact ? activeChat.contact.id : null"
                :shall-show-user-profile-sidebar.sync="shallShowUserProfileSidebar"
                :profile-user-data="profileUserData"
                :profile-user-minimal-data="profileUserDataMinimal"
                :mq-shall-show-left-sidebar.sync="mqShallShowLeftSidebar"
                @show-user-profile="showUserProfileSidebar"
                @open-chat="openChatOfContact"
                @all-pick="pickAll"
                @clear="clear"
            />
        </portal>
    </div>
</template>

<script>
import store from '@/store'
import {
    ref, onUnmounted, nextTick, onMounted,
} from '@vue/composition-api'
import {
    BAvatar, BDropdown, BDropdownItem, BForm, BInputGroup, BFormInput, BButton,
} from 'bootstrap-vue'
import VuePerfectScrollbar from 'vue-perfect-scrollbar'
// import { formatDate } from '@core/utils/filter'
import {$themeBreakpoints} from '@themeConfig'
import {useResponsiveAppLeftSidebarVisibility} from '@core/comp-functions/ui/app'
import ChatLeftSidebar from './ChatLeftSidebar.vue'
import chatStoreModule from './chatStoreModule'
import ChatActiveChatContentDetailsSidedbar from './ChatActiveChatContentDetailsSidedbar.vue'
import ChatLog from './ChatLog.vue'
import useChat from './useChat'
import {mapGetters} from "vuex";
import api from "@/api";
import moment from "moment";

export default {
    components: {

        // BSV
        BAvatar,
        BDropdown,
        BDropdownItem,
        BForm,
        BInputGroup,
        BFormInput,
        BButton,

        // 3rd Party
        VuePerfectScrollbar,

        // SFC
        ChatLeftSidebar,
        ChatActiveChatContentDetailsSidedbar,
        ChatLog,
    },
    computed: {
        ...mapGetters(["servers"]),
    },
    data() {
        return {
            shallShowUserProfileSidebar: false,
            shallShowActiveChatContactSidebar: false,
            mqShallShowLeftSidebar: false,

            activeChat: {},
            chatInputMessage: '',

            perfectScrollbarSettings: {
                maxScrollbarLength: 150,
            },

            chatsContacts: [],
            contacts: [],

            profileUserData: {},
            profileUserDataMinimal: {
                avatar: ''
            },
        }
    },
    methods:{
        clear(){
            this.chatsContacts.forEach(chat => {
                chat.checked = false;
            })
        },
        pickAll(){
            this.chatsContacts.forEach(chat => {
                chat.checked = !chat.checked;
            })
        },
        showUserProfileSidebar(){
            this.shallShowUserProfileSidebar = true;
        },
        loadServers(){
            this.servers.forEach(server => {
                this.chatsContacts.push({
                    id: server.id,

                    checked: false,

                    avatar: '',
                    status: 'online',
                    fullName: server.name,
                    about: '',

                    contact: {
                        id: server.id,
                        fullName: server.name,
                        avatar: '',
                    },

                    chat: {
                        chat: [],
                        unseenMsgs: 0,
                        lastMessage: {
                            message: '',
                            time: 0
                        }
                    }
                });
            });
        },
        startConversation() {
            if (store.state.app.windowWidth < $themeBreakpoints.lg) {
                this.mqShallShowLeftSidebar = true
            }
        },
        openChatOfContact(chatId){
            var server = this.servers.find(s => s.id === chatId);
            this.chatInputMessage = '';

            this.activeChat = this.chatsContacts.find(chat => chat.id === chatId);

            this.activeChat.chat.unseenMsgs = 0;

            this.mqShallShowLeftSidebar = false;
            this.scrollToBottomInChatLog();
        },
        scrollToBottomInChatLog(){
            this.activeChat.chat.unseenMsgs = 0;
        },
        sendMessage(){
            this.sendSelected([this.activeChat.id], this.chatInputMessage);
            this.chatInputMessage = '';
        },
        sendToSelected(){
            this.sendSelected(this.chatsContacts.filter(chat => chat.checked).map(chat => chat.id), this.chatInputMessage);
            this.chatInputMessage = '';
        },
        sendSelected: function(servers, cmd){
            servers.forEach(serverId => {
                var chat = this.chatsContacts.find(chat => chat.id === serverId);

                chat.chat.chat.push({
                    senderId: 0,
                    message: this.chatInputMessage,
                    time: moment().unix()
                });
            })

            api.post('rcon/send', {servers: servers, cmd: cmd})
            .then(response => {
                for(const serverid in response.data.messages) {
                    var chat = this.chatsContacts.find(chat => chat.id === parseInt(serverid));
                    chat.chat.unseenMsgs++;
                    chat.chat.lastMessage.time = moment().unix();
                    chat.chat.lastMessage.message = response.data.messages[serverid];

                    chat.chat.chat.push({
                        senderId: parseInt(serverid),
                        message: response.data.messages[serverid],
                        time: moment().unix()
                    });
                }

                this.scrollToBottomInChatLog();
            });
        }
    },
    mounted() {
        this.loadServers();
    }
    // beforeMount() {
    //     // Scroll to Bottom ChatLog
    //     const refChatLogPS = ref(null)
    //     const scrollToBottomInChatLog = () => {
    //         const scrollEl = refChatLogPS.value.$el || refChatLogPS.value
    //         scrollEl.scrollTop = scrollEl.scrollHeight
    //     }
    //
    //
    //
    //     this.loadServers(chatsContacts);
    //
    //     // ------------------------------------------------
    //     // Single Chat
    //     // ------------------------------------------------
    //     const activeChat = ref({})
    //     const chatInputMessage = ref('')
    //     const openChatOfContact = userId => {
    //         // Reset send message input value
    //         chatInputMessage.value = ''
    //
    //         store.dispatch('app-chat/getChat', {userId})
    //             .then(response => {
    //                 activeChat.value = response.data
    //
    //                 // Set unseenMsgs to 0
    //                 const contact = chatsContacts.value.find(c => c.id === userId)
    //                 if (contact) contact.chat.unseenMsgs = 0
    //
    //                 // Scroll to bottom
    //                 nextTick(() => {
    //                     scrollToBottomInChatLog()
    //                 })
    //             })
    //
    //         // if SM device =>  Close Chat & Contacts left sidebar
    //         // eslint-disable-next-line no-use-before-define
    //         mqShallShowLeftSidebar.value = false
    //     }
    //     const sendMessage = () => {
    //         if (!chatInputMessage.value) return
    //         const payload = {
    //             contactId: activeChat.value.contact.id,
    //             // eslint-disable-next-line no-use-before-define
    //             senderId: profileUserDataMinimal.value.id,
    //             message: chatInputMessage.value,
    //         }
    //         store.dispatch('app-chat/sendMessage', payload)
    //             .then(response => {
    //                 const {newMessageData, chat} = response.data
    //
    //                 // ? If it's not undefined => New chat is created (Contact is not in list of chats)
    //                 if (chat !== undefined) {
    //                     activeChat.value = {chat, contact: activeChat.value.contact}
    //                     chatsContacts.value.push({
    //                         ...activeChat.value.contact,
    //                         chat: {
    //                             id: chat.id,
    //                             lastMessage: newMessageData,
    //                             unseenMsgs: 0,
    //                         },
    //                     })
    //                 } else {
    //                     // Add message to log
    //                     activeChat.value.chat.chat.push(newMessageData)
    //                 }
    //
    //                 // Reset send message input value
    //                 chatInputMessage.value = ''
    //
    //                 // Set Last Message for active contact
    //                 const contact = chatsContacts.value.find(c => c.id === activeChat.value.contact.id)
    //                 contact.chat.lastMessage = newMessageData
    //
    //                 // Scroll to bottom
    //                 nextTick(() => {
    //                     scrollToBottomInChatLog()
    //                 })
    //             })
    //     }
    //
    //     // User Profile Sidebar
    //     // ? Will contain all details of profile user (e.g. settings, about etc.)
    //     const profileUserData = ref({})
    //     // ? Will contain id, name and avatar & status
    //     const profileUserDataMinimal = ref({})
    //
    //
    //     // Active Chat Contact Details
    //     const shallShowActiveChatContactSidebar = ref(false)
    //
    //     // UI + SM Devices
    //     // Left Sidebar Responsiveness
    //
    //     //TODO check this shit
    //     //const {mqShallShowLeftSidebar} = useResponsiveAppLeftSidebarVisibility()
    // }
}
</script>

<style lang="scss">
@import "~@core/scss/base/pages/app-chat.scss";
@import "~@core/scss/base/pages/app-chat-list.scss";
</style>
