<template>
    <div class="chats">
        <div
            v-for="(msgGrp, index) in formattedChatData.formattedChatLog"
            :key="msgGrp.senderId+String(index)"
            class="chat"
            :class="{'chat-left': msgGrp.senderId === formattedChatData.contact.id}"
        >
            <div class="chat-avatar">
                <b-avatar v-if="msgGrp.senderId !== formattedChatData.contact.id"
                          size="36"
                          class="avatar-border-2 box-shadow-1"
                          variant="transparent"
                          :src="profileUserAvatar"
                />
                <feather-icon v-else
                              icon="ServerIcon"
                              size="36"
                />
            </div>
            <div class="chat-body">
                <div
                    v-for="msgData in msgGrp.messages"
                    :key="msgData.time"
                    class="chat-content"
                >
                    <p v-html="msgData.msg"></p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {computed} from '@vue/composition-api'
import {BAvatar} from 'bootstrap-vue'

export default {
    components: {
        BAvatar,
    },
    props: {
        chatData: {
            type: Object,
            required: true,
        },
        profileUserAvatar: {
            type: String,
            required: true,
        },
    },
    setup(props) {
        const formattedChatData = computed(() => {
            const contact = {
                id: props.chatData.contact.id,
                avatar: props.chatData.contact.avatar,
            }

            let chatLog = []
            if (props.chatData.chat) {
                chatLog = props.chatData.chat.chat
            }

            const formattedChatLog = []
            let chatMessageSenderId = chatLog[0] ? chatLog[0].senderId : undefined
            let msgGroup = {
                sender: chatMessageSenderId,
                messages: [],
            }

            chatLog.forEach((msg, index) => {
                if (chatMessageSenderId === msg.senderId) {
                    msgGroup.messages.push({
                        msg: msg.message,
                        time: msg.time,
                    })
                } else {
                    chatMessageSenderId = msg.senderId
                    formattedChatLog.push(msgGroup)
                    msgGroup = {
                        senderId: msg.senderId,
                        messages: [{
                            msg: msg.message,
                            time: msg.time,
                        }],
                    }
                }
                if (index === chatLog.length - 1) formattedChatLog.push(msgGroup)
            })

            return {
                formattedChatLog,
                contact,
                profileUserAvatar: props.profileUserAvatar,
            }
        })

        return {
            formattedChatData,
        }
    },
}
</script>

<style>

</style>
