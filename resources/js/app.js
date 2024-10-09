new Vue({
    el: '#app',
    data: {
        messages: [],
        newMessage: ''
    },
    mounted() {
        this.listenForMessages();
    },
    methods: {
        listenForMessages() {
            window.Echo.private('chat.' + userId)
                .listen('MessageSent', (e) => {
                    this.messages.push({
                        message: e.message.message,
                        sender_id: e.message.sender_id,
                        receiver_id: e.message.receiver_id,
                        created_at: e.message.created_at
                    });
                });
        },
        sendMessage() {
            axios.post('/api/messages', {
                receiver_id: this.receiverId,
                message: this.newMessage
            }).then(response => {
                this.newMessage = '';
            });
        }
    }
});
