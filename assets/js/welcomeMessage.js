document.addEventListener('alpine:init', () => {
    Alpine.data('welcomeMessage', () => ({
        show: true,
        init() {
            setTimeout(() => {
                this.show = false;
            }, 3000);
        },
    }));
});

document.addEventListener('alpine:init', () => {
    Alpine.store('modals', {
        openLoginModal: false,
        openRegisterModal: false,
    });
});