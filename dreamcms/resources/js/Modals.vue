<template>
    <div>
        <login-modal></login-modal>
        <register-modal></register-modal>
        <balance-modal></balance-modal>
        <exchange-modal></exchange-modal>
        <ban-modal></ban-modal>
        <two-f-a-modal></two-f-a-modal>
        <special-offer-modal></special-offer-modal>
    </div>
</template>

<script>
    import LoginModal from "./modals/LoginModal";
    import RegisterModal from "./modals/RegisterModal";
    import BalanceModal from "./modals/BalanceModal";
    import ExchangeModal from "./modals/ExchangeModal";
    import BanModal from "./modals/BanModal";
    import TwoFAModal from "./modals/TwoFAModal";
    import SpecialOfferModal from "./modals/SpecialOfferModal";

    export default {
        name: "Modals",
        components: {SpecialOfferModal, BanModal, ExchangeModal, BalanceModal, LoginModal, RegisterModal, TwoFAModal}
    }
</script>