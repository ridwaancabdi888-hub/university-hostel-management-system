export function photoUploadDisclosure(initialOpen = false) {
    return {
        open: Boolean(initialOpen),

        toggle() {
            this.open = !this.open;
        },

        close() {
            this.open = false;
        },
    };
}
