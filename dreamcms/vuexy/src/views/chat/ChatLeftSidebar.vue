<template>
    <div class="sidebar-left">
        <div class="sidebar">

            <!-- Logged In User Profile Sidebar -->
            <user-profile-sidebar
                :shall-show-user-profile-sidebar="shallShowUserProfileSidebar"
                :profile-user-data="profileUserData"
                @close-sidebar="$emit('update:shall-show-user-profile-sidebar', false)"
            />

            <!-- Sidebar Content -->
            <div
                class="sidebar-content card"
                :class="{'show': mqShallShowLeftSidebar}"
            >

                <!-- Sidebar close icon -->
                <span class="sidebar-close-icon">
          <feather-icon
              icon="XIcon"
              size="16"
              @click="$emit('update:mq-shall-show-left-sidebar', false)"
          />
        </span>

                <!-- Header -->
                <div class="chat-fixed-search">
                    <div class="d-flex align-items-center w-100">
                        <b-input-group class="input-group-merge ml-1 w-100 round">
                            <b-input-group-prepend is-text>
                                <feather-icon
                                    icon="SearchIcon"
                                    class="text-muted"
                                />
                            </b-input-group-prepend>
                            <b-form-input
                                v-model="searchQuery"
                                placeholder="Поиск"
                            />
                        </b-input-group>
                    </div>
                </div>

                <!-- ScrollArea: Chat & Contacts -->
                <vue-perfect-scrollbar
                    :settings="perfectScrollbarSettings"
                    class="chat-user-list-wrapper list-group scroll-area"
                >

                    <!-- Chats Title -->
                    <div class="chat-list-title mb-2">
                        <b-button
                            variant="primary"
                            @click="$emit('clear')"
                        >
                            Убрать все
                        </b-button>

                        <b-button
                            variant="primary"
                            @click="$emit('all-pick')"
                            class="float-right"
                        >
                            Выбрать все
                        </b-button>
                    </div>

                    <!-- Chats -->
                    <ul class="chat-users-list chat-list media-list">
                        <chat-contact
                            v-for="contact in filteredChatsContacts"
                            :key="contact.id"
                            :user="contact"
                            tag="li"
                            :class="{'active': activeChatContactId === contact.id}"
                            is-chat-contact
                            @click="$emit('open-chat', contact.id)"
                        />
                    </ul>

                    <!-- Contacts Title -->
                    <!--          <h4 class="chat-list-title">-->
                    <!--            Консоли-->
                    <!--          </h4>-->

                    <!--          &lt;!&ndash; Contacts &ndash;&gt;-->
                    <!--          <ul class="chat-users-list contact-list media-list">-->
                    <!--            <chat-contact-->
                    <!--              v-for="contact in filteredContacts"-->
                    <!--              :key="contact.id"-->
                    <!--              :user="contact"-->
                    <!--              tag="li"-->
                    <!--              @click="$emit('open-chat', contact.id)"-->
                    <!--            />-->
                    <!--          </ul>-->
                </vue-perfect-scrollbar>
            </div>

        </div>
    </div>
</template>

<script>
import {
    BAvatar, BInputGroup, BInputGroupPrepend, BFormInput, BButton
} from 'bootstrap-vue'
import VuePerfectScrollbar from 'vue-perfect-scrollbar'
import {ref, computed} from '@vue/composition-api'
import ChatContact from './ChatContact.vue'
import UserProfileSidebar from './UserProfileSidebar.vue'

export default {
    components: {

        // BSV
        BAvatar,
        BInputGroup,
        BInputGroupPrepend,
        BFormInput,
        BButton,

        // 3rd party
        VuePerfectScrollbar,

        // SFC
        ChatContact,
        UserProfileSidebar,
    },
    props: {
        chatsContacts: {
            type: Array,
            required: true,
        },
        contacts: {
            type: Array,
            required: true,
        },
        shallShowUserProfileSidebar: {
            type: Boolean,
            required: true,
        },
        profileUserData: {
            type: Object,
            required: true,
        },
        profileUserMinimalData: {
            type: Object,
            required: true,
        },
        activeChatContactId: {
            type: Number,
            default: null,
        },
        mqShallShowLeftSidebar: {
            type: Boolean,
            required: true,
        },
    },
    setup(props) {
        const perfectScrollbarSettings = {
            maxScrollbarLength: 150,
        }

        const resolveChatContact = userId => props.contacts.find(contact => contact.id === userId)

        // Search Query
        const searchQuery = ref('')

        const searchFilterFunction = contact => contact.fullName.toLowerCase().includes(searchQuery.value.toLowerCase())
        const filteredChatsContacts = computed(() => props.chatsContacts.filter(searchFilterFunction))
        const filteredContacts = computed(() => props.contacts.filter(searchFilterFunction))

        return {
            // Search Query
            searchQuery,
            filteredChatsContacts,
            filteredContacts,

            // UI
            resolveChatContact,
            perfectScrollbarSettings,
        }
    },
}
</script>
