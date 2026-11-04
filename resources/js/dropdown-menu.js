export function dropdownMenu() {
    return {
        open: false,

        toggle() {
            this.open = !this.open;
        },

        close() {
            this.open = false;
        },

        closeAndRestoreFocus() {
            const trigger = this.$refs?.trigger;

            this.close();
            this.$nextTick(() => {
                if (trigger?.isConnected !== false && typeof trigger?.focus === 'function') {
                    trigger.focus();
                }
            });
        },
    };
}
