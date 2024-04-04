document.addEventListener('DOMContentLoaded', () => {
    const userId = document.body.getAttribute('data-user-id');
    const addFriendButtons = document.querySelectorAll('.add-friend-btn');
    const notificationIcon = document.getElementById('notification-icon');
    const notificationDropdown = document.getElementById('notification-dropdown');
    const notificationCount = document.getElementById('notification-count');
    const notificationsUrl = new URL('http://localhost:3000/.well-known/mercure');
    notificationsUrl.searchParams.append('topic', `https://mondomaine.com/user/${userId}/notifications`);

    // Fonction pour basculer l'affichage du menu déroulant de notification
    function toggleNotificationDropdown() {
        notificationDropdown.classList.toggle('hidden');
    }

    // Gestion de l'ouverture/fermeture du menu de notifications
    notificationIcon.addEventListener('click', (event) => {
        event.stopPropagation();
        toggleNotificationDropdown();
    });

    // Ferme le menu déroulant si un clic est effectué en dehors de celui-ci
    window.addEventListener('click', (event) => {
        if (!notificationDropdown.contains(event.target) && !notificationDropdown.classList.contains('hidden')) {
            toggleNotificationDropdown();
        }
    });

    // Écouteur d'événements SSE pour les notifications en temps réel
    const eventSource = new EventSource(notificationsUrl);
    eventSource.onmessage = (event) => {
        const eventData = JSON.parse(event.data);
        const { message, userId, username } = eventData.data;
        addNotification(message, userId, username);
    };

    // Ajout d'une notification à l'interface utilisateur
    function addNotification(message, userId, username) {
        const notificationItem = document.createElement('div');
        notificationItem.classList.add('notification-item', 'px-4', 'py-2', 'hover:bg-gray-100', 'flex', 'items-center');
        notificationItem.innerHTML = `
            <div>
                <p class="font-semibold">${username}</p>
                <p>${message}</p>
            </div>
        `;
        notificationItem.addEventListener('click', function () {
            window.location.href = `/profil/${userId}`;
        });
        notificationDropdown.insertBefore(notificationItem, notificationDropdown.firstChild); // Insère la notification en haut de la liste
        updateNotificationsCount(true);
    }

    // Charge les notifications existantes au chargement de la page
    function loadNotifications() {
        fetch('/api/notifications')
            .then((response) => response.json())
            .then((data) => {
                data.forEach((notification) => {
                    const { message, userId, username } = notification;
                    addNotification(message, userId, username);
                });
            })
            .catch((error) => console.error('Error:', error));
    }

    // Mise à jour du compteur de notifications
    function updateNotificationsCount(increment = false) {
        if (increment) {
            const currentCount = parseInt(notificationCount.textContent, 10) || 0;
            notificationCount.textContent = currentCount + 1;
        } else {
            fetch('/api/notifications/count')
                .then((response) => response.json())
                .then(({ unreadCount }) => {
                    notificationCount.textContent = unreadCount;
                })
                .catch((error) => console.error('Error:', error));
        }
    }

    // Gestion de l'ajout d'amis
    addFriendButtons.forEach(button => {
        button.addEventListener('click', () => {
            const friendId = button.dataset.id;
            addFriend(friendId);
        });
    });

    function addFriend(friendId) {
        fetch(`/add-friend/${friendId}`, { method: 'POST' })
            .then(response => response.json())
            .then(data => {
                updateUIAfterFriendRequest(friendId, data.message, true);
            })
            .catch(error => console.error('Error:', error));
    }

    // Gestion des actions sur les demandes d'amitié
    document.body.addEventListener('click', (event) => {
        if (event.target.matches('.accept-friend-request') || event.target.matches('.refuse-friend-request')) {
            const action = event.target.matches('.accept-friend-request') ? 'accept' : 'refuse';
            const friendshipId = event.target.dataset.friendshipId;
            handleFriendRequest(friendshipId, action);
        }
    });

    function handleFriendRequest(friendshipId, action) {
        fetch(`/${action}-friend/${friendshipId}`, { method: 'POST' })
            .then((response) => response.json())
            .then((data) => {
                updateUIAfterFriendRequest(friendshipId, data.message, false);
            })
            .catch((error) => console.error('Error:', error));
    }

    // Mise à jour de l'UI après gestion de demande d'amitié
    function updateUIAfterFriendRequest(friendshipId, message, isAddingFriend) {
        const messageElement = document.createElement('div');
        messageElement.classList.add('notification-response', 'p-2', 'rounded', 'bg-green-100', 'text-green-800', 'mt-2');
        messageElement.textContent = message;

        if (isAddingFriend) {
            const addButton = document.querySelector(`.add-friend-btn[data-id="${friendshipId}"]`);
            if (addButton) {
                addButton.replaceWith(messageElement);
            }
        } else {
            const actionButtons = document.querySelectorAll(`[data-friendship-id="${friendshipId}"]`);
            actionButtons.forEach(button => button.replaceWith(messageElement.cloneNode(true)));
        }
    }

    loadNotifications(); // Initie le chargement des notifications au démarrage
});
