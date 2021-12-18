<template>
    <component
        :is="tag"
        v-on="$listeners"
    >
        <!--    <b-avatar-->
        <!--      size="42"-->
        <!--      :src="user.avatar"-->
        <!--      :badge="isChatContact"-->
        <!--      class="badge-minimal"-->
        <!--      :badge-variant="resolveAvatarBadgeVariant(user.status)"-->
        <!--      variant="transparent"-->
        <!--    />-->
        <feather-icon
            icon="ServerIcon"
            size="42"
        />
        <div class="chat-info flex-grow-1">
            <h5 class="mb-0">
                {{ user.fullName }}
            </h5>
            <p class="card-text text-truncate">
                <span v-html="user.chat.lastMessage.message"></span>
            </p>
        </div>
        <div
            v-if="isChatContact"
            class="chat-meta text-nowrap"
        >
            <!--      <small class="float-right mb-25 chat-time"> {{ user.chat.lastMessage.time ? formatDateToMonthShort(user.chat.lastMessage.time, { hour: 'numeric', minute: 'numeric' }) : '' }}</small>-->

            <b-badge
                v-if="user.chat.unseenMsgs"
                pill
                variant="primary"
                class="float-right"
            >
                {{ user.chat.unseenMsgs }}
            </b-badge>

            <b-form-checkbox v-model="user.checked" class="mr-2"/>
        </div>
    </component>
</template>

<script>
import {BAvatar, BBadge, BFormCheckbox} from 'bootstrap-vue'
import {formatDateToMonthShort} from '@core/utils/filter'
import useChat from './useChat'

export default {
    components: {
        BAvatar,
        BBadge,
        BFormCheckbox
    },
    props: {
        tag: {
            type: String,
            default: 'div',
        },
        user: {
            type: Object,
            required: true,
        },
        isChatContact: {
            type: Boolean,
            dedfault: false,
        },
    },
    setup() {
        const {resolveAvatarBadgeVariant} = useChat()
        return {formatDateToMonthShort, resolveAvatarBadgeVariant}
    },
}
</script>

<style>

</style>
